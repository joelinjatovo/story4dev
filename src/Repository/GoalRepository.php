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
    
    public function getValue($indicators, ?Iteration $iteration = null)
    {
        $qb = $this->createQueryBuilder('goa');
        $qb->select("SUM(goa.value) as value");
        $qb->andWhere('goa.indicator IN (:indicators)')->setParameter('indicators', $indicators);
        
        if( $iteration ) {
            $qb->andWhere('goa.iteration = :iteration')->setParameter('iteration', $iteration);
        }
        
        $value = $qb->getQuery()->getSingleScalarResult();
        return is_null($value)?0:$value;
    }
}
