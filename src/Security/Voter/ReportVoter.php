<?php

namespace App\Security\Voter;

use App\Entity\Report;
use App\Entity\User;
use App\Entity\ProjectContribution;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;


class ReportVoter extends Voter
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
        if (!$subject instanceof Report) {
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
        $report = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($report, $user);
            case self::EDIT:
                return $this->canEdit($report, $user);
            case self::REMOVE:
                return $this->canRemove($report, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Report $report, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($report, $user)) {
            return true;
        }

        // the Project object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return false; //!$report->isPrivate();
    }

    private function canEdit(Report $report, User $user)
    {
        if( $user === $report->getAuthor() ) {
            return true;
        }
        
        $activity = $report->getActivity();
        if( ! $activity ){
            return false;
        }
        
        $project = $activity->getProject();
        if( ! $project ){
            return false;
        }
        
        if($report->getAuthor()==$user){
            return true;
        }
        
        $contribution = $this->em
            ->getRepository(ProjectContribution::class)
            ->findOneBy(['project' => $project, 'user' => $user]);
        
        if($contribution){
            return $contribution->isAdmin();
        }
        
        return false;
    }

    private function canRemove(Report $report, User $user)
    {
        return false;
    }
}