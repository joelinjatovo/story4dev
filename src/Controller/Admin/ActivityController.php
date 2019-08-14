<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Project;
use App\Entity\Activity;
use App\Form\ActivityType;

/** @Route("/admin", name="admin_activity_") */
class ActivityController extends AbstractController
{
    /**
     * @Route("/activity", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/activity/create.html.twig');
    }
    
    /**
     * @Route("/activity", name="create", methods="POST")
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if ( ! $form->isValid() ) {
                return $this->json([
                    'success' => false,
                    'title'   => 'Validation Error',
                    'status'  => 'error',
                    'message' => 'An error was occured. :)',
                    'errors'  => $form->getErrors(true, false),
                    'error'   => 'No error'
                ]);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'title'   => 'Success',
                'status'  => 'success',
                'message' => 'Activity created successfully.',
                'html'    => $this->renderView('admin/project/activity.html.twig', ['activity' => $activity] )
            ]);
            
        }
        
        if ( $request->isXmlHttpRequest() ) {
        }

        return new Response('Saved new activity with id '.$activity->getId());
    }
    
    /**
     * @Route("/activity/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Activity $activity)
    {
        return $this->render('admin/activity/show.html.twig', ['activity' => $activity]);
    }
    
    /**
     * @Route("/activity/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Activity $activity)
    {
        return $this->render('admin/activity/edit.html.twig', ['activity' => $activity]);
    }
    
    /**
     * @Route("/activity/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Activity $activity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $activity->setTitle('New activity name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('admin_activity_show', ['id' => $activity->getId()]);
    }
    
    /**
     * @Route("/activity/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Activity $activity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($activity);
        
        $entityManager->flush();
        
        return new Response('Activity removed successfully');
    }
    
    /**
     * @Route("/activities", name="list", defaults={"page": "1"}, methods="GET", requirements={"page"="\d+"})
     * @Route("/activities/page/{page}", name="list_paginated", methods="GET", requirements={"page"="\d+"})
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $activities = $entityManager->getRepository(Activity::class)->findAll();
        
        return $this->render('admin/activity/list.html.twig', ['activities' => $activities]);
    }
}
