<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\TestimonialsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TestimonialsRepository::class)
 * @ORM\Table(name="testimonials", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_from_to", columns={"user_from_id", "user_to_id"})
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class Testimonials
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text;
    /**
     * @var int
     * @ORM\Column(type="integer", precision=2)
     * @Assert\Range(min="1", max="10")
     * @Assert\NotBlank()
     */
    private int $rating;
    /**
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="testimonials")
     */
    private UserInterface $user_to;
    /**
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_reviews")
     */
    private UserInterface $user_from;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $verified;

    public function __construct()
    {
        $this->verified = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return UserInterface
     */
    public function getUserTo(): UserInterface
    {
        return $this->user_to;
    }

    /**
     * @param UserInterface $user_to
     */
    public function setUserTo(UserInterface $user_to): void
    {
        $this->user_to = $user_to;
    }

    /**
     * @return UserInterface
     */
    public function getUserFrom(): UserInterface
    {
        return $this->user_from;
    }

    /**
     * @param UserInterface $user_from
     */
    public function setUserFrom(UserInterface $user_from): void
    {
        $this->user_from = $user_from;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     */
    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }
}
