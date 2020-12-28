<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @var \App\Repository\UserRepository|\Doctrine\Persistence\ObjectRepository
     */
    private $userRepository;
    
    /**
     * @var User
     */
    private User $user;
    
    /**
     * @var User
     */
    private User $user_with_testimonials;

    protected function setUp()
    {
        self::bootKernel();
        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->user = $this->getUser();
        $this->user_with_testimonials = $this->getUser(true);
        self::ensureKernelShutdown();
    }

    public function testGetUserPublicInfoStatus()
    {
        $client = static::createClient();

        $client->request('GET', "/user/{$this->user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Test doesn\'t get 200 status');
    }

    public function testGetUserPublicInfoIsJson()
    {
        $client = static::createClient();

        $client->request('GET', "/user/{$this->user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $this->assertJson($client->getResponse()->getContent(), 'Response are not JSON');
    }

    public function testGetUserPublicInfoAuth()
    {
        $client = static::createClient();

        $client->request('GET', "/user/{$this->user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "bad token"
        ]);

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Test doesn\'t get 401 status');
    }

    public function testGetUserPublicInfoJsonContains()
    {
        $client = static::createClient();

        $client->request('GET', "/user/{$this->user->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $response = $client->getResponse()->getContent();

        $fields = [
            'email', 'username', 'created_at',
            'name', 'surname', 'phone', 'mentor',
            'skills'
        ];

        foreach ($fields as $field) {
            $this->assertContains($field, $response, "Response doesn't contains {$field} field");
        }

    }

    public function testGetUserPublicInfoReturnNotFound()
    {
        $client = static::createClient();

        $client->request('GET', "/user/01010101",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetTestimonialsStatus()
    {
        $client = static::createClient();

        $client->request('GET', "/user/{$this->user_with_testimonials->getId()}/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user_with_testimonials->getSessionToken()}"
        ]);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function testGetTestimonialsReturnNotFound()
    {
        $client = static::createClient();

        $client->request('GET', "/user/01010101/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetTestimonialsIsJson()
    {
        $client = static::createClient();

        $client->request('GET', "/user/{$this->user_with_testimonials->getId()}/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user_with_testimonials->getSessionToken()}"
        ]);

        $this->assertJson($client->getResponse()->getContent(), 'Response are not JSON');
    }

    public function testGetTestimonialsJsonContains()
    {
        $client = static::createClient();

        $client->request('GET', "/user/{$this->user_with_testimonials->getId()}/testimonials",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user_with_testimonials->getSessionToken()}"
        ]);

        $response = $client->getResponse()->getContent();
        $fields = ['result', 'user_from', 'from_name', 'text', 'rating'];

        foreach ($fields as $field) {
            $this->assertContains($field, $response, "Response doesn't contains {$field} field");
        }
    }

    private function getUser(bool $with_testimonials = false, int $offset = 0) : User
    {
        if($with_testimonials) {
            $users = $this->userRepository->getUsersWithTestimonials(1,1,$offset);
            if(!$users[0]->getSessionToken()) {
                return $this->getUser(true, ++$offset);
            }
            return $users[0];
        }
        return $this->userRepository->findOneBy([
            'active' => true,
        ]);
    }
}
