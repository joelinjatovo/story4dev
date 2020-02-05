<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Report;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\ProjectContribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Report::class);
    }
    
    public function getAllDeleted()
    {
        return $this->createQueryBuilder('p')
            ->where("p.deletedAt IS NOT NULL")
            ->getQuery();
    }
    
    public function findByAuthor(User $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.author = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
    }
        
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.author = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
    }
    
    public function findByActivity(Activity $activity, $args = [])
    {
        $default = [
            'user'    => null,  
            'orderBy' => 'createdAt',  
            'order'   => 'ASC',  
            'limit'   => 0,  
            'search'  => null,  
            'status'  => null,  
        ];
        $args = array_merge($default, (array) $args);
        
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->andWhere('r.activity = :activity')->setParameter('activity', $activity);
        
        if( ! is_null($args['user']) && ( $args['user'] instanceof User ) ){
            $queryBuilder->andWhere('r.author = :user')->setParameter('user', $args['user']);
        }
        
        if( ! is_null($args['status']) && ( in_array( $args['status'] , [Report::STATUS_OPENED, Report::STATUS_CLOSED, Report::STATUS_TERMINATED]) ) ) {
            $queryBuilder->andWhere('r.status = :status')->setParameter('status', $args['status']);
        }
        
        if( ! is_null($args['search']) && ! empty($args['search']) ) {
            $queryBuilder->andWhere('r.title LIKE :search OR r.description LIKE :search')->setParameter('search', '%' . $args['search'] . '%');
        }
        
        if( $args['limit'] > 0) {
            $queryBuilder->setMaxResults((int) $args['limit']);
        }
        
        $queryBuilder->addOrderBy('r.id','DESC')
            ->addOrderBy('r.'.$args['orderBy'], $args['order']);
        
        return $queryBuilder->getQuery();
    }
    
    public function findByProject(Project $project, $args = [])
    {
        $default = [
            'user'    => null,  
            'orderBy' => 'createdAt',  
            'order'   => 'ASC',  
            'limit'   => 0,  
            'search'  => null,  
            'status'  => null,  
        ];
        $args = array_merge($default, (array) $args);
        
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->leftJoin('r.activity', 'a');
        $queryBuilder->andWhere('a.project = :project')->setParameter('project', $project);
        
        if( ! is_null($args['user']) && ( $args['user'] instanceof User ) ){
            $queryBuilder->andWhere('r.author = :user')->setParameter('user', $args['user']);
        }
        
        if( ! is_null($args['status']) && ( in_array( $args['status'] , [Report::STATUS_OPENED, Report::STATUS_CLOSED, Report::STATUS_TERMINATED]) ) ) {
            $queryBuilder->andWhere('r.status = :status')->setParameter('status', $args['status']);
        }
        
        if( ! is_null($args['search']) && ! empty($args['search']) ) {
            $queryBuilder->andWhere('r.title LIKE :search OR r.description LIKE :search')->setParameter('search', '%' . $args['search'] . '%');
        }
        
        if( $args['limit'] > 0) {
            $queryBuilder->setMaxResults((int) $args['limit']);
        }
        
        $queryBuilder->addOrderBy('r.id','DESC')
            ->addOrderBy('r.'.$args['orderBy'], $args['order']);
        
        return $queryBuilder->getQuery();
    }
    
    public function feedByProject(Project $project, ?User $user = null, $orderBy = 'createdAt', $order = 'DESC', $limit = 0)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->leftJoin('r.activity', 'a');
        $queryBuilder->andWhere('a.project = :project')->setParameter('project', $project);
        $queryBuilder->andWhere('r.publishExternally = :publish OR r.publishExternally IS NULL')->setParameter('publish', 1);
        //$queryBuilder->andWhere('r.status = :status')->setParameter('status', Report::STATUS_TERMINATED);
        $queryBuilder->orderBy('r.'.$orderBy, $order);
        
        if($user){
            $queryBuilder->andWhere('r.author = :user')->setParameter('user', $user);
        }
        
        if($limit > 0){
            $queryBuilder->setMaxResults($limit);
        }
        
        return $queryBuilder->getQuery();
    }
    
    public function findByContribution(Project $project, User $user)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.activity', 'a')
            ->leftJoin('a.project', 'p')
            ->where('a.project = :project AND r.author = :user')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery();
    }
    
    public function countByProject(Project $project)
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->leftJoin('r.activity', 'a')
            ->where('a.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
