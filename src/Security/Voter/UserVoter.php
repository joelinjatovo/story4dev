<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\ProjectContribution;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;


class UserVoter extends Voter
{
    const LIST_REPORTS = 'list_reports';

    private $security;
    private $em;

    public function __construct(Security $security, EntityManager $em)
    {
        $this->security = $security;
        $this->em = $em;
    }
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::LIST_REPORTS])) {
            return false;
        }

        // only vote on Result objects inside this voter
        if (!$subject instanceof User) {
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
        
        $currentUser = $token->getUser();
        if (!$currentUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a User object, thanks to supports
        /** @var User $user */
        $user = $subject;

        switch ($attribute) {
            case self::LIST_REPORTS:
                return $this->canListReports($user, $currentUser);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canListReports(User $user, User $currentUser)
    {
        return $user == $currentUser;
    }
}