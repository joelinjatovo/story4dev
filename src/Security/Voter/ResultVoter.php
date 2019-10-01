<?php

namespace App\Security\Voter;

use App\Entity\Result;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;


class ResultVoter extends Voter
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

        // only vote on Result objects inside this voter
        if (!$subject instanceof Result) {
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

        // you know $subject is a Result object, thanks to supports
        /** @var Result $result */
        $result = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($result, $user);
            case self::EDIT:
                return $this->canEdit($result, $user);
            case self::LIST:
                return $this->canList($result, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Result $result, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($result, $user)) {
            return true;
        }

        // the Project object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return false; //!$result->isPrivate();
    }

    private function canEdit(Result $result, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $result->getAuthor();
    }

    private function canList(Result $result, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $result->getAuthor();
    }
}