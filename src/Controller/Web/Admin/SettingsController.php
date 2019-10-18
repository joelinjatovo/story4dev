<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Option;
use App\Form\GeneralSettingType;
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
        switch($tab){
            default:
            break;
        }
        
        $form = $this->createForm(GeneralSettingType::class);
        
        return $this->render('admin/settings/index.html.twig', [
            'tab'  => $tab,
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }
}
