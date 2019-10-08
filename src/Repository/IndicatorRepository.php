<?php

namespace App\Repository;

use App\Entity\Indicator;
use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Indicator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indicator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indicator[]    findAll()
 * @method Indicator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicatorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Indicator::class);
    }
    
    public function findByActivity(Activity $activity, $search = null)
    {
        if( empty($search) ){
            return $this->createQueryBuilder('i')
                ->where('i.activity = :activity')
                ->setParameter('activity', $activity)
                ->getQuery();
        }
        
        return $this->createQueryBuilder('i')
            ->where('i.activity = :activity AND i.title LIKE :search')
            ->setParameter('activity', $activity)
            ->setParameter('search', '%'.$search.'%')
            ->getQuery();
    }

    // /**
    //  * @return Indicator[] Returns an array of Indicator objects
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
    public function findOneBySomeField($value): ?Indicator
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
