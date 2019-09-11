<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Report;
use App\Entity\Project;
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
    
    public function findByActivity(Activity $activity)
    {
        return $this->createQueryBuilder('r')
            ->where('r.activity = :activity')
            ->setParameter('activity', $activity)
            ->getQuery();
    }
    
    public function findByProject(Project $project)
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.activity', 'a')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->getQuery();
    }
}
