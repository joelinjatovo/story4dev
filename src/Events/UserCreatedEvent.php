<?php

namespace App\Events;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The user.created event is dispatched each time an user is created
 * in the system.
 */
class UserCreatedEvent extends Event
{
    public const NAME = 'user.created';

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
