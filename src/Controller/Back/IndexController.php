<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
    * @Route({
     *     "fr": "/back",
     *     "en": "/back"
     * }, name="back")
    */
    public function index(Request $request)
    {
        $number = random_int(0, 100);

        return $this->render('back/index.html.twig', [
            'number' => $number,
        ]);
    }
}