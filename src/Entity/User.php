<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="This e-mail is already used")
 * @UniqueEntity("username", message="This username is already used")
 */
class User implements UserInterface
{
    /**
     * @var string ROLE_USER
     */
    const ROLE_USER = 'ROLE_USER';
    /**
     * @var string ROLE_ADMIN
     */
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column (type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column (type="text")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $username;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", mappedBy="user", cascade={"persist"})
     */
    private $profile;

    /**
     * @ORM\Column (type="simple_array")
     */
    private array $roles;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(min="50", max="50")
     */
    private $session_token;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(min="50", max="50")
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(min="100", max="100")
     */
    private $recovery_token;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->roles = [self::ROLE_USER];
        $this->active = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile): void
    {
        $this->profile = $profile;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getActivationToken()
    {
        return $this->activation_token;
    }

    /**
     * @param mixed $activation_token
     */
    public function setActivationToken($activation_token): void
    {
        $this->activation_token = $activation_token;
    }

    /**
     * @return mixed
     */
    public function getRecoveryToken()
    {
        return $this->recovery_token;
    }

    /**
     * @param mixed $recovery_token
     */
    public function setRecoveryToken($recovery_token): void
    {
        $this->recovery_token = $recovery_token;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getSessionToken()
    {
        return $this->session_token;
    }

    /**
     * @param mixed $session_token
     */
    public function setSessionToken($session_token): void
    {
        $this->session_token = $session_token;
    }


    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
