<?php

namespace App\Repository;

use App\Entity\ActivityFavorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ActivityFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityFavorite[]    findAll()
 * @method ActivityFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityFavorite::class);
    }

    // /**
    //  * @return ActivityFavorite[] Returns an array of ActivityFavorite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivityFavorite
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
