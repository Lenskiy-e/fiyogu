<?php
declare(strict_types=1);

namespace App\DTO\User;

use App\DTO\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDTO implements DTOInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="40")
     */
    private string $email;
    /**
     * @var string
     * @Assert\Length(min="8")
     * @Assert\NotBlank()
     */
    private string $password;
    /**
     * @var string
     * @Assert\Length(min="3", max="20")
     */
    private string $username;

    /**
     * @var string
     * @Assert\Length(max="100", min="2")
     * @Assert\NotBlank()
     */
    private string $name;


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}