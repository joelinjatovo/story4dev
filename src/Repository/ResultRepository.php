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
    
    public function getValue($indicators, ?Iteration $iteration = null)
    {
            $qb = $this->createQueryBuilder('res');
            $qb->select("SUM(res.value) as value");
            $qb->andWhere('res.indicator IN (:indicators)')->setParameter('indicators', $indicators);
            
            if( $iteration ) {
                $qb->leftJoin('res.report', 'rep');
                $qb->leftJoin('rep.activity', 'act');
                $qb->leftJoin(Iteration::class, 'ite', 'WITH', 'ite.project = act.project');
                
                $qb->andWhere('DATE(rep.createdAt) >= DATE(ite.startAt)');
                $qb->andWhere('DATE(rep.createdAt) <= DATE(ite.endAt)');
                $qb->andWhere('ite = :iteration')->setParameter('iteration', $iteration);
            }
            
            $value = $qb->getQuery()->getSingleScalarResult();
            return is_null($value)?0:$value;
    }
}
