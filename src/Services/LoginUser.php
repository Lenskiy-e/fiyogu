<?php
declare(strict_types=1);
namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class LoginUser
 * @package App\Services
 */
class LoginUser
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var TokenGenerator
     */
    private TokenGenerator $tokenGenerator;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * LoginUser constructor.
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct
    (
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $credentials
     * @return string
     * @throws EntityNotFoundException
     */
    public function login(array $credentials) : string
    {
        if( !isset($credentials['email']) || !isset($credentials['password'])) {
            throw new BadRequestException('Please, provide email and password', 400);
        }

        $user = $this->userRepository->findOneBy(['email' => $credentials['email']]);

        if(!$user) {
            throw new EntityNotFoundException('User not found', 404);
        }

        if(!$user->isActive()) {
            throw new EntityNotFoundException('Please, activate your account', 404);
        }

        if ( !$this->validPassword($credentials['password'],$user) ) {
            throw new BadRequestException('Email or password is invalid', 400);
        }

        return $this->setSessionToken($user);
    }

    /**
     * @param string $password
     * @param User $user
     * @return bool
     */
    private function validPassword(string $password, User $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user,$password);
    }

    /**
     * @param User $user
     * @return string
     */
    private function setSessionToken(User $user): string
    {
        $token = $this->tokenGenerator->generateToken(50);

        $user->setSessionToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $token;
    }
}