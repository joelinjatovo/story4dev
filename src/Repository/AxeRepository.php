<?php

namespace App\Repository;

use App\Entity\Axe;
use App\Entity\Graph;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Axe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Axe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Axe[]    findAll()
 * @method Axe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AxeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Axe::class);
    }
    
    public function getData(Graph $graph, $indicators = null, $valueField = 'value')
    {
        $qb = $this->createQueryBuilder('axe');
        $qb->select("axe.id AS axe_id, axe.title AS iteration, CASE WHEN SUM(res.value) IS NULL THEN 0 ELSE SUM(res.value) END as " . $valueField );
        $qb->leftJoin('axe.graph', 'gra');
        $qb->leftJoin('gra.project', 'pro');
        $qb->leftJoin('pro.activities', 'act');
        $qb->leftJoin('act.reports', 'rep', 'WITH', 'DATE(rep.createdAt) >= DATE(axe.startAt) AND DATE(rep.createdAt) <= DATE(axe.endAt)');
        
        if( is_array( $indicators ) && ( count($indicators) > 0 ) ){
            $qb->leftJoin('rep.results', 'res', 'WITH', 'res.indicator IN (:indicators)');
            $qb->setParameter('indicators', $indicators);
        }else{
            $qb->leftJoin('rep.results', 'res');
        }
        
        $qb->groupBy('axe_id');
        $qb->orderBy('axe.startAt', 'ASC');
        $qb->where('axe.graph = :graph');
        $qb->setParameter('graph', $graph);

        return $qb->getQuery()->getResult();
    }
}
