<?php

namespace App\Repository;

use App\Entity\Indicator;
use App\Entity\Activity;
use App\Entity\Project;
use App\Entity\IndicatorFavorite;
use App\Entity\User;
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
    
    public function getAllDeleted()
    {
        return $this->createQueryBuilder('i')
            ->where("i.deletedAt IS NOT NULL")
            ->getQuery();
    }
    
    public function findByActivity(Activity $activity, $search = null)
    {
        if( empty($search) ){
            return $this->createQueryBuilder('i')
                ->where('i.activity = :activity')
                ->setParameter('activity', $activity)
                ->orderBy('i.title', 'ASC')
                ->getQuery();
        }
        
        return $this->createQueryBuilder('i')
            ->where('i.activity = :activity AND i.title LIKE :search')
            ->setParameter('activity', $activity)
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('i.title', 'ASC')
            ->getQuery();
    }
    
    public function findByProject(Project $project, $search = null)
    {
        if( empty($search) ){
            return $this->createQueryBuilder('i')
                ->join('i.activity', 'a')
                ->where('a.project = :project')
                ->setParameter('project', $project)
                ->orderBy('i.title', 'ASC')
                ->getQuery();
        }
        
        return $this->createQueryBuilder('i')
            ->join('i.activity', 'a')
            ->where('a.project = :project AND i.title LIKE :search')
            ->setParameter('project', $project)
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('i.title', 'ASC')
            ->getQuery();
    }
    
    public function findFavorite(Activity $activity, User $user)
    {
        return $this->createQueryBuilder('i')
            ->join(IndicatorFavorite::class, 'if1', 'WITH if1.indicator = i')
            ->where('i.activity = :activity AND if1.user = :user')
            ->setParameter('activity', $activity)
            ->setParameter('user', $user)
            ->orderBy('i.title', 'ASC')
            ->getQuery();
    }
}
