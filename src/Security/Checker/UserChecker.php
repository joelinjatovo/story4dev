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

        if ( $user->isDeleted() ) {
            throw new AccountDeletedException($translator->trans("Ce compte a été supprimé."));
        }

        if ( $user->getConfirmToken() ) {
            throw new EmailNotConfirmedException($translator->trans("Votre adresse email n'a pas été confirmé. Veuillez vérifier votre boîte email!"));
        }

        if ( $user->isBlocked() ) {
            throw new AccountBlockedException($translator->trans("Ce compte a été désactivé par l'administrateur"));
        }

        if ( $user->isPinged() ) {
            throw new AccountPingedException($translator->trans("Ce compte n'a pas encore été validé par l'administrateur"));
        }

        if ( $user->isCanceled() ) {
            throw new AccountCanceledException($translator->trans("Votre inscription a été réfusé."));
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}