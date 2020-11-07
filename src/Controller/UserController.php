<?php

namespace App\Controller;

use App\DTO\DTOException;
use App\DTO\User\RequestToCreateUserDTOTransformer;
use App\DTO\User\CreateUserDTO;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller
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
     * UserController constructor.
     * @param UserRepository $repository
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     */
    public function __construct(UserRepository $repository, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @Route ("/register", name="user_register_page", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerPage()
    {
        return $this->render('pages/register.html.twig',[
            'title' => 'Registration'
        ]);
    }

    /**
     * @param Request $request
     * @param RequestToCreateUserDTOTransformer $transformer
     * @Route ("/register", name="user_register", methods={"POST"})
     */
    public function register(Request $request, RequestToCreateUserDTOTransformer $transformer, EntityManagerInterface $manager)
    {
        try {
            /** @var CreateUserDTO $dto */
            $dto = $transformer->transform( json_decode( $request->getContent(),true ) );
            $user = new User();
            $profile = new Profile();


            $user->setEmail( $dto->getEmail() );
            $user->setPassword( $dto->getPassword() );
            $user->setUsername( $dto->getUsername() );

            $profile->setUser($user);
            $profile->setName( $dto->getName() );

            $manager->persist($user);
            $manager->persist($profile);
            $manager->flush();

            return $this->json([
                'errors' => ''
            ],200);
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
}
