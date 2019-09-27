<?php

namespace App\Repository;

use App\Entity\ActivityFile;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ActivityFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityFile[]    findAll()
 * @method ActivityFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityFile::class);
    }
    
    public function findByProject(Project $project, User $user)
    {
        return $this->createQueryBuilder('af')
            ->innerJoin('af.activity', 'a')
            ->andWhere('a.project = :project')
            ->setParameter('project', $project)
            ->orderBy('af.id', 'ASC')
            ->getQuery()
        ;
    }

    // /**
    //  * @return ActivityFile[] Returns an array of ActivityFile objects
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
    public function findOneBySomeField($value): ?ActivityFile
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
