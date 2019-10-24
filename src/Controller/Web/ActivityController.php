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
use App\Entity\Report;
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
     * @Route("/project/{project_id}/activity", name="index", methods="GET", requirements={"project_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function index(Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();
        
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);

        return $this->render('activity/create.html.twig', [
            'user'     => $user, 
            'project'  => $project, 
            'activity' => $activity, 
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity", name="create", methods="POST", requirements={"project_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function create(Project $project, Request $request, FormError $formError): Response
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();
        
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
        
            $this->addFlash('success', 'Activité créée avec succès.');

            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-exit':
                    return $this->redirectToRoute('project_show', [
                        'id'   => $project->getId(), 
                    ]);
                case 'save-continue':
                    return $this->redirectToRoute('activity_show', [
                        'project_id'  => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-edit':
                    return $this->redirectToRoute('activity_edit', [
                        'project_id'  => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-create':
                case 'save-default':
                default:
                    return $this->redirectToRoute('activity_index', [
                        'project_id' => $project->getId(), 
                    ]);
            }

        }
        
        $this->addFlash('error', 'Une erreur s\'est produite. Veuillez réessayer!');

        return $this->redirectToRoute('activity_index', [
            'project_id' => $project->getId(),
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity/{activity_id}", name="show", methods="GET", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function show(Project $project, Activity $activity)
    {
        $this->denyAccessUnlessGranted('view', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $entityManager = $this->getDoctrine()->getManager();
        $reports = $entityManager->getRepository(Report::class)->findByActivity($activity)->execute();

        $data   =  $activity->getData();
        $series =  $activity->getSerie();
        
        return $this->render('activity/show.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity, 
            'reports'  => $reports, 
            'data'     => json_encode($data),
            'series'   => json_encode($series),
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity/edit/{activity_id}", name="edit", methods="GET", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function edit(Project $project, Activity $activity)
    {
        $this->denyAccessUnlessGranted('edit', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(ActivityType::class, $activity);

        return $this->render('activity/edit.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity, 
            'form'     => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity/edit/{activity_id}", name="update", methods="POST", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function update(Project $project, Activity $activity, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(ActivityType::class, $activity);
        
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();
        
            $this->addFlash('success', 'Votre modification a été bien sauvegardé.');

            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-exit':
                    return $this->redirectToRoute('project_show', [
                        'id'   => $project->getId(), 
                    ]);
                case 'save-create':
                    return $this->redirectToRoute('activity_create', [
                        'project_id'  => $project->getId(), 
                    ]);
                case 'save-continue':
                    return $this->redirectToRoute('activity_show', [
                        'project_id'  => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-edit':
                case 'save-default':
                default:
                    return $this->redirectToRoute('activity_edit', [
                        'project_id' => $project->getId(), 
                        'activity_id' => $activity->getId(), 
                    ]);

            }

            return $this->redirectToRoute('activity_edit', [
                'project_id'  => $project->getId(), 
                'activity_id' => $activity->getId(), 
            ]);
        }

        $this->addFlash('error', 'Une erreur s\'est produite. Veuillez réessayer!');

        return $this->redirectToRoute('activity_edit', [
            'project_id'  => $project->getId(), 
            'activity_id' => $activity->getId(), 
        ]);
    }
    
    /**
     * @Route("/activity/remove", name="remove", methods="POST")
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $activity = $entityManager->getRepository(Activity::class)->find($id);
                if( $activity  && ! $activity->isDeleted()){
                    $this->denyAccessUnlessGranted('remove', $activity);

                    $entityManager->remove($activity);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Activité supprimé avec succès',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
    
    /**
     * @Route("/project/{project_id}/activities/{page<\d+>?1}", name="list", methods="GET", requirements={"project_id"="\d+"})
     * @Route("/project/{project_id}/activities/{type}/{page<\d+>?1}", name="list_type", methods="GET", requirements={"project_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(Project $project, ?string $type, $page = 1, PaginatorService $paginator)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $user = $project->getAuthor();

        $entityManager = $this->getDoctrine()->getManager();
        
        $query = $entityManager->getRepository(Activity::class)->findByProject($project);
        
        $activities = $paginator->paginate($query, 10);

        if($type == 'list'){
            return $this->render('activity/list.html.twig', [
                'user'       => $user,
                'project'    => $project,
                'activities' => $activities
            ]);
        }
        
        return $this->render('activity/grid.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'activities' => $activities
        ]);
    }
}
