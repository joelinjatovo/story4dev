<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use InvalidArgumentException;
use App\Entity\Option\GeneralOption;

class PaginatorService
{
    private $request;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->em = $em;
    }
    
    public function paginate($query, $limit = false)
    {
        $params = $this->request->attributes->get('_route_params');
        $page = isset($params['page'])?$params['page']:1;
        
        if (!is_numeric($page)) {
            throw new InvalidArgumentException('La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').');
        }

        if ($page < 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }
        
        if($limit===false){
            $option = $this->em
                ->getRepository(GeneralOption::class)
                ->findOneBy(['option_key' => 'paginator_limit']);

            if($option){
                $limit = $option->getOptionValue();
            }else{
                $limit = 5;
            }
        }
            
        if (!is_numeric($limit)) {
            throw new InvalidArgumentException('La valeur de l\'argument $limit est incorrecte (valeur : ' . $limit . ').');
        }
        
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
