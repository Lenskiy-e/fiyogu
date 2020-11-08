<?php
declare(strict_types=1);

namespace App\DTO\Profile;

use App\DTO\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UpdateProfileDTO
 * @package App\DTO\Profile
 */
class UpdateProfileDTO implements DTOInterface
{
    /**
     * @var string|null
     * @Assert\Length(max="100", min="2")
     */
    private ?string $name;

    /**
     * @var string|null
     * @Assert\Length(max="100")
     */
    private ?string $surname;

    /**
     * @var string|null
     * @Assert\Length(max="30")
     */
    private ?string $phone;
    /**
     * @var bool|null
     */
    private ?bool $mentor;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     */
    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return bool|null
     */
    public function getMentor(): ?bool
    {
        return $this->mentor;
    }

    /**
     * @param bool|null $mentor
     */
    public function setMentor(?bool $mentor): void
    {
        $this->mentor = $mentor;
    }

}