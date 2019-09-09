<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/** 
 * @Route(name="setting_")
 *
 * @IsGranted("ROLE_USER") 
 */
class SettingController extends AbstractController
{
    /**
     * @Route("/setting/{tab?}", defaults={"tab":"general"}, name="index")
     */
    public function index($tab)
    {
        return $this->render('setting/index.html.twig', [
            'tab' => $tab,
            'user' => $this->getUser(),
        ]);
    }
}
