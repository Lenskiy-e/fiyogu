<?php

namespace App\Tests\Functional;

use App\Entity\Skill;
use App\Entity\User;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SkillControllerTest extends WebTestCase
{
    /** @var EntityManagerInterface$entityManager */
    private EntityManagerInterface $entityManager;
    
    /** @var SkillRepository $skillRepository  */
    private SkillRepository $skillRepository;
    /**
     * @var User
     */
    private $user;
    
    protected function setUp()
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->skillRepository = $this->entityManager->getRepository(Skill::class);
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->user = $userRepository->findOneBy(['active' => true]);
        self::ensureKernelShutdown();
    }
    
    protected function tearDown() : void
    {
        $this->removeSkill();
    }
    
    public function testAddReturnSuccessStatus()
    {
        $client = static::createClient();
    
        $client->request('POST', "/skill/add",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'name' => 'skillByPHPUnit'
            ])
        );
        
        $this->assertEquals(201,$client->getResponse()->getStatusCode());
    }
    
    public function testAddReturnSuccessJson()
    {
        $client = static::createClient();
    
        $client->request('POST', "/skill/add",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'name' => 'skillByPHPUnit'
            ])
        );
        $response = $client->getResponse()->getContent();
        
        $this->assertJson($response);
        $this->assertContains(json_encode(['result'=>'success']), $response);
        $this->removeSkill();
    }
    
    public function testAddReturnBadRequestOnDuplicate()
    {
        $originalClient = static::createClient();
        self::ensureKernelShutdown();
        $duplicateClient = static::createClient();
    
        $originalClient->request('POST', "/skill/add",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'name' => 'skillByPHPUnit'
            ])
        );
    
        $duplicateClient->request('POST', "/skill/add",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'name' => 'skillByPHPUnit'
            ])
        );
        
        $this->assertEquals(201, $originalClient->getResponse()->getStatusCode());
        $this->assertEquals(400, $duplicateClient->getResponse()->getStatusCode());
        
    }
    
    public function testAddReturnErrorMessageOnDuplicate()
    {
        $originalClient = static::createClient();
        self::ensureKernelShutdown();
        $duplicateClient = static::createClient();
    
        $originalClient->request('POST', "/skill/add",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'name' => 'skillByPHPUnit'
            ])
        );
    
        $duplicateClient->request('POST', "/skill/add",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ],
            json_encode([
                'name' => 'skillByPHPUnit'
            ])
        );
        $this->assertNotContains('User already has this skill', $originalClient->getResponse()->getContent());
        $this->assertContains('User already has this skill', $duplicateClient->getResponse()->getContent());
    }
    
    public function testGetSkillUsersReturnSuccessStatus()
    {
        $client = static::createClient();
        $skill = $this->getSkill();
    
        $client->request('GET', "/skill/{$skill->getId()}/users",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ]
        );
        
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    
    public function testGetSkillUsersContainsUsers()
    {
        $client = static::createClient();
        $skill = $this->getSkill();
    
        $client->request('GET', "/skill/{$skill->getId()}/users",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ]
        );
        
        $this->assertContains('users', $client->getResponse()->getContent());
    }
    
    public function testGetSkillUsersCountOfReturnedUsers()
    {
        $client = static::createClient();
        $skill = $this->getSkill();
    
        $client->request('GET', "/skill/{$skill->getId()}/users",[],[],
            [
                'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
            ]
        );
        $response = json_decode($client->getResponse()->getContent(),true);
        
        if(isset($response['users'])) {
            self::ensureKernelShutdown();
            $count = count($response['users']);
            $newCount = $count - rand(1, $count - 1);
            $newClient = static::createClient();
            $newClient->request('GET', "/skill/{$skill->getId()}/users",[],[],
                [
                    'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
                ],
                json_encode([
                    'limit' => $newCount
                ])
            );
            $newResponse = json_decode($newClient->getResponse()->getContent(),true);
            
            $this->assertCount($newCount, $newResponse['users']);
        }
    }
    
    private function removeSkill()
    {
        $skill = $this->skillRepository->findOneBy([
            'name' => 'skillByPHPUnit'
        ]);
        if($skill) {
            $this->entityManager->remove($skill);
            $this->entityManager->flush();
        }
    }
    
    private function getSkill() : Skill
    {
        return $this->skillRepository->findOneBy([
            'valid' => true
        ]);
    }
}