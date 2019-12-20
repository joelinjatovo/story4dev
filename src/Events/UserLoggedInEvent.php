<?php

namespace App\Events;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The user.loggedIn event is dispatched each time an user is logged in
 * in the system.
 */
class UserLoggedInEvent extends Event
{
    public const NAME = 'user.loggedIn';

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
