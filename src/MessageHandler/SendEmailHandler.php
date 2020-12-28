<?php

namespace App\MessageHandler;

use App\Mailer\UserRegisteredMailer;
use App\Message\SendEmailMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendEmailHandler implements MessageHandlerInterface
{
    /**
     * @var UserRegisteredMailer
     */
    private UserRegisteredMailer $mailer;
    
    public function __construct(UserRegisteredMailer $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function __invoke(SendEmailMessage $message)
    {
        $this->mailer->send($message->getUser());
    }
}
