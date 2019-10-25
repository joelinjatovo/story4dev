<?php

namespace App\EventListener\Entity;

class UserListener
{
    public function __construct()
    {
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
    }
}
