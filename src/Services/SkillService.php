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
 * Class SkillService
 * @package App\Services
 */
class SkillService
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
     * @var Cache
     */
    private Cache $cache;
    
    /**
     * SkillService constructor.
     * @param UserRepository $userRepository
     * @param SkillRepository $skillRepository
     * @param EntityManagerInterface $entityManager
     * @param Cache $cache
     */
    public function __construct
    (
        UserRepository $userRepository,
        SkillRepository $skillRepository,
        EntityManagerInterface $entityManager,
        Cache $cache
    )
    {
        $this->userRepository = $userRepository;
        $this->skillRepository = $skillRepository;
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }
    
    /**
     * @param array $request
     * @return Skill
     */
    public function create(array $request) : Skill
    {
        if(!$request['name']) {
            throw new BadRequestException('Please, provide the name',400);
        }

        $skillName = strtolower($request['name']);

        if( $skill = $this->skillRepository->findOneBy(['name' => $skillName]) ) {
            return $skill;
        }
    
        $skill = new Skill();
        $skill->setName($skillName);
    
        $this->entityManager->persist($skill);
        $this->entityManager->flush();
    
        return $skill;
    }
    
    public function addSkillToUser(User $user, Skill $skill)
    {
        $userSkills = $user->getSkills();
    
        if($userSkills->contains($skill)) {
            throw new BadRequestException('User already has this skill',400);
        }
    
        $userSkills->add($skill);
        $this->entityManager->flush();
        $this->cache->addSkillUser($skill->getId(), $user->getId());
    }
    
    public function edit(array $request, Skill $skill) : void
    {
        $skill->setName( $request['name'] ?? $skill->getName() );
        $skill->setValid( $request['valid'] ?? $skill->isValid() );
        
        $this->entityManager->persist($skill);
        $this->entityManager->flush();
    }
    
    /**
     * @param int $skill_id
     * @param bool $mentor
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getSkillUsers(int $skill_id, bool $mentor, int $limit, int $offset) : array
    {
        $users = [];
        $list = $this->cache->getSkillUsers($skill_id);
    
        if($offset > 0) {
            $offset *= $limit;
        }
        
        foreach ($list as $user) {
            $user = $this->cache->getUserPublicInfo($user);
            if($user['mentor'] == $mentor) {
                $users[] = $user;
            }
        }
        return array_splice($users, $offset, $limit);
    }
}