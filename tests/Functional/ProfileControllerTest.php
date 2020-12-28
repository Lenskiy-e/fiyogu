<?php

namespace App\Tests\Functional;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var ProfileRepository
     */
    private ProfileRepository $profileRepository;
    /**
     * @var User
     */
    private User $user;

    protected function setUp()
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->profileRepository = $this->entityManager->getRepository(Profile::class);
        $this->user = $userRepository->findOneBy(['active' => true]);
        self::ensureKernelShutdown();
    }

    public function testUpdateReturnSuccessStatus()
    {
        $profile = $this->user->getProfile();
        $client = static::createClient();

        $client->request('PATCH', "/profile/{$profile->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdateReturnJson()
    {
        $profile = $this->user->getProfile();
        $client = static::createClient();

        $client->request('PATCH', "/profile/{$profile->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdateReturnNotFound()
    {
        $client = static::createClient();

        $client->request('PATCH', "/profile/123123123123",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$this->user->getSessionToken()}"
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
