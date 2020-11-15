<?php
declare(strict_types=1);
namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SkillRepository::class)
 */
class Skill
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private string $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private bool $valid;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="skills")
     * @ORM\JoinTable(name="user_skill")
     */
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->valid = false;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return ArrayCollection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     */
    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }
}
