<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Goal;
use App\Entity\Result;
use App\Entity\Indicator;
use App\Entity\Iteration;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Goal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goal[]    findAll()
 * @method Goal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Goal::class);
    }
    
    public function getValue($entity, ?Iteration $iteration = null)
    {
        if($entity instanceof Indicator){
            $qb = $this->createQueryBuilder('goa');
            $qb->select("SUM(goa.value) as value");
            
            if( $iteration ) {
                $qb->andWhere('goa.iteration = :iteration');
                $qb->setParameter('iteration', $iteration);
            }
            
            $qb->andWhere('goa.indicator = :indicator');
            $qb->setParameter('indicator', $entity);
            
            $value = $qb->getQuery()->getSingleScalarResult();
            return is_null($value)?0:$value;
        }
        
        // Somme des objectifs de chaque indicateur de l'activité
        if($entity instanceof Activity){
            $qb = $this->createQueryBuilder('goa');
            $qb->select("SUM(goa.value) as value");
            $qb->leftJoin('goa.indicator', 'ind');
            $qb->andWhere('ind.activity = :activity');
            $qb->setParameter('activity', $entity);
            $value = $qb->getQuery()->getSingleScalarResult();
            return is_null($value)?0:$value;
        }
        
        // Somme des objectifs de chaque indicateur du projet
        if($entity instanceof Project){
            $qb = $this->createQueryBuilder('goa');
            $qb->select("SUM(goa.value) as value");
            $qb->leftJoin('goa.indicator', 'ind');
            $qb->leftJoin('ind.activity', 'act');
            $qb->andWhere('act.project = :project');
            $qb->setParameter('project', $entity);
            $value = $qb->getQuery()->getSingleScalarResult();
            return is_null($value)?0:$value;
        }
        
        return 0;
    }
}
