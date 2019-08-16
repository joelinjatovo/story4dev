<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/** @Route(name="account_") */
class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="index")
     */
    public function index()
    {
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
    
    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        return $this->render('account/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
