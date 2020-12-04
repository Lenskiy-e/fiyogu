<?php

namespace App\Tests\Controllers\Functional;

use App\Controller\ProfileController;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProfileControllerTest extends KernelTestCase
{
    /**
     * @var ProfileController $profileController
     */
    private ProfileController $profileController;

    /**
     * @var UserRepository|\Doctrine\Persistence\ObjectRepository $userRepository
     */
    private UserRepository $userRepository;

    /** @var EntityManagerInterface $entityManager */
    private EntityManagerInterface $entityManager;

    /** @var ProfileRepository $profileRepository */
    private ProfileRepository $profileRepository;

    protected function setUp()
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        /** @var LoggerInterface $loggerInterface */
        $loggerInterface = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $this->userRepository = $this->entityManager->getRepository(User::class);

        $this->profileRepository = $this->entityManager->getRepository(Profile::class);

        $this->profileController = new ProfileController($loggerInterface, $this->entityManager);

        $this->profileController->setContainer(self::$kernel->getContainer());
    }

    public function testUpdateReturnJson()
    {
        $profile = $this->getUser()->getProfile();
        $oldProfile = clone $profile;
        $surname = "New {$profile->getSurname()}";
        $phone = "{$profile->getPhone()}00";

        $request = new Request([],[],[],[],[],[],json_encode([
            'phone'     => $phone,
            'surname'   => $surname,
            'mentor'    => !$profile->isMentor()
        ]));

        $this->profileController->update($profile, $request);

        $this->assertEquals($surname, $profile->getSurname(), "Surname doesn't correct");
        $this->assertEquals($phone, $profile->getPhone(), "Phone doesn't correct");
        $this->assertEquals(!$oldProfile->isMentor(), $profile->isMentor(), "Mentor doesn't correct");

        $this->returnState($oldProfile);
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
