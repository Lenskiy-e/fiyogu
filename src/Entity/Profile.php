<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="100")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $surname;

    /**
     * @ORM\Column (type="string", length=30, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column (type="boolean")
     */
    private $mentor;

    public function __construct()
    {
        $this->mentor = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return false
     */
    public function isMentor() : bool
    {
        return $this->mentor;
    }

    /**
     * @param false $mentor
     */
    public function setMentor(bool $mentor): void
    {
        $this->mentor = $mentor;
    }


}
