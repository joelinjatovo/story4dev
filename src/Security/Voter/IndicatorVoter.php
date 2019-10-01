<?php

namespace App\Security\Voter;

use App\Entity\Indicator;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;


class IndicatorVoter extends Voter
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

        // only vote on Indicator objects inside this voter
        if (!$subject instanceof Indicator) {
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

        // you know $subject is a Indicator object, thanks to supports
        /** @var Indicator $indicator */
        $indicator = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($indicator, $user);
            case self::EDIT:
                return $this->canEdit($indicator, $user);
            case self::LIST:
                return $this->canList($indicator, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Indicator $indicator, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($indicator, $user)) {
            return true;
        }

        // the Project object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return false; //!$indicator->isPrivate();
    }

    private function canEdit(Indicator $indicator, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $indicator->getAuthor();
    }

    private function canList(Indicator $indicator, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $indicator->getAuthor();
    }
}