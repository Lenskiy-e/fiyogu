<?php

namespace App\Event;

use App\Entity\Skill;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserAddSkillEvent extends Event
{
    const NAME = 'skill.add';
    
    /**
     * @var Skill
     */
    private Skill $skill;
    /**
     * @var User
     */
    private User $user;
    
    public function __construct(Skill $skill, User $user)
    {
        $this->skill = $skill;
        $this->user = $user;
    }
    
    /**
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill;
    }
    
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}