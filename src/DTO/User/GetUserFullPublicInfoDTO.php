<?php
declare(strict_types=1);
namespace App\DTO\User;

use App\DTO\DTOInterface;
use App\Entity\Skill;
use App\Entity\User;
use App\Services\DTOHelpers;
use Doctrine\Common\Collections\Collection;

/**
 * Class GetUserDTO
 * @package App\DTO\User
 */
final class GetUserFullPublicInfoDTO implements DTOInterface
{
    /**
     * @var string
     */
    private string $email;
    /**
     * @var string
     */
    private string $username;
    /**
     * @var \DateTime
     */
    private \DateTime $created_at;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $surname;

    /**
     * @var string|null
     */
    private ?string $phone;

    /**
     * @var bool
     */
    private bool $mentor;

    /**
     * @var Collection
     */
    private Collection $skills;

    /**
     * GetUserDTO constructor.
     * @param $user
     * @throws \App\DTO\DTOException
     */
    public function __construct(User $user)
    {
        DTOHelpers::support(User::class, $user);
        $profile = $user->getProfile();

        $this->email        = $user->getEmail();
        $this->username     = $user->getUsername();
        $this->created_at   = $user->getCreatedAt();
        $this->name         = $profile->getName();
        $this->surname      = $profile->getSurname();
        $this->phone        = $profile->getPhone();
        $this->mentor       = $profile->isMentor();
        $this->skills       = $user->getSkills();
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at->format("d.m.Y");
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return bool
     */
    public function isMentor(): bool
    {
        return $this->mentor;
    }

    /**
     * @return array
     */
    public function getSkills(): array
    {
        $skills = [];
        foreach ($this->skills as $item) {
            if($item->isValid()) {
                $skills[$item->getId()] = $item->getName();
            }
        }
        return $skills;
    }


    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'created_at' => $this->getCreatedAt(),
            'name' => $this->getName(),
            'surname' => $this->getSurname(),
            'phone' => $this->getPhone(),
            'mentor' => $this->isMentor(),
            'skills' => $this->getSkills()
        ];
    }
}