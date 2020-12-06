<?php

namespace App\Tests\Functional;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private ProfileRepository $profileRepository;

    protected function setUp()
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->profileRepository = $this->entityManager->getRepository(Profile::class);

        self::ensureKernelShutdown();
    }

    public function testUpdateReturnSuccessStatus()
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        $client = static::createClient();

        $client->request('PATCH', "/profile/{$profile->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdateReturnJson()
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        $client = static::createClient();

        $client->request('PATCH', "/profile/{$profile->getId()}",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdateReturnNotFound()
    {
        $user = $this->getUser();
        $client = static::createClient();

        $client->request('PATCH', "/profile/123123123123",[],[],[
            'HTTP_X-AUTH-TOKEN' => "{$user->getSessionToken()}"
        ]);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }


    private function getUser() : User
    {
        $users = $this->userRepository->findBy([
            'active' => true,
        ], null, 1);

        return $users[0];
    }

    private function returnState(Profile $profile)
    {
        $newProfile = $this->profileRepository->find($profile->getId());

        $newProfile->setSurname( $profile->getSurname() );
        $newProfile->setMentor( $profile->isMentor() );
        $newProfile->setPhone( $profile->getPhone() );

        $this->entityManager->persist($newProfile);
        $this->entityManager->flush();
    }
}
