<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Version;

/**
 * @Version("v1")
 */
class UserController extends AbstractController
{
    /**
     * @Rest\Get("/user")
     */
    public function index(Request $request)
    {
        $format = "user";
        if( $request->query->has('_format') ){
             $format = $request->query->get('_format');
        }
        
        return $this->json(['_format' => $format, 'data' => $this->getUser()], 200, [], ['groups' => [$format]]);
    }

}