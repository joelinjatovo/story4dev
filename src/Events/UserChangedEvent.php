<?php

namespace App\Events;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The user.activated event is dispatched each time an user is activated
 * in the system.
 */
class UserChangedEvent extends Event
{
    public const ACTIVATED = 'user.activated';

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
