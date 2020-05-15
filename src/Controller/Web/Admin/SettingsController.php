<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Option;
use App\Form\GeneralSettingType;
use App\Helper\OptionHelper;
use App\Controller\Web\BaseController;

/** 
 * @Route(name="admin_settings_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class SettingsController extends BaseController
{
    /**
     * @Route("/admin/settings", name="index")
     * @Route("/admin/settings/general", name="general")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $settings = OptionHelper::getOptionsFields($em);

        $form = $this->createForm(GeneralSettingType::class, null, ['settings'=>$settings]);
        
        $form->handleRequest($request);

        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                foreach($settings as $key => $setting){
                    $option = $form->get($key)->getData();
                    $em->persist($option);
                }
                $em->flush();
        
                $this->addFlash('success', $this->trans('controller.settings.success') );
            }else{
                $this->addFlash('error', $this->trans('controller.error.occured') . ' ' . $form->getErrors() );
            }
        }
        
        return $this->render('admin/settings/index.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }
}
