<?php

namespace App\Controller;

use App\DTO\DTOException;
use App\DTO\User\RequestToCreateUserDTOTransformer;
use App\DTO\User\CreateUserDTO;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @param RequestToCreateUserDTOTransformer $transformer
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenGenerator $generator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route ("/create", name="user_create", methods={"POST"})
     */
    public function create
    (
        Request $request,
        RequestToCreateUserDTOTransformer $transformer,
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $generator
    )
    {
        try {
            /** @var CreateUserDTO $dto */
            $dto = $transformer->transform( json_decode( $request->getContent(),true ) );
            $user = new User();
            $profile = new Profile();


            $user->setEmail( $dto->getEmail() );
            $user->setPassword( $encoder->encodePassword($user, $dto->getPassword() ) );
            $user->setUsername( $dto->getUsername() );
            $user->setActivationToken( $generator->generateToken(50) );


            $profile->setUser($user);
            $profile->setName( $dto->getName() );

            $this->manager->persist($user);
            $this->manager->persist($profile);
            $this->manager->flush();

            return $this->json([
                'result' => 'success'
            ],201);
        }catch (DTOException $e) {
            $this->logger->error($e->getTraceAsString());

            $message = $e->getMessage();

            if($e->getCode() === 477) {
                $message = $e->getArrayErrors();
            }

            return $this->json([
                'errors' => $message
            ],400);
        }
    }

    /**
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/activate/{token}",name="user_activate", methods={"get"})
     */
    public function activate(string $token)
    {
        try {
            $user = $this->repository->findForActivation($token);

            $user->setActivationToken('');
            $user->setActive(true);

            $this->manager->persist($user);
            $this->manager->flush();

            return $this->json([
                'result' => 'success'
            ],200);

        } catch (NonUniqueResultException $e) {
            $this->logger->error('Activation token duplicate!');
            $this->logger->error($e->getMessage());
            return $this->json([
                'errors' => [
                    "Server error, please, try again or write to out administrator: {$this->getParameter('admin_email')}"
                ]
            ],500);
        } catch (EntityNotFoundException $e) {
            $this->logger->error("Activate user by token {$token}");
            $this->logger->error($e->getMessage());
            return $this->json([
                'errors' => ["User not found. You can support our administrator: {$this->getParameter('admin_email')}"]
            ],404);
        }
    }
}
