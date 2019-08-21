<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/** 
 * @Route(name="app_")
 *
 */
class IndexController extends AbstractController
{
    /**
    * @Route("/", name="index")
    */
    public function index(Request $request)
    {
        $number = random_int(0, 100);

        return $this->render('index/index.html.twig', [
            'number' => $number,
        ]);
    }
}