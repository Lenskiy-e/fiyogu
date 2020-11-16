<?php

namespace App\Controller;

use App\DTO\DTOException;
use App\DTO\User\GetUserFullPublicInfoDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
