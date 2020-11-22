<?php

namespace App\EventSubscriber;

use App\Event\UserCreateEvent;
use App\Mailer\UserRegisteredMailer;
use App\Services\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var TokenGenerator
     */
    private TokenGenerator $generator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;
    /**
     * @var UserRegisteredMailer
     */
    private UserRegisteredMailer $mailer;

    /**
     * UserSubscriber constructor.
     * @param EntityManagerInterface $manager
     * @param TokenGenerator $generator
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRegisteredMailer $mailer
     */
    public function __construct(
        EntityManagerInterface $manager,
        TokenGenerator $generator,
        UserPasswordEncoderInterface $encoder,
        UserRegisteredMailer $mailer
    )
    {
        $this->manager = $manager;
        $this->generator = $generator;
        $this->encoder = $encoder;
        $this->mailer = $mailer;
    }

    public function onUserCreate(UserCreateEvent $event)
    {
        $user = $event->getUser();
        $password = $this->encoder->encodePassword($user,$user->getPassword());
        $token = $this->generator->generateToken(50);

        $user->setPassword($password);
        $user->setActivationToken($token);

        $this->manager->persist($user);
        $this->manager->flush();

        $this->mailer->send($user);

    }

    public static function getSubscribedEvents()
    {
        return [
            UserCreateEvent::NAME   => 'onUserCreate'
        ];
    }
}
