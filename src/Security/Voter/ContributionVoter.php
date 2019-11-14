<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\ProjectContribution;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ContributionVoter extends Voter
{
    const ACCEPT   = 'accept';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::ACCEPT])) {
            return false;
        }

        // only vote on Project objects inside this voter
        if (!$subject instanceof ProjectContribution) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::ACCEPT:
                return $this->canAccept($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canAccept(ProjectContribution $contribution, User $user)
    {
        return $contribution->getUser() && ( $user->getId() == $contribution->getUser()->getId() );
    }
}