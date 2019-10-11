<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/** 
 * @Route(name="admin_settings_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class SettingsController extends AbstractController
{
    /**
     * @Route("/admin/settings/{tab?}", defaults={"tab":"general"}, name="index")
     */
    public function index($tab)
    {
        return $this->render('admin/settings/index.html.twig', [
            'tab' => $tab,
            'user' => $this->getUser(),
        ]);
    }
}
