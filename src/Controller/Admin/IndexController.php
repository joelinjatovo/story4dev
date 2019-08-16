<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/** 
 * @Route("/admin", name="admin_")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class IndexController extends AbstractController
{
    /**
    * @Route("/", name="index")
    */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $number = random_int(0, 100);

        return $this->render('admin/index.html.twig', [
            'number' => $number,
        ]);
    }
}