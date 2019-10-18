<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use InvalidArgumentException;

class AppRepository extends ServiceEntityRepository
{
    public function paginate($query, $page = 1, $limit = 5)
    {
        if (!is_numeric($page)) {
            throw new InvalidArgumentException('La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').');
        }

        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }
        
        if (!is_numeric($limit)) {
            throw new InvalidArgumentException('La valeur de l\'argument $limit est incorrecte (valeur : ' . $limit . ').');
        }
        
        
        $offset = $limit * ($page - 1);
        
        $query->setFirstResult($offset) // Offset
            ->setMaxResults($limit); // Limit

        $paginator = new Paginator($query);
        if ( ($paginator->count() <= $offset) && $page != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.');
        }
        
        return [
            'paginator' => $paginator,
            'page' => [
                'current' => $page,
                'count'   => $paginator->count(),
            ],
        ];
    }
}
