<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Project;

/** @Route("/admin/activity", name="admin_activity_") */
class ActivityController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('admin/activity/index.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }
}
