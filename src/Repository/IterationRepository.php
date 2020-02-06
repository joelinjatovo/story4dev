<?php

namespace App\Repository;

use App\Entity\Iteration;
use App\Entity\Project;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Iteration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Iteration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Iteration[]    findAll()
 * @method Iteration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IterationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Iteration::class);
    }
}
