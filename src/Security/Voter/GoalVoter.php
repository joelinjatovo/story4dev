<?php

namespace App\Security\Voter;

use App\Entity\Goal;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;


class GoalVoter extends Voter
{
    const VIEW   = 'view';
    const EDIT   = 'edit';
    const REMOVE = 'remove';

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
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::REMOVE])) {
            return false;
        }

        // only vote on Goal objects inside this voter
        if (!$subject instanceof Goal) {
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

        // you know $subject is a Goal object, thanks to supports
        /** @var Goal $goal */
        $goal = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($goal, $user);
            case self::EDIT:
                return $this->canEdit($goal, $user);
            case self::LIST:
                return $this->canList($goal, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Goal $goal, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($goal, $user)) {
            return true;
        }

        // the Project object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return false; //!$goal->isPrivate();
    }

    private function canEdit(Goal $goal, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $goal->getAuthor();
    }

    private function canList(Goal $goal, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $goal->getAuthor();
    }
}