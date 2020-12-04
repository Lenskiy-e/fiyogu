<?php

namespace App\Tests\Controllers\Functional;

use App\Controller\UserController;
use App\Entity\Testimonials;
use App\Entity\User;
use App\Repository\TestimonialsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserControllerTest extends KernelTestCase
{
    private UserController $userController;
    private UserRepository $userRepository;
    private TestimonialsRepository $testimonialsRepository;

    protected function setUp()
    {
        /** @var ValidatorInterface $validatorInterface */
        $validatorInterface = $this->getMockBuilder(ValidatorInterface::class)
            ->getMock();

        /** @var LoggerInterface $loggerInterface */
        $loggerInterface = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $kernel = self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->userRepository = $entityManager->getRepository(User::class);

        $this->testimonialsRepository = $entityManager->getRepository(Testimonials::class);

        $this->userController = new UserController(
            $this->userRepository,
            $validatorInterface,
            $loggerInterface,
            $entityManager
        );

        $this->userController->setContainer($kernel->getContainer());
    }

    public function testGetUserPublicInfoIsJsonResponse()
    {
        $user = $this->getUser();
        $userInfo = $this->userController->getUserPublicInfo($user);

        $this->assertInstanceOf(Response::class, $userInfo, 'Function doesn\'t return json response!');
    }

    public function testGetUserPublicInfoHasFields()
    {
        $user = $this->getUser();
        $userInfoContent = $this->userController->getUserPublicInfo($user)->getContent();
        $profile = $user->getProfile();

        $this->assertContains($user->getEmail(), $userInfoContent, 'Response doesn\'t contains user Email!');
        $this->assertContains($user->getUsername(), $userInfoContent, 'Response doesn\'t contains user Username!');
        $this->assertContains($user->getCreatedAt()->format("d.m.Y"), $userInfoContent, 'Response doesn\'t contains user Created at date!');
        $this->assertContains($profile->getName(), $userInfoContent, 'Response doesn\'t contains user Name!');
        $this->assertContains($profile->getSurname(), $userInfoContent, 'Response doesn\'t contains user Surname!');
        $this->assertContains($profile->getPhone(), $userInfoContent, 'Response doesn\'t contains user Phone!');
        if($profile->isMentor()) {
            $this->assertContains('"mentor":true', $userInfoContent, 'Response doesn\'t contains that user is mentor!');
        }else{
            $this->assertContains('"mentor":false', $userInfoContent, 'Response doesn\'t contains that user is not mentor!');
        }
        $this->assertContains('"skills":{', $userInfoContent, 'Response doesn\'t contains user Skills!');
    }

    public function testGetUserPublicInfoHasNoField()
    {
        $user = $this->getUser();
        $userInfoContent = $this->userController->getUserPublicInfo($user)->getContent();

        $this->assertNotContains($user->getPassword(), $userInfoContent, 'Response contains a user Password!');
        $this->assertNotContains($user->getSessionToken(), $userInfoContent, 'Response contains a user Session Token!');
    }

    public function testGetTestimonialsIsJsonResponse()
    {
        $user = $this->getUser(true);
        $response = $this->getTestimonials($user);
        $this->assertInstanceOf(JsonResponse::class, $response,'Function doesn\'t return json response!');
    }

    public function testGetTestimonialsContainsAllTestimonials()
    {
        $user = $this->getUser(true);
        $response = $this->getTestimonials($user)->getContent();
        $user_testimonials = $this->testimonialsRepository->findBy([
            'user_to'   => $user,
            'verified'  => true
        ]);

        foreach ($user_testimonials as $testimonials) {
            $this->assertContains("{$testimonials->getUserFrom()->getId()}", $response, 'Response doesn\'t contains all testimonials!');
        }
    }

    public function testGetTestimonialsNotContainsUnverifiedTestimonials()
    {
        $user = $this->getUser(true);
        $response = $this->getTestimonials($user)->getContent();
        $user_testimonials = $this->testimonialsRepository->findBy([
            'user_to'   => $user,
            'verified'  => false
        ]);

        foreach ($user_testimonials as $testimonials) {
            $this->assertNotContains("{$testimonials->getUserFrom()->getId()}", $response, 'Response contains unverified testimonials!');
        }
    }

    private function getUser(bool $with_testimonials = false) : User
    {
        if($with_testimonials) {
            $users = $this->userRepository->getUsersWithTestimonials(1,1,0);
        }else{
            $users = $this->userRepository->findBy([
                'active' => true,
            ], null, 1);
        }

        return $users[0];
    }

    private function getTestimonials(User $user) {
        return $this->userController->getTestimonials(
            $user,
            $this->getMockBuilder(Request::class)->getMock(),
            $this->testimonialsRepository
        );
    }
}
