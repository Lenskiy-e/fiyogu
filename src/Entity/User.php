<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="This e-mail is already used")
 * @UniqueEntity("username", message="This username is already used")
 */
class User
{
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
     * @Assert\Length(min="8")
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(min="3", max="20")
     */
    private $username;

    /**
     * @Assert\DateTime(format="d.m.Y")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", mappedBy="user", cascade={"persist"})
     */
    private $profile;

    public function __construct()
    {
        $this->created_at = time();
    }

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
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }


}
