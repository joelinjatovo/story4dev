<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Indicator;
use App\Entity\Iteration;
use App\Entity\Goal;
use App\Entity\Unit;
use App\Form\IndicatorType;
use App\Form\GoalType;
use App\Service\FormError;
use App\Service\PaginatorService;

/** 
 * @Route(name="indicator_")
 *
 * @IsGranted("ROLE_USER") 
 */
class IndicatorController extends AbstractController
{
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator", name="index", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function index(User $user, Project $project, Activity $activity)
    {
        $this->denyAccessUnlessGranted('edit', $activity);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        $indicator = new Indicator();
        foreach($project->getIterations() as $iteration){
            $goal = new Goal();
            $goal->setAuthor($this->getUser());
            $goal->setIteration($iteration);
            $indicator->addGoal($goal);
        }

        $form = $this->createForm(IndicatorType::class, $indicator);

        return $this->render('indicator/create.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator,
            'form'      => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator", name="create", methods="POST", requirements={"project_id"="\d+", "activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function create(User $user, Project $project, Activity $activity, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $activity);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $indicator->setAuthor($this->getUser());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($indicator);

            $entityManager->flush();
            
            if ( $request->isXmlHttpRequest() ) {
                return $this->json([
                    'success' => true,
                    'title'   => 'Success',
                    'status'  => 'success',
                    'message' => 'Indicator created successfully.',
                    'html'    => $this->renderView('activity/indicator.html.twig', ['indicator' => $indicator] )
                ]);
            }
        
            $this->addFlash('success', 'Indicator created succesfully.');

            $args = [
                'slug'        => $user->getSlug(),
                'project_id'  => $project->getId(),
                'activity_id' => $activity->getId(),
            ];
            
            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-continue':
                    $args['indicator_id'] = $indicator->getId();
                    return $this->redirectToRoute('indicator_show', $args);
                case 'save-edit':
                    $args['indicator_id'] = $indicator->getId();
                    return $this->redirectToRoute('indicator_edit', $args);
                case 'save-exit':
                    return $this->redirectToRoute('activity_show', $args);
                case 'save-create':
                case 'save-default':
                default:
                    return $this->redirectToRoute('indicator_index', $args);
            }
            
        }
        
        if ( $request->isXmlHttpRequest() ) {
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'status'  => 'error',
                'message' => 'An error was occured. :)',
                'errors'  => $formError->getErrorMessages($form),
            ]);
        }
        
        $this->addFlash('error', 'Something went wrong.');

        return $this->redirectToRoute('indicator_index', [
            'slug'        => $user->getSlug(),
            'project_id'  => $project->getId(),
            'activity_id' => $activity->getId(), 
        ]);
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
        $this->denyAccessUnlessGranted('view', $indicator);
        
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
        
        $data =  $indicator->getData();
        
        return $this->render('indicator/show.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'form'      => $form->createView(),
            'data'     => json_encode($data),
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
        $this->denyAccessUnlessGranted('edit', $indicator);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $form = $this->createForm(IndicatorType::class, $indicator);
        
        return $this->render('indicator/edit.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'form'      => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/indicator/edit/{indicator_id}", name="update", methods="POST", requirements={"project_id"="\d+", "activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function update(User $user, Project $project, Activity $activity, Indicator $indicator, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $indicator);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $form = $this->createForm(IndicatorType::class, $indicator);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($indicator);
            $entityManager->flush();
            
            if ( $request->isXmlHttpRequest() ) {
                return $this->json([
                    'success' => true,
                    'title'   => 'Success',
                    'status'  => 'success',
                    'message' => 'Indicator updated successfully.',
                ]);
            }
        
            $this->addFlash('success', 'Indicator updated succesfully.');
            
            $args = [
                'slug'        => $user->getSlug(),
                'project_id'  => $project->getId(),
                'activity_id' => $activity->getId(),
                'indicator_id' => $indicator->getId()
            ];
            
            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-continue':
                    return $this->redirectToRoute('indicator_show', $args);
                case 'save-exit':
                    return $this->redirectToRoute('activity_show', $args);
                case 'save-create':
                    return $this->redirectToRoute('indicator_index', $args);
                case 'save-edit':
                case 'save-default':
                default:
                    return $this->redirectToRoute('indicator_edit', $args);
            }
            
            return $this->redirectToRoute('indicator_edit', $args);
        }
        
        if ( $request->isXmlHttpRequest() ) {
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'status'  => 'error',
                'message' => 'An error was occured. :)',
                'errors'  => $formError->getErrorMessages($form),
            ]);
        }
        
        $this->addFlash('error', 'Something went wrong.');

        return $this->redirectToRoute('indicator_edit', [
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
        $this->denyAccessUnlessGranted('remove', $indicator);
        
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
    public function list(User $user, Project $project, Activity $activity, $page = 1, PaginatorService $paginator)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $query = $entityManager->getRepository(Indicator::class)->findByActivity($activity);
        
        $indicators = $paginator->paginate($query, 10);
        
        return $this->render('indicator/list.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'activity'   => $activity,
            'indicators' => $indicators
        ]);
    }
}
