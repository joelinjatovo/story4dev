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
    
    public function getAll()
    {
        return $this->createQueryBuilder('u')
            ->getQuery();
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

}
