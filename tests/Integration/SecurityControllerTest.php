<?php

namespace App\Tests\Integration;

use App\Controller\SecurityController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SecurityControllerTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var SecurityController
     */
    private SecurityController $securityController;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    protected function setUp()
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        /** @var LoggerInterface $loggerInterface */
        $loggerInterface = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $this->securityController = new SecurityController($this->entityManager, $loggerInterface);
        $this->userRepository = $this->entityManager->getRepository(User::class);

        $this->securityController->setContainer(self::$kernel->getContainer());
    }

    public function testActivateChangeData()
    {
        $user = $this->getUser(false);
        $token = $user->getActivationToken();

        $this->securityController->activate($token,$this->userRepository);

        $this->assertTrue($user->isActive());
        $this->assertEmpty($user->getActivationToken());

        $user->setActivationToken($token);
        $user->setActive(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function testBadTokenDontChangeData()
    {
        $user = $this->getUser(false);
        $token = $user->getActivationToken();

        $this->securityController->activate('nonExistingtoken',$this->userRepository);

        $this->assertFalse($user->isActive());
        $this->assertEquals($token,$user->getActivationToken());
    }

    private function getUser(bool $active = true) : User
    {
        $users = $this->userRepository->findBy([
            'active' => $active
        ],null,1);

        return $users[0];
    }
}