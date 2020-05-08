<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Project;
use App\Traits\PaginatorEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends AppRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }
    
    public function getAll()
    {
        return $this->createQueryBuilder('p')
            ->getQuery();
    }
    
    public function getAllDeleted()
    {
        return $this->createQueryBuilder('p')
            ->where("p.deletedAt IS NOT NULL")
            ->getQuery();
    }
    
    public function findByAuthor(User $user)
    {
        return $this->createQueryBuilder('p')
            ->where('p.author = :user')
            ->setParameter('user', $user)
            ->getQuery();
    }
    
    public function findByChild(Project $project)
    {
        return $this->createQueryBuilder('p')
            ->join('p.childs', 'p1')
            ->where('p1 = :project')
            ->setParameter('project', $project)
            ->getQuery();
    }
    
    public function findByParent(Project $project)
    {
        return $this->createQueryBuilder('p')
            ->join('p.parents', 'p1')
            ->where('p1 = :project')
            ->setParameter('project', $project)
            ->getQuery();
    }
    
    public function findByContributor(User $user, ?User $currentUser = null)
    {
        if($currentUser){
            return $this->createQueryBuilder('p')
                ->leftJoin('p.contributions', 'c1')
                ->leftJoin('p.contributions', 'c2')
                ->where('c1.user = :user AND c2.user = :current')
                ->setParameter('user', $user)
                ->setParameter('current', $currentUser)
                ->getQuery();
        }
        
        return $this->createQueryBuilder('p')
            ->leftJoin('p.contributions', 'c')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery();
    }
    
    public function findByIds(Array $ids)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery();
    }
}
