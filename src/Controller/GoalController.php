<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

/** @Route(name="goal_") */
class GoalController extends AbstractController
{
    /**
     * @Route("/goal", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('goal/create.html.twig');
    }
    
    /**
     * @Route("/goal", name="create", methods="POST")
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $goal = new Goal();
        $form = $this->createForm(GoalType::class, $goal);
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
            $entityManager->persist($goal);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'title'   => 'Success',
                'status'  => 'success',
                'message' => 'Goal created successfully.',
                'html'    => $this->renderView('indicator/goal.html.twig', ['goal' => $goal] )
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

        return new Response('Saved new goal with id '.$goal->getId());
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/{indicator_id}/goal/{goal_id}", name="show", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+", "goal_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     * @Entity("goal", options={"mapping": {"goal_id": "id"}})
     */
    public function show(User $user, Project $project, Activity $activity, Indicator $indicator, Goal $goal)
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
        
        if($goal->getIndicator() != $indicator ){
            throw $this->createNotFoundException('The indicator does not match');
        }
        
        return $this->render('goal/show.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'goal'      => $goal,
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/{indicator_id}/goal/edit/{goal_id}", name="edit", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+", "goal_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     * @Entity("goal", options={"mapping": {"goal_id": "id"}})
     */
    public function edit(User $user, Project $project, Activity $activity, Indicator $indicator, Goal $goal)
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
        
        if($goal->getIndicator() != $indicator ){
            throw $this->createNotFoundException('The indicator does not match');
        }
        
        return $this->render('goal/edit.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'goal'      => $goal
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/{indicator_id}/goal/edit/{goal_id}", name="update", methods="POST", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+", "goal_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     * @Entity("goal", options={"mapping": {"goal_id": "id"}})
     */
    public function update(User $user, Project $project, Activity $activity, Indicator $indicator, Goal $goal)
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
        
        if($goal->getIndicator() != $indicator ){
            throw $this->createNotFoundException('The indicator does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $goal->setTitle('New goal name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('goal_show', [
            'slug'         => $user->getSlug(),
            'project_id'   => $project->getId(),
            'activity_id'  => $activity->getId(),
            'indicator_id' => $indicator->getId(),
            'goal_id'      => $goal->getId()
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/{indicator_id}/goal/remove/{goal_id}", name="remove", methods="POST", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+", "goal_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     * @Entity("goal", options={"mapping": {"goal_id": "id"}})
     */
    public function remove(User $user, Project $project, Activity $activity, Indicator $indicator, Goal $goal)
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
        
        if($goal->getIndicator() != $indicator ){
            throw $this->createNotFoundException('The indicator does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($goal);
        
        $entityManager->flush();
        
        return new Response('goal removed successfully');
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/{indicator_id}/goals/{page<\d+>?1}", name="list", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+", "goal_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     * @Entity("goal", options={"mapping": {"goal_id": "id"}})
     */
    public function list(User $user, Project $project, Activity $activity, Indicator $indicator, $page = 1)
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
        
        if($goal->getIndicator() != $indicator ){
            throw $this->createNotFoundException('The indicator does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $goals = $entityManager->getRepository(Goal::class)->findAll();
        
        return $this->render('goal/list.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'goals'     => $goals
        ]);
    }
}
