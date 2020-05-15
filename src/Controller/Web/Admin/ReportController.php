<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Report;
use App\Service\PaginatorService;
use App\Controller\Web\BaseController;

/** 
 * @Route(name="admin_report_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class ReportController extends BaseController
{
    
    /**
     * @Route("/admin/report/remove", name="remove", methods="POST")
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
                
                $report = $entityManager->getRepository(Report::class)->find($id);
                if( $report && $report->isDeleted()){
                    $entityManager->remove($report);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Rapport supprimé complètement avec succès',
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
     * @Route("/admin/report/restore", name="restore", methods="POST")
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

                $report = $entityManager->getRepository(Report::class)->find($id);
                if( $report && $report->isDeleted()){
                    $report->setDeletedAt(null);
                    $entityManager->persist($report);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Rapport restauré avec succès',
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
