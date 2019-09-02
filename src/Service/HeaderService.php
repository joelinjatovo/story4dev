<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Project;

class HeaderService
{
    private $em;
    private $user;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();
    }
    
    public function getProjects()
    {
        if( $this->user ){
            return $this->em->getRepository(Project::class)->findByAuthor($this->user)->execute();
        }
        
        return null;
    }
    
    public function getContributions()
    {
        if( $this->user ){
            return $this->em->getRepository(Project::class)->findContributions($this->user)->execute();
        }
        
        return null;
    }
}
