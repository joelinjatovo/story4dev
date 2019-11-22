<?php

namespace App\Security\Voter;

use App\Entity\Graph;
use App\Entity\User;
use App\Entity\Indicator;
use App\Entity\ProjectContribution;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class GraphVoter extends Voter
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

        // only vote on Graph objects inside this voter
        if (!$subject instanceof Graph) {
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

        // you know $subject is a Graph object, thanks to supports
        /** @var Graph $graph */
        $graph = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($graph, $user);
            case self::EDIT:
                return $this->canEdit($graph, $user);
            case self::REMOVE:
                return $this->canRemove($graph, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Graph $graph, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($graph, $user)) {
            return true;
        }
        
        $project = $graph->getProject();
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
        return false; //!$graph->isPrivate();
    }

    private function canEdit(Graph $graph, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        if( $user === $graph->getAuthor() ) {
            return true;
        }
        
        $project = $graph->getProject();
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

    private function canRemove(Graph $graph, User $user)
    {
        return $this->canEdit($graph, $user);
    }
}