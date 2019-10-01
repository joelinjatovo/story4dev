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
        return $this->json([
            'status'  => 'success', 
            'message' => 'Logged in user', 
            'data'    => $this->getUser()
        ], 200, [], ['groups' => ["user"]]);
    }

}