<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Indicator;
use App\Controller\Web\BaseController;

/** 
 * @Route(name="admin_indicator_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class IndicatorController extends BaseController
{
    
    /**
     * @Route("/admin/indicator/remove", name="remove", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->getFilters()->disable("deleted");
                
                $indicator = $entityManager->getRepository(Indicator::class)->find($id);
                if( $indicator && $indicator->isDeleted()){
                    $entityManager->remove($indicator);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.indicator.deleted.completely'),
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
    
    /**
     * @Route("/admin/indicator/restore", name="restore", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function restore(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                
                $entityManager->getFilters()->disable("deleted");

                $indicator = $entityManager->getRepository(Indicator::class)->find($id);
                if( $indicator && $indicator->isDeleted()){
                    $indicator->setDeletedAt(null);
                    $entityManager->persist($indicator);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.indicator.restored'),
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
}
