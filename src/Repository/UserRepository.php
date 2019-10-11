<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

use App\Entity\User;
use App\Entity\Project;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    public function getAll($search = null, $role = null, $status = null)
    {
        $qb = $this->createQueryBuilder('u');
        if( ! empty( $role ) ){
            $qb->andWhere('u.roles LIKE :roles')
                ->setParameter('roles', '%'.$role.'%');
            
        }
        
        if( ! empty( $status ) ){
            $qb->andWhere('u.status LIKE :status')
                ->setParameter('status', $status);
        }
        
        if( ! empty( $search ) ){
            $qb->andWhere(' ( (u.fullName LIKE :search) OR (u.email LIKE :search) OR (u.username LIKE :search) OR (u.phone LIKE :search) OR (u.website LIKE :search) OR (u.title LIKE :search) OR (u.company LIKE :search) OR (u.presentation LIKE :search) )')
                ->setParameter('search', '%'.$search.'%');
        }
        
        return $qb->getQuery();
    }
    
    public function getRecent($limit = 10)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
    public function findOneByConfirmToken($token)
    {
        return $this->createQueryBuilder('u')
            ->where('u.confirmToken = :query AND u.confirmedAt > :last')
            ->setParameter('query', $token)
            ->setParameter('last', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findOneByResetToken($token)
    {
        return $this->createQueryBuilder('u')
            ->where('u.resetToken = :query AND u.resetedAt > :last')
            ->setParameter('query', $token)
            ->setParameter('last', new \DateTime('-1 hour'), \Doctrine\DBAL\Types\Type::DATETIME)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function loadUserByUsername($emailOrUsername)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :query OR u.email = :query')
            ->setParameter('query', $emailOrUsername)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findAuthorAndContrubitors(Project $project)
    {
        return $this->createQueryBuilder('u')
            ->join('u.projectContributions', 'c')
            ->where('c.project = :project')
            ->setParameter('project', $project)
            ->getQuery();
    }
    
    public function findContributors(Project $project, User $user)
    {
        return $this->createQueryBuilder('u')
            ->join('u.projectContributions', 'c')
            ->where('c.project = :project AND c.user != :user')
            ->setParameter('project', $project)
            ->setParameter('user', $user)
            ->getQuery();
    }
    
    public function searchAllNotInProject(Project $project, string $search)
    {
        return $this->createQueryBuilder('u')
            //->join('u.projectContributions', 'c')
            ->where('( ( u.fullName LIKE :search ) OR (u.email LIKE :search) OR (u.username LIKE :search) )')
            //->setParameter('project', $project)
            ->setParameter('search', '%'.$search.'%')
            ->getQuery();
    }

}
