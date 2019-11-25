<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Report;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\ProjectContribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Report::class);
    }
    
    public function getAllDeleted()
    {
        return $this->createQueryBuilder('p')
            ->where("p.deletedAt IS NOT NULL")
            ->getQuery();
    }
    
    public function findByAuthor(User $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.author = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
    }
        
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.author = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
    }
    
    public function findByActivity(Activity $activity, ?User $user = null, $orderBy = 'createdAt', $order = 'ASC', $limit = 0)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->andWhere('r.activity = :activity')->setParameter('activity', $activity);
        $queryBuilder->orderBy('r.'.$orderBy, $order);
        
        if($user){
            $queryBuilder->andWhere('r.author = :user')->setParameter('user', $user);
        }
        
        if($limit > 0){
            $queryBuilder->setMaxResults($limit);
        }
        
        return $queryBuilder->getQuery();
    }
    
    public function findByProject(Project $project, ?User $user = null, $orderBy = 'createdAt', $order = 'ASC', $limit = 0)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->leftJoin('r.activity', 'a');
        $queryBuilder->andWhere('a.project = :project')->setParameter('project', $project);
        $queryBuilder->orderBy('r.'.$orderBy, $order);
        
        if($user){
            $queryBuilder->andWhere('r.author = :user')->setParameter('user', $user);
        }
        
        if($limit > 0){
            $queryBuilder->setMaxResults($limit);
        }
        
        return $queryBuilder->getQuery();
    }
    
    public function feedByProject(Project $project, ?User $user = null, $orderBy = 'createdAt', $order = 'ASC', $limit = 0)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->leftJoin('r.activity', 'a');
        $queryBuilder->andWhere('a.project = :project')->setParameter('project', $project);
        $queryBuilder->andWhere('r.publishExternally = :publish OR r.publishExternally IS NULL')->setParameter('publish', 1);
        //$queryBuilder->andWhere('r.status = :status')->setParameter('status', Report::STATUS_TERMINATED);
        $queryBuilder->orderBy('r.'.$orderBy, $order);
        
        if($user){
            $queryBuilder->andWhere('r.author = :user')->setParameter('user', $user);
        }
        
        if($limit > 0){
            $queryBuilder->setMaxResults($limit);
        }
        
        return $queryBuilder->getQuery();
    }
    
    public function findByContribution(Project $project, User $user)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.activity', 'a')
            ->leftJoin('a.project', 'p')
            ->where('a.project = :project AND r.author = :user')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
    }
}
