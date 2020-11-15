<?php
declare(strict_types=1);
namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UserCreateEvent
 * @package App\Event
 */
class UserCreateEvent extends Event
{
    /**
     *
     */
    const NAME = 'user.register';
    /**
     * @var User
     */
    private User $user;

    /**
     * UserCreateEvent constructor.
     * @param User $user
     * @param string $name
     */
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