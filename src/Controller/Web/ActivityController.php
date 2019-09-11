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
use App\Form\ActivityType;
use App\Form\IndicatorType;
use App\Service\FormError;
use App\Service\PaginatorService;

/** 
 * @Route(name="activity_")
 *
 * @IsGranted("ROLE_USER") 
 */
class ActivityController extends AbstractController
{
    /**
     * @Route("/{slug}/project/{project_id}/activity", name="index", methods="GET", requirements={"project_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function index(User $user, Project $project)
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);

        return $this->render('activity/create.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/activity", name="create", methods="POST")
     * @Route("/{slug}/project/{project_id}/activity", name="create_2", methods="POST", requirements={"project_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function create(?User $user, ?Project $project, Request $request, FormError $formError): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            if($activity->getAddress() && $activity->getAddress()->isEmpty() ) {
                $activity->setAddress(null);
            }

            $activity->setAuthor($this->getUser());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();
            
            if ( $request->isXmlHttpRequest() ) {
                return $this->json([
                    'success' => true,
                    'title'   => 'Success',
                    'status'  => 'success',
                    'message' => 'Activity created successfully.',
                    'html'    => $this->renderView('project/activity.html.twig', ['activity' => $activity] )
                ]);
            }
        
            $this->addFlash('success', 'Activity created succesfully.');

            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-continue':
                    return $this->redirectToRoute('activity_show', [
                        'slug'        => $user->getSlug(),
                        'project_id'  => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-edit':
                    return $this->redirectToRoute('activity_edit', [
                        'slug'        => $user->getSlug(),
                        'project_id'  => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-exit':
                    return $this->redirectToRoute('project_show', [
                        'slug' => $user->getSlug(),
                        'id'   => $project->getId(), 
                    ]);
                case 'save-default':
                default:
                    return $this->redirectToRoute('activity_index', [
                        'slug'       => $user->getSlug(),
                        'project_id' => $project->getId(), 
                    ]);
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

        return $this->redirectToRoute('activity_index', [
            'slug'       => $user->getSlug(),
            'project_id' => $project->getId(), 
        ]);
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
        
        $form = $this->createForm(ActivityType::class, $activity);

        return $this->render('activity/edit.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity, 
            'form'     => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/edit/{activity_id}", name="update", methods="POST", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function update(User $user, Project $project, Activity $activity, Request $request, FormError $formError)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $form = $this->createForm(ActivityType::class, $activity);
        
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();
            
            if ( $request->isXmlHttpRequest() ) {
                return $this->json([
                    'success' => true,
                    'title'   => 'Success',
                    'status'  => 'success',
                    'message' => 'Activity updated successfully.',
                ]);
            }
        
            $this->addFlash('success', 'Activity updated succesfully.');

            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-continue':
                    return $this->redirectToRoute('activity_show', [
                        'slug'        => $user->getSlug(),
                        'project_id'  => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-edit':
                    return $this->redirectToRoute('activity_edit', [
                        'slug'        => $user->getSlug(),
                        'project_id'  => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-exit':
                    return $this->redirectToRoute('project_show', [
                        'slug' => $user->getSlug(),
                        'id'   => $project->getId(), 
                    ]);
                case 'save-default':
                default:
                    return $this->redirectToRoute('activity_edit', [
                        'slug'       => $user->getSlug(),
                        'project_id' => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);

            }

            return $this->redirectToRoute('activity_edit', [
                'slug'        => $user->getSlug(),
                'project_id'  => $project->getId(), 
                'activity_id' => $activity->getId(), 
            ]);
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

        return $this->redirectToRoute('activity_edit', [
            'slug'        => $user->getSlug(),
            'project_id'  => $project->getId(), 
            'activity_id' => $activity->getId(), 
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
    public function list(User $user, Project $project, $page = 1, PaginatorService $paginator)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(Activity::class)->findByProject($project);
        $activities = $paginator->paginate($query, 10);
        
        return $this->render('activity/list.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'activities' => $activities
        ]);
    }
}
