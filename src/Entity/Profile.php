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
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(max="100", min="2")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max="100", min="2")
     */
    private $surname;

    /**
     * @ORM\Column (type="string", length=30, nullable=true)
     * @Assert\Length(min="8", max="30")
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
    public function getMentor()
    {
        return $this->mentor;
    }

    /**
     * @param false $mentor
     */
    public function setMentor($mentor): void
    {
        $this->mentor = $mentor;
    }


}
