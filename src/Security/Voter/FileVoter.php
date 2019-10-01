<?php

namespace App\Security\Voter;

use App\Entity\File;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;


class FileVoter extends Voter
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

        // only vote on Report objects inside this voter
        if (!$subject instanceof File) {
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

        // you know $subject is a Report object, thanks to supports
        /** @var Report $report */
        $file = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($file, $user);
            case self::EDIT:
                return $this->canEdit($file, $user);
            case self::REMOVE:
                return $this->canRemove($file, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(File $file, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($file, $user)) {
            return true;
        }

        // the Project object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return false; //!$report->isPrivate();
    }

    private function canEdit(File $file, User $user)
    {
        if( $user === $file->getAuthor() ) {
            return true;
        }
        
        return false;
    }

    private function canRemove(File $file, User $user)
    {
        return false;
    }
}