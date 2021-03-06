<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Event\UserCreateEvent;
use App\Form\CreateProfileType;
use App\Form\CreateUserType;
use App\Repository\UserRepository;
use App\Services\FormErrors;
use App\Services\LoginUser;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecurityController
 * @package App\Controller
 * @Route("/auth")
 */
class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    /**
     * @Route("/login", name="user_login", methods={"POST"})
     * @param Request $request
     * @param LoginUser $loginUser
     * @return Response
     */
    public function login(Request $request, LoginUser $loginUser): Response
    {
        $token = $loginUser->login( json_decode($request->getContent(),true) );
        return $this->json([
            'status'    => 'success',
            'token'     => $token
        ],200);
    }

    /**
     * @Route("/register", name="user_register", methods={"POST"})
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @param FormErrors $formErrorsService
     * @return JsonResponse
     */
    public function register(
        Request $request,
        EventDispatcherInterface $dispatcher,
        FormErrors $formErrorsService
    ): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $user = new User();
        $profile = new Profile();

        $userForm = $this->createForm(CreateUserType::class, $user);
        $profileForm = $this->createForm(CreateProfileType::class, $profile);
        $userForm->submit($data);
        $profileForm->submit($data);

        if( $errors = $formErrorsService->getFormErrors($profileForm, $userForm) ) {
            return $this->json([
                'error' => $errors
            ], 400);
        }

        $profile->setUser($user);
        $user->setProfile($profile);

        $this->manager->persist($user);
        $this->manager->persist($profile);
        $this->manager->flush();

        $userCreateEvent = new UserCreateEvent($user);
        $dispatcher->dispatch($userCreateEvent, $userCreateEvent::NAME);

        return $this->json([
            'result' => 'success'
        ], 201);

    }

    /**
     * @param string $token
     * @param UserRepository $repository
     * @return JsonResponse
     * @Route("/activate/{token}",name="user_activate", methods={"get"})
     */
    public function activate(string $token, UserRepository $repository): JsonResponse
    {
        $user = $repository->findForActivation($token);
    
        if(!$user) {
            return $this->json([
                'error'    => 'User not found or already activate'
            ],404);
        }
    
        $user->setActivationToken(null);
        $user->setActive(true);
    
        $this->manager->persist($user);
        $this->manager->flush();
    
        return $this->json([
            'result' => 'success'
        ],200);
    }
}
