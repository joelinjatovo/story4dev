<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Periodicity;
use App\Form\PeriodicityType;
use App\Service\FormError;
use App\Controller\Web\BaseController;


/** 
 * @Route(name="admin_periodicity_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class PeriodicityController extends BaseController
{
    /**
     * @Route("/admin/periodicity", name="index", methods="GET")
     */
    public function index()
    {
        $periodicity = new Periodicity();
        
        $form = $this->createForm(PeriodicityType::class, $periodicity);
        
        return $this->render('admin/periodicity/create.html.twig', [
            'periodicity' => $periodicity,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/admin/periodicity", name="create", methods="POST")
     */
    public function create(Request $request): Response
    {
        $periodicity = new Periodicity();
        
        $form = $this->createForm(PeriodicityType::class, $periodicity);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) {
            if( $periodicity->getAuthor() == null ) {
                $periodicity->setAuthor( $this->getUser() );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($periodicity);
            $entityManager->flush();
        
            $this->addFlash('success', 'Une nouvelle périodicité a été bien créée avec succès.');

            return $this->redirectToRoute('admin_periodicity_edit', [
                'id'   => $periodicity->getId()
            ]);
        }
        
        $this->addFlash('error', 'Votre demande est invalide! Veuillez réessayer!');

        return $this->redirectToRoute('admin_periodicity_create');
    }
    
    /**
     * @Route("/admin/periodicity/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     * @Entity("periodicity", options={"mapping": {"id": "id"}})
     */
    public function edit(Periodicity $periodicity, Request $request, FormError $formError)
    {
        $form = $this->createForm(PeriodicityType::class, $periodicity);
        
        return $this->render('admin/periodicity/edit.html.twig', [
            'periodicity' => $periodicity,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/admin/periodicity/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     * @Entity("periodicity", options={"mapping": {"id": "id"}})
     */
    public function update(Periodicity $periodicity, Request $request, FormError $formError)
    {
        $form = $this->createForm(PeriodicityType::class, $periodicity);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($periodicity);
                $entityManager->flush();
        
                $this->addFlash('success', "La périodicité a été bien modifiée avec succès.");

                return $this->redirectToRoute('admin_periodicity_edit', [
                    'id' => $periodicity->getId()
                ]);
            }else{
                $this->addFlash('error', 'Votre demande est invalide! Veuillez réessayer! ' . $form->getErrors() );
            }
        }
        
        return $this->render('admin/periodicity/edit.html.twig', [
            'periodicity' => $periodicity,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/admin/periodicity/remove", name="remove", methods="POST")
     * 
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->getFilters()->disable("deleted");
                
                $periodicity = $entityManager->getRepository(Periodicity::class)->find($id);
                if( $periodicity ){
                    $entityManager->remove($periodicity);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Périodicité supprimée complètement avec succès',
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
     * @Route("/admin/periodicities/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $periodicities = $entityManager->getRepository(Periodicity::class)->findAll();
        
        return $this->render('admin/periodicity/list.html.twig', ['periodicities' => $periodicities]);
    }
}
