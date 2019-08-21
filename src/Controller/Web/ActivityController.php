<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Indicator;
use App\Form\ActivityType;
use App\Form\IndicatorType;

/** @Route(name="activity_") */
class ActivityController extends AbstractController
{
    /**
     * @Route("/activity", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('activity/create.html.twig');
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
                'html'    => $this->renderView('project/activity.html.twig', ['activity' => $activity] )
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
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}", name="show", methods="GET", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function show(User $user, Project $project, Activity $activity)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        
        return $this->render('activity/show.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity, 
            'form'     => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/edit/{activity_id}", name="edit", methods="GET", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function edit(User $user, Project $project, Activity $activity)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        return $this->render('activity/edit.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity,
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/edit/{activity_id}", name="update", methods="POST", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function update(User $user, Project $project, Activity $activity)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $activity->setTitle('New activity name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('activity_show', [
            'slug'        => $user->getSlug(),
            'project_id'  => $project->getId(),
            'activity_id' => $activity->getId()
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/remove/{activity_id}", name="remove", methods="POST", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function remove(User $user, Project $project, Activity $activity)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($activity);
        
        $entityManager->flush();
        
        return new Response('Activity removed successfully');
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activities/{page<\d+>?1}", name="list", methods="GET", requirements={"project_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(User $user, Project $project, $page = 1)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $activities = $entityManager->getRepository(Activity::class)->findAll();
        
        return $this->render('activity/list.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'activities' => $activities
        ]);
    }
}
