<?php


namespace App\Services;


use App\DTO\DTOException;
use App\DTO\User\GetUserFullPublicInfoDTO;
use App\Entity\User;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Cache
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $cache;
    
    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }
    
    /**
     * @param int $id
     * @return array
     */
    public function getSkillUsers(int $id): array
    {
        try {
            return $this->cache->getItem("skill-{$id}-users")->get() ?? [];
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
    
    /**
     * @param CacheItemInterface $item
     * @param array $users
     */
    private function setSkillUsers(CacheItemInterface $item, array $users) : void
    {
        shuffle($users);
        $item->set($users);
        $this->cache->save($item);
    }
    
    /**
     * @param int $skill_id
     * @param int $user_id
     */
    public function addSkillUser(int $skill_id, int $user_id) : void
    {
        try {
            $users = $this->cache->getItem("skill-{$skill_id}-users");
            $newUsers[$user_id] = $user_id;
        
            if($users->isHit()) {
                $newUsers = array_merge($newUsers, $users->get());
            }
    
            $this->setSkillUsers($users,$newUsers);
        } catch (InvalidArgumentException $e) {
        }
    }
    
    /**
     * @param int $skill_id
     * @param int $user_id
     */
    public function removeSkillUser(int $skill_id, int $user_id) : void
    {
        try {
            $users = $this->cache->getItem("skill-{$skill_id}-users");
            
            $cachedUsers = $users->get();
            unset($cachedUsers[$user_id]);
            
            $this->setSkillUsers($users, $cachedUsers);
        } catch (InvalidArgumentException $e) {
        }
    }
    
    /**
     * @param int $id
     * @return array
     */
    public function getUserPublicInfo(int $id) : array
    {
        try {
            return $this->cache->getItem("user-{$id}-public")->get();
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
    
    /**
     * @param User $user
     * @throws DTOException
     * @throws InvalidArgumentException
     */
    public function setUserPublicInfo(User $user)
    {
        $key = "user-{$user->getId()}-public";
        $item = $this->cache->getItem($key);
    
        $item->set((new GetUserFullPublicInfoDTO($user))->toArray());
        $this->cache->save($item);
    }
    
    /**
     * @param User $user
     */
    public function deleteUserPublicInfo(User $user)
    {
        try {
            $this->cache->deleteItem("user-{$user->getId()}-public");
        } catch (InvalidArgumentException $e) {
            throw new BadRequestException('Bad data provided',400);
        }
    }
}