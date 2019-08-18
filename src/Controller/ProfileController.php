<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/** @Route(name="profile_") */
class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="index")
     */
    public function index()
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    
    /**
     * @Route("/profile/edit", name="edit")
     */
    public function edit()
    {
        return $this->render('profile/edit.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
