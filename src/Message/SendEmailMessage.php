<?php

namespace App\Message;

use App\Entity\User;

final class SendEmailMessage
{
    /**
     * @var User
     */
    private User $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    
}
