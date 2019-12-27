<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Result;
use App\Entity\Indicator;
use App\Entity\Iteration;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Result|null find($id, $lockMode = null, $lockVersion = null)
 * @method Result|null findOneBy(array $criteria, array $orderBy = null)
 * @method Result[]    findAll()
 * @method Result[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Result::class);
    }
    
    public function getValue($entity, ?Iteration $iteration = null)
    {
        if($entity instanceof Indicator){
            $qb = $this->createQueryBuilder('res');
            $qb->select("SUM(res.value) as value");
            
            if( $iteration ) {
                $qb->leftJoin('res.report', 'rep');
                $qb->leftJoin('rep.activity', 'act');
                $qb->leftJoin(Iteration::class, 'ite', 'WITH', 'ite.project = act.project');
                
                $qb->andWhere('res.indicator = :indicator');
                $qb->andWhere('DATE(rep.createdAt) >= DATE(ite.startAt)');
                $qb->andWhere('DATE(rep.createdAt) <= DATE(ite.endAt)');
                $qb->andWhere('ite = :iteration');
                $qb->setParameter('iteration', $iteration);
            }
            
            $qb->andWhere('res.indicator = :indicator');
            $qb->setParameter('indicator', $entity);
            
            $value = $qb->getQuery()->getSingleScalarResult();
            return is_null($value)?0:$value;
        }
        
        // Somme des résultats de tous les indicateurs de l'activité
        if($entity instanceof Activity){
            $qb = $this->createQueryBuilder('res');
            $qb->select("SUM(res.value) as value");
            $qb->leftJoin('res.report', 'rep');
            $qb->andWhere('rep.activity = :activity');
            $qb->setParameter('activity', $entity);
            $value = $qb->getQuery()->getSingleScalarResult();
            return is_null($value)?0:$value;
        }
        
        // Somme des résultats de tous les indicateurs du projet
        if($entity instanceof Project){
            $qb = $this->createQueryBuilder('res');
            $qb->select("SUM(res.value) as value");
            $qb->leftJoin('res.report', 'rep');
            $qb->leftJoin('rep.activity', 'act');
            $qb->andWhere('act.project = :project');
            $qb->setParameter('project', $entity);
            $value = $qb->getQuery()->getSingleScalarResult();
            return is_null($value)?0:$value;
        }
        
        return 0;
    }
}
