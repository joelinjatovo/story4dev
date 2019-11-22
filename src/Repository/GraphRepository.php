<?php

namespace App\Repository;

use App\Entity\Graph;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Graph|null find($id, $lockMode = null, $lockVersion = null)
 * @method Graph|null findOneBy(array $criteria, array $orderBy = null)
 * @method Graph[]    findAll()
 * @method Graph[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GraphRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Graph::class);
    }
    
    public function findByProject(Project $project, $orderBy = 'createdAt', $order = 'ASC', $limit = 0)
    {
        $queryBuilder = $this->createQueryBuilder('g')
            ->where('g.project = :project')
            ->setParameter('project', $project)
            ->orderBy('g.'.$orderBy, $order);
        
        if($limit > 0){
            $queryBuilder->setMaxResults($limit);
        }
        
        return $queryBuilder->getQuery();
    }

    // /**
    //  * @return Graph[] Returns an array of Graph objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Graph
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
