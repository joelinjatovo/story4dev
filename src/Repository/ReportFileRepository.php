<?php

namespace App\Repository;

use App\Entity\ReportFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReportFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportFile[]    findAll()
 * @method ReportFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportFile::class);
    }

    // /**
    //  * @return ReportFile[] Returns an array of ReportFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReportFile
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
