<?php


namespace App\Mailer;


use App\Entity\User;

class UserRegisteredMailer extends Mailer implements MailerInterface
{

    public function send(User $user): void
    {
        $message = (new \Swift_Message())
            ->setSubject('Welcome!')
            ->setFrom($this->from)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('email/user_registered_mail.html.twig',[
                    'user' => $user
                ]),
                'text/html'
            );
        $this->mailer->send($message);
    }
}