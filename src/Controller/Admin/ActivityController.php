<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Indicator;
use App\Form\ActivityType;
use App\Form\IndicatorType;

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
                
                $errors = [];
                foreach ($form->all() as $child) {
                    if (!$child->isValid()) {
                       $errors[$child->getName()] = (String) $form[$child->getName()]->getErrors();
                    }
                }
                
                return $this->json([
                    'success' => false,
                    'title'   => 'Validation Error',
                    'status'  => 'error',
                    'message' => 'An error was occured. :)',
                    'errors'  => $errors
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
            return $this->json([
                'success' => false,
                'title'   => 'Error',
                'status'  => 'error',
                'message' => 'Something went wrong. :)',
                'errors'  => [],
            ]);
        }

        return new Response('Saved new activity with id '.$activity->getId());
    }
    
    /**
     * @Route("/activity/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Activity $activity)
    {
        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        
        return $this->render('admin/activity/show.html.twig', ['activity' => $activity, 'form' => $form->createView() ]);
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
     * @Route("/activities/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $activities = $entityManager->getRepository(Activity::class)->findAll();
        
        return $this->render('admin/activity/list.html.twig', ['activities' => $activities]);
    }
}
