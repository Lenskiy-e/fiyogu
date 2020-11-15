<?php
declare(strict_types=1);
namespace App\Services;

use App\Entity\Skill;
use App\Entity\User;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class AddSkill
 * @package App\Services
 */
class AddSkill
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var SkillRepository
     */
    private SkillRepository $skillRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * AddSkill constructor.
     * @param UserRepository $userRepository
     * @param SkillRepository $skillRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct
    (
        UserRepository $userRepository,
        SkillRepository $skillRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $userRepository;
        $this->skillRepository = $skillRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $request
     * @param User $user
     */
    public function add(array $request, User $user) : void
    {
        if(!$request['name']) {
            throw new BadRequestException('Please, provide the name');
        }

        $skillName = strtolower($request['name']);

        if( ! $skill = $this->getSkill($skillName) ) {
            $skill = $this->createSkill($skillName);
        }

        $userSkills = $user->getSkills();

        if($userSkills->contains($skill)) {
            throw new BadRequestException('User already has this skill');
        }

        $userSkills->add($skill);
        $this->entityManager->flush();
    }

    /**
     * @param string $skillName
     * @return Skill|null
     */
    private function getSkill(string $skillName) : ?Skill
    {
        return $this->skillRepository->findOneBy(['name' => $skillName]);
    }

    /**
     * @param string $skillName
     * @return Skill
     */
    private function createSkill(string $skillName) : Skill
    {
        $skill = new Skill();
        $skill->setName($skillName);

        $this->entityManager->persist($skill);
        $this->entityManager->flush();

        return $skill;
    }
}