<?php

namespace App\Repository;

use App\Entity\IndicatorFavorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method IndicatorFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndicatorFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndicatorFavorite[]    findAll()
 * @method IndicatorFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicatorFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndicatorFavorite::class);
    }

    // /**
    //  * @return IndicatorFavorite[] Returns an array of IndicatorFavorite objects
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
    public function findOneBySomeField($value): ?IndicatorFavorite
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
