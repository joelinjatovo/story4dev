<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\User;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, File::class);
    }
    
    public function findAllQuery(User $user)
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.activityFiles', 'af')
            ->innerJoin('af.activity', 'a')
            ->innerJoin('a.project', 'p')
            ->innerJoin('p.contributions', 'c')
            ->where('c.user = :user OR p.author = :user')
            ->setParameter('user', $user)
            ->getQuery()
        ;
    }
    
    public function findByProject(Project $project, User $user)
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.activityFiles', 'af')
            ->innerJoin('af.activity', 'a')
            ->innerJoin('a.project', 'p')
            ->innerJoin('p.contributions', 'c')
            ->where('a.project = :project AND ( c.user = :user OR p.author = :user )')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->orderBy('f.id', 'ASC')
            ->getQuery()
        ;
    }

    // /**
    //  * @return File[] Returns an array of File objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
