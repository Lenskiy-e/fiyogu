<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?int $id;

    /**
     * @ORM\Column (type="string", length=255)
     * @Assert\Email()
     * @Assert\Length(min="6", max="255")
     */
    private $email;

    /**
     * @ORM\Column (type="text")
     * @Assert\Length(min="8")
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="20")
     */
    private $username;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $created_at;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", mappedBy="user", cascade={"persist", "remove"})
     */
    private Profile $profile;

    /**
     * @ORM\Column (type="simple_array")
     */
    private array $roles;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, unique=true)
     * @Assert\Length(min="50", max="50")
     */
    private $session_token;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, unique=true)
     * @Assert\Length(min="50", max="50")
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=100, nullable=true, unique=true)
     * @Assert\Length(min="100", max="100")
     */
    private $recovery_token;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, unique=true, nullable=true)
     */
    private string $security_token;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Skill", inversedBy="users")
     * @ORM\JoinTable(name="user_skill")
     */
    private Collection $skills;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Testimonials", mappedBy="user_from")
     */
    private Collection $user_reviews;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Testimonials", mappedBy="user_to")
     */
    private Collection $testimonials;

    /**
     * @var float
     * @ORM\Column(type="float", scale=2, precision=4)
     */
    private float $rating;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->roles = [self::ROLE_USER];
        $this->active = false;
        $this->skills = new ArrayCollection();
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
     * @return Profile
     */
    public function getProfile() : Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile): void
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

    /**
     * @return ArrayCollection
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    /**
     * @param Collection $skills
     */
    public function setSkills(Collection $skills): void
    {
        $this->skills = $skills;
    }

    /**
     * @return Collection
     */
    public function getUserReviews(): Collection
    {
        return $this->user_reviews;
    }

    /**
     * @param Collection $user_reviews
     */
    public function setUserReviews(Collection $user_reviews): void
    {
        $this->user_reviews = $user_reviews;
    }

    /**
     * @return Collection
     */
    public function getTestimonials(): Collection
    {
        return $this->testimonials;
    }

    /**
     * @param Collection $testimonials
     */
    public function setTestimonials(Collection $testimonials): void
    {
        $this->testimonials = $testimonials;
    }

    /**
     * @return float
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    public function getSalt() : string
    {
        return 'sr6fe9fh.f0!';
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
