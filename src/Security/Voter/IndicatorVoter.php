<?php

namespace App\Security\Voter;

use App\Entity\Indicator;
use App\Entity\User;
use App\Entity\ProjectContribution;
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
            case self::REMOVE:
                return $this->canRemove($indicator, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Indicator $indicator, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($indicator, $user)) {
            return true;
        }
        
        $activity = $indicator->getActivity();
        if( ! $activity ){
            return false;
        }
        
        $project = $activity->getProject();
        if( ! $project ){
            return false;
        }
        
        $contribution = $this->em
            ->getRepository(ProjectContribution::class)
            ->findOneBy(['project' => $project, 'user' => $user]);
        
        if($contribution){
            return true;
        }

        // the Project object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return false; //!$indicator->isPrivate();
    }

    private function canEdit(Indicator $indicator, User $user)
    {
        if( $user === $indicator->getAuthor() ) {
            return true;
        }
        
        $activity = $indicator->getActivity();
        if( ! $activity ){
            return false;
        }
        
        $project = $activity->getProject();
        if( ! $project ){
            return false;
        }
        
        $contribution = $this->em
            ->getRepository(ProjectContribution::class)
            ->findOneBy(['project' => $project, 'user' => $user]);
        
        if($contribution){
            return $contribution->isAdmin();
        }
        
        return false;
    }

    private function canRemove(Indicator $indicator, User $user)
    {
        return false;
    }
}