<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Activity::class);
    }
    
    public function getAllDeleted()
    {
        return $this->createQueryBuilder('p')
            ->where("p.deletedAt IS NOT NULL")
            ->getQuery();
    }
    
    public function findByProject(Project $project, $orderBy = 'createdAt', $order = 'ASC', $limit = 0)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->orderBy('a.'.$orderBy, $order);
        
        if($limit > 0){
            $queryBuilder->setMaxResults($limit);
        }
        
        return $queryBuilder->getQuery();
    }
}
