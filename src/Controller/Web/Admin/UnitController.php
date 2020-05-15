<?php

namespace App\Controller\Web\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Unit;
use App\Form\UnitType;
use App\Service\FormError;
use App\Controller\Web\BaseController;

/** 
 * @Route(name="admin_unit_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class UnitController extends BaseController
{
    /**
     * @Route("/admin/unit", name="index", methods="GET")
     */
    public function index()
    {
        $unit = new Unit();
        
        $form = $this->createForm(UnitType::class, $unit);
        
        return $this->render('admin/unit/create.html.twig', [
            'unit' => $unit,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/admin/unit", name="create", methods="POST")
     */
    public function create(Request $request): Response
    {
        $unit = new Unit();
        
        $form = $this->createForm(UnitType::class, $unit);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) {
            if( $unit->getAuthor() == null ) {
                $unit->setAuthor( $this->getUser() );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($unit);
            $entityManager->flush();
        
            $this->addFlash('success', $this->trans('controller.unit.created') );

            return $this->redirectToRoute('admin_unit_edit', [
                'id'   => $unit->getId()
            ]);
        }
        
        $this->addFlash('error', $this->trans('controller.error.occured'));

        return $this->redirectToRoute('admin_unit_create');
    }
    
    /**
     * @Route("/admin/unit/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     * @Entity("unit", options={"mapping": {"id": "id"}})
     */
    public function edit(Unit $unit)
    {
        $form = $this->createForm(UnitType::class, $unit);
        
        return $this->render('admin/unit/edit.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/admin/unit/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     * @Entity("unit", options={"mapping": {"id": "id"}})
     */
    public function update(Unit $unit, Request $request, FormError $formError)
    {
        $form = $this->createForm(UnitType::class, $unit);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($unit);
                $entityManager->flush();
        
                $this->addFlash('success', $this->trans('controller.unit.updated') );

                return $this->redirectToRoute('admin_unit_edit', [
                    'id' => $unit->getId()
                ]);
            }else{
                $this->addFlash('error', $this->trans('controller.error.occured') . ' ' . $form->getErrors() );
            }
        }
        
        return $this->render('admin/unit/edit.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/admin/unit/remove", name="remove", methods="POST")
     * 
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->getFilters()->disable("deleted");
                
                $unit = $entityManager->getRepository(Unit::class)->find($id);
                if( $unit ){
                    $entityManager->remove($unit);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.unit.deleted'),
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
     * @Route("/admin/units/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $units = $entityManager->getRepository(Unit::class)->findAll();
        
        return $this->render('admin/unit/list.html.twig', ['units' => $units]);
    }
}
