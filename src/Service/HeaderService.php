<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Project;
use App\Entity\User;

class HeaderService
{
    private $em;
    private $user;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();
    }
    
    public function getProjects(?User $user = null)
    {
        if( $user ){
            return $this->em->getRepository(Project::class)->findByAuthor($user)->execute();
        }
        
        if( $this->user ){
            return $this->em->getRepository(Project::class)->findByAuthor($this->user)->execute();
        }
        
        return null;
    }
    
    public function getMyProjects(?User $user = null)
    {
        if( $user ){
            return $this->em->getRepository(Project::class)->findByContributor($user)->execute();
        }
        
        if( $this->user ){
            return $this->em->getRepository(Project::class)->findByContributor($this->user)->execute();
        }
        
        return null;
    }
}
