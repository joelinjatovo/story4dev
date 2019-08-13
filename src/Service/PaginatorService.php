<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use InvalidArgumentException;

class PaginatorService
{
    protected $page;
    protected $limit;
    
    public function getPage()
    {
        return $this->page;
    }
    
    public function getLimit()
    {
        return $this->limit;
    }
    
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
        
        $this->page = $page;
        $this->limit = $limit;
        
        $offset = $limit * ($page - 1);
        
        $query->setFirstResult($offset) // Offset
            ->setMaxResults($limit); // Limit

        $paginator = new Paginator($query);
        if ( ($paginator->count() <= $offset) && $page != 1) {
            //throw new NotFoundHttpException('La page demandée n\'existe pas.');
        }
        
        return $paginator;
    }
}
