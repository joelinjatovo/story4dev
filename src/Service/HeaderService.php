<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use InvalidArgumentException;
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
            return $this->em->getRepository(Project::class)->findByContributor($this->user);
        }
        return $this->em->getRepository(Project::class)->findAll();
    }
}
