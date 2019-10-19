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
    
    public function findByActivity(Activity $activity, ?User $user = null)
    {
        if($user){
            return $this->createQueryBuilder('r')
                ->where('r.activity = :activity AND r.author = :user')
                ->setParameter('activity', $activity)
                ->setParameter('user', $user)
                ->orderBy('r.createdAt', 'DESC')
                ->getQuery();
        }
        return $this->createQueryBuilder('r')
            ->where('r.activity = :activity')
            ->setParameter('activity', $activity)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
    }
    
    public function findByProject(Project $project, ?User $user = null)
    {
        if($user){
            return $this->createQueryBuilder('r')
                ->leftJoin('r.activity', 'a')
                ->where('a.project = :project AND r.author = :user')
                ->setParameter('project', $project)
                ->setParameter('user', $user)
                ->orderBy('r.createdAt', 'DESC')
                ->getQuery();
        }
        
        return $this->createQueryBuilder('r')
            ->leftJoin('r.activity', 'a')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
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
