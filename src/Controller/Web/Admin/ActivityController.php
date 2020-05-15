<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Activity;
use App\Controller\Web\BaseController;

/** 
 * @Route(name="admin_activity_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class ActivityController extends BaseController
{
    
    /**
     * @Route("/admin/activity/remove", name="remove", methods="POST")
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
                
                $activity = $entityManager->getRepository(Activity::class)->find($id);
                if( $activity && $activity->isDeleted()){
                    $entityManager->remove($activity);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Activité supprimé complètement avec succès',
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
     * @Route("/admin/activity/restore", name="restore", methods="POST")
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

                $activity = $entityManager->getRepository(Activity::class)->find($id);
                if( $activity && $activity->isDeleted()){
                    $activity->setDeletedAt(null);
                    $entityManager->persist($activity);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Activité restauré avec succès',
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
