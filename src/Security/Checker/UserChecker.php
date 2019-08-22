<?php

namespace App\Security\Checker;

use App\Entity\User;
use App\Exception\AccountDeletedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        // user is deleted, show a generic Account Not Found message.
        if ($user->isDeleted() || true) {
            throw new AccountDeletedException('This account has been deleted.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}