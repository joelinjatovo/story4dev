<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\ProjectContribution;
use App\Entity\Project;

/**
 * @method Contributor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contributor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contributor[]    findAll()
 * @method Contributor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectContributionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectContribution::class);
    }
    
    public function findByProject(Project $project, $orderBy = 'createdAt', $order = 'ASC', $limit = 0)
    {
        switch($orderBy){
            case 'title':
                $orderBy = 'u.fullName';
            break;
            default:
            case 'createdAt':
                $orderBy = 'pc.createdAt';
            break;
        }
        
        $queryBuilder = $this->createQueryBuilder('pc')
            ->leftJoin('pc.user', 'u')
            ->where('pc.project = :project')
            ->setParameter('project', $project)
            ->orderBy($orderBy, $order);
        
        if($limit > 0){
            $queryBuilder->setMaxResults($limit);
        }
        
        return $queryBuilder->getQuery();
    }
}
