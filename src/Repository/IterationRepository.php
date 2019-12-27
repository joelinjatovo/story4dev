<?php

namespace App\Repository;

use App\Entity\Iteration;
use App\Entity\Project;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Iteration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Iteration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Iteration[]    findAll()
 * @method Iteration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IterationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Iteration::class);
    }
    
    public function getData(Project $project, $indicators = null, $valueField = 'value')
    {
        $qb = $this->createQueryBuilder('ite');
        $qb->select("ite.id AS ite_id, ite.title AS iteration, CASE WHEN SUM(res.value) IS NULL THEN 0 ELSE SUM(res.value) END as " . $valueField );
        $qb->leftJoin('ite.project', 'pro');
        $qb->leftJoin('pro.activities', 'act');
        $qb->leftJoin('act.reports', 'rep', 'WITH', 'DATE(rep.createdAt) >= DATE(ite.startAt) AND DATE(rep.createdAt) <= DATE(ite.endAt)');
        
        if( is_array( $indicators ) && ( count($indicators) > 0 ) ){
            $qb->leftJoin('rep.results', 'res', 'WITH', 'res.indicator IN (:indicators)');
            $qb->setParameter('indicators', $indicators);
        }else{
            $qb->leftJoin('rep.results', 'res');
        }
        
        $qb->groupBy('ite_id');
        $qb->orderBy('ite.startAt', 'ASC');
        $qb->where('ite.project = :project');
        $qb->setParameter('project', $project);

        return $qb->getQuery()->getResult();
    }
}
