<?php

namespace App\EventSubscriber;

use App\Entity\Profile;
use App\Entity\Skill;
use App\Entity\User;
use App\Services\Cache;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class UserPublicInfoSubscriber implements EventSubscriber
{
    
    /**
     * @var Cache
     */
    private Cache $cache;
    
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }
    
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if($entity instanceof User) {
            $this->cache->deleteUserPublicInfo($entity);
        }
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
    
        if($entity instanceof User) {
            $this->cache->setUserPublicInfo($entity);
        }
    
        if($entity instanceof Profile) {
            $this->cache->setUserPublicInfo( $entity->getUser() );
        }
    
        if($entity instanceof Skill) {
            foreach ($entity->getUsers()->toArray() as $user) {
                $this->cache->setUserPublicInfo($user);
            }
        }
    }
    
    public function getSubscribedEvents(): array
    {
        return [
          Events::postRemove,
          Events::postUpdate
        ];
    }
}