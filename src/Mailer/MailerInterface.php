<?php

namespace App\Mailer;

use App\Entity\User;

/**
 * Interface MailerInterface
 * @package App\Mailer
 */
interface MailerInterface
{
    public function send(User $user) : void;
}