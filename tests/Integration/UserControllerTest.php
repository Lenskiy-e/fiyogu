<?php

namespace App\Tests\Integration;

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
    /**
     * @var UserController
     */
    private UserController $userController;
    /**
     * @var UserRepository|\Doctrine\Persistence\ObjectRepository
     */
    private UserRepository $userRepository;
    /**
     * @var TestimonialsRepository|\Doctrine\Persistence\ObjectRepository
     */
    private TestimonialsRepository $testimonialsRepository;
    /**
     * @var User
     */
    private User $user;
    /**
     * @var User
     */
    private User  $user_with_testimonials;

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
        
        $this->user = $this->getUser();
        $this->user_with_testimonials = $this->getUser(true);
    }

    public function testGetUserPublicInfoIsJsonResponse()
    {
        $userInfo = $this->userController->getUserPublicInfo($this->user);

        $this->assertInstanceOf(Response::class, $userInfo, 'Function doesn\'t return json response!');
    }

    public function testGetUserPublicInfoHasFields()
    {
        $userInfoContent = $this->userController->getUserPublicInfo($this->user)->getContent();
        $profile = $this->user->getProfile();

        $this->assertContains($this->user->getEmail(), $userInfoContent, 'Response doesn\'t contains user Email!');
        $this->assertContains($this->user->getUsername(), $userInfoContent, 'Response doesn\'t contains user Username!');
        $this->assertContains($this->user->getCreatedAt()->format("d.m.Y"), $userInfoContent, 'Response doesn\'t contains user Created at date!');
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
        $userInfoContent = $this->userController->getUserPublicInfo($this->user)->getContent();

        $this->assertNotContains($this->user->getPassword(), $userInfoContent, 'Response contains a user Password!');
        $this->assertNotContains($this->user->getSessionToken(), $userInfoContent, 'Response contains a user Session Token!');
    }

    public function testGetTestimonialsIsJsonResponse()
    {
        $response = $this->getTestimonials($this->user_with_testimonials);
        $this->assertInstanceOf(JsonResponse::class, $response,'Function doesn\'t return json response!');
    }

    public function testGetTestimonialsContainsAllTestimonials()
    {
        $response = $this->getTestimonials($this->user_with_testimonials)->getContent();
        $user_testimonials = $this->testimonialsRepository->findBy([
            'user_to'   => $this->user_with_testimonials,
            'verified'  => true
        ]);

        foreach ($user_testimonials as $testimonials) {
            $this->assertContains("{$testimonials->getUserFrom()->getId()}", $response, 'Response doesn\'t contains all testimonials!');
        }
    }

    public function testGetTestimonialsNotContainsUnverifiedTestimonials()
    {
        $response = $this->getTestimonials($this->user_with_testimonials)->getContent();
        $user_testimonials = $this->testimonialsRepository->findBy([
            'user_to'   => $this->user_with_testimonials,
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
            return $users[0];
        }
        
        return $this->userRepository->findOneBy([
            'active' => true,
        ]);
    }

    private function getTestimonials(User $user) {
        return $this->userController->getTestimonials(
            $user,
            $this->getMockBuilder(Request::class)->getMock(),
            $this->testimonialsRepository
        );
    }
}
