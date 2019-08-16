<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

/** @Route({
     *     "fr": "/admin",
     *     "en": "/admin"
     * }, name="admin_") */
class IndexController extends AbstractController
{
    /**
    * @Route("/", name="index")
    */
    public function index(Request $request)
    {
        $number = random_int(0, 100);

        return $this->render('admin/index.html.twig', [
            'number' => $number,
        ]);
    }
}