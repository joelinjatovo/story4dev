<?php

namespace App\Repository;

use App\Entity\Iteration;
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

    // /**
    //  * @return Iteration[] Returns an array of Iteration objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Iteration
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
