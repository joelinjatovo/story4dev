<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Activity;
use App\Entity\Unit;
use App\Entity\Indicator;

/** @Route("/admin", name="admin_indicator_") */
class IndicatorController extends AbstractController
{
    /**
     * @Route("/indicator", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/indicator/create.html.twig');
    }
    
    /**
     * @Route("/indicator", name="create", methods="POST")
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $activity_id = $request->request->get('activity');
        $activity = $entityManager->getRepository(Activity::class)->find($activity_id);

        if (!$activity) {
            throw $this->createNotFoundException('No activity found for id '.$activity_id );
        }
        
        $unit_id = $request->request->get('unit');
        $unit = $entityManager->getRepository(Unit::class)->find($unit_id);

        if (!$unit) {
            throw $this->createNotFoundException('No unit found for id '.$unit_id );
        }

        $indicator = new Indicator();
        $indicator->setActivity( $activity );
        $indicator->setTitle( $request->request->get('title') );
        
        $errors = $validator->validate($indicator);
        if (count($errors) > 0) {
            if ( $request->isXmlHttpRequest() ) {
                return $this->json([
                    'success' => false,
                    'title'   => 'Validation Error',
                    'status'  => 'error',
                    'message' => 'An error was occured. :)',
                    'errors'  => $errors,
                    'error'   => (string) $errors
                ]);
            }

            return new Response((string) $errors, 400);
        }

        $entityManager->persist($indicator);

        $entityManager->flush();
        
        if ( $request->isXmlHttpRequest() ) {
            return $this->json([
                'success' => true,
                'title'   => 'Success',
                'status'  => 'success',
                'message' => 'Indicator created successfully.',
                'html'    => $this->renderView('admin/activity/indicator.html.twig', ['indicator' => $indicator] )
            ]);
        }

        return new Response('Saved new indicator with id '.$indicator->getId());
    }
    
    /**
     * @Route("/indicator/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Indicator $indicator)
    {
        return $this->render('admin/indicator/show.html.twig', ['indicator' => $indicator]);
    }
    
    /**
     * @Route("/indicator/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Indicator $indicator)
    {
        return $this->render('admin/indicator/edit.html.twig', ['indicator' => $indicator]);
    }
    
    /**
     * @Route("/indicator/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Indicator $indicator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $indicator->setTitle('New indicator name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('admin_indicator_show', ['id' => $indicator->getId()]);
    }
    
    /**
     * @Route("/indicator/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Indicator $indicator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($indicator);
        
        $entityManager->flush();
        
        return new Response('indicator removed successfully');
    }
    
    /**
     * @Route("/indicators", name="list", defaults={"page": "1"}, methods="GET", requirements={"page"="\d+"})
     * @Route("/indicators/page/{page}", name="list_paginated", methods="GET", requirements={"page"="\d+"})
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $indicators = $entityManager->getRepository(Indicator::class)->findAll();
        
        return $this->render('admin/indicator/list.html.twig', ['indicators' => $indicators]);
    }
}
