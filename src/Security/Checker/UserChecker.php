<?php

namespace App\Security\Checker;

use App\Entity\User;
use App\Exception\AccountDeletedException;
use App\Exception\AccountPingedException;
use App\Exception\AccountBlockedException;
use App\Exception\AccountCanceledException;
use App\Exception\EmailNotConfirmedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }
    
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
        
        $translator = $this->translator;

        if ( $user->getConfirmToken() ) {
            throw new EmailNotConfirmedException($translator->trans('Your email account is not yet confirmed. Please, check your inbox!'));
        }

        if ( $user->isBlocked() ) {
            throw new AccountBlockedException($translator->trans('This account is disabled by admin.'));
        }

        if ( $user->isPinged() ) {
            throw new AccountPingedException($translator->trans('This account is not yet validate by admin.'));
        }

        if ( $user->isCanceled() ) {
            throw new AccountCanceledException($translator->trans('Your subscription is canceled by admin.'));
        }

        // user is deleted, show a generic Account Not Found message.
        if ($user->isDeleted()) {
            throw new AccountDeletedException($translator->trans('This account has been deleted.'));
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}