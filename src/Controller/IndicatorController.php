<?php

namespace App\Controller;

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
use App\Entity\Goal;
use App\Entity\Unit;
use App\Form\IndicatorType;
use App\Form\GoalType;

/** @Route(name="indicator_") */
class IndicatorController extends AbstractController
{
    /**
     * @Route("/indicator", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('indicator/create.html.twig');
    }
    
    /**
     * @Route("/indicator", name="create", methods="POST")
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        
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
            $entityManager->persist($indicator);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'title'   => 'Success',
                'status'  => 'success',
                'message' => 'Indicator created successfully.',
                'html'    => $this->renderView('activity/indicator.html.twig', ['indicator' => $indicator] )
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

        return new Response('Saved new indicator with id '.$indicator->getId());
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/{indicator_id}", name="show", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function show(User $user, Project $project, Activity $activity, Indicator $indicator)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $goal = new Goal();
        $form = $this->createForm(GoalType::class, $goal);
        
        return $this->render('indicator/show.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'form'      => $form->createView()
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/edit/{indicator_id}", name="edit", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function edit(User $user, Project $project, Activity $activity, Indicator $indicator)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        return $this->render('indicator/edit.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/update/{indicator_id}", name="update", methods="POST", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function update(User $user, Project $project, Activity $activity, Indicator $indicator)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $indicator->setTitle('New indicator name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('indicator_show', [
            'slug'         => $user->getSlug(),
            'project_id'   => $project->getId(),
            'activity_id'  => $activity->getId(),
            'indicator_id' => $indicator->getId()
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/remove/{indicator_id}", name="remove", methods="POST", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function remove(User $user, Project $project, Activity $activity, Indicator $indicator)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($indicator);
        
        $entityManager->flush();
        
        return new Response('indicator removed successfully');
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicators/{page<\d+>?1}", name="list", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function list(User $user, Project $project, Activity $activity, $page = 1)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $indicators = $entityManager->getRepository(Indicator::class)->findAll();
        
        return $this->render('indicator/list.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'activity'   => $activity,
            'indicators' => $indicators
        ]);
    }
}
