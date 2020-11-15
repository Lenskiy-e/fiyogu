<?php

namespace App\Controller;

use App\DTO\DTOException;
use App\DTO\User\GetUserFullPublicInfoDTO;
use App\Entity\Profile;
use App\Entity\User;
use App\Event\UserCreateEvent;
use App\Form\CreateProfileType;
use App\Form\CreateUserType;
use App\Repository\UserRepository;
use App\Services\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route ("/user")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $repository;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * UserController constructor.
     * @param UserRepository $repository
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $manager
     */
    public function __construct
    (
        UserRepository $repository,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        EntityManagerInterface $manager
    )
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @param EventDispatcherInterface $dispatcher
     * @param FormErrors $formErrorsService
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route ("/create", name="user_create", methods={"POST"})
     */
    public function create
    (
        Request $request,
        EventDispatcherInterface $dispatcher,
        FormErrors $formErrorsService
    )
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
     * @param User $user
     * @return Response
     * @Route ("/{id}", name="user_get_public_info", methods={"get"})
     */
    public function getUserPublicInfo(User $user): Response
    {
        try {
            return $this->json( (new GetUserFullPublicInfoDTO($user))->toArray() );
        }catch (DTOException $e) {
            $this->logger->error($e->getTraceAsString());

            $message = $e->getMessage();

            if($e->getCode() === 477) {
                $message = $e->getArrayErrors();
            }

            return $this->json([
                'errors' => $message
            ],$e->getCode());
        }
    }
}
