<?php

namespace App\EventSubscriber;

use App\Event\UserAddSkillEvent;
use App\Event\UserRemoveSkillEvent;
use App\Services\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SkillSubscriber implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private Cache $cache;
    
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }
    
    public function removeSkill(UserRemoveSkillEvent $event)
    {
        $this->cache->removeSkillUser($event->getSkill()->getId(),$event->getUser()->getId());
    }
    
    public function addSkill(UserAddSkillEvent $event)
    {
        $this->cache->addSkillUser($event->getSkill()->getId(),$event->getUser()->getId());
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            UserAddSkillEvent::NAME         => 'addSkill',
            UserRemoveSkillEvent::NAME      => 'removeSkill'
        ];
    }
}