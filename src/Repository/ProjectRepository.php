<?php

namespace App\Repository;

use App\Entity\Project;
use App\Traits\PaginatorEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends AppRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }
    
    public function getAll($page = 1, $limit = 5)
    {
        $query = $this->createQueryBuilder('p')
            ->getQuery();

        return $this->paginate($query, $page, $limit);
    }
}
