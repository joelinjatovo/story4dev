<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


use App\Entity\User;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\ActivityFavorite;
use App\Entity\Indicator;
use App\Entity\Iteration;
use App\Entity\Report;
use App\Entity\File;
use App\Form\ActivityType;
use App\Form\IndicatorType;
use App\Service\FormError;
use App\Service\PaginatorService;
use App\Twig\AppExtension;

/** 
 * @Route(name="activity_")
 *
 * @IsGranted("ROLE_USER") 
 */
class ActivityController extends AbstractController
{
    /**
     * @Route("/p/{slug}/activity", name="index", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function index(Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();
        
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity, array( 'project' => $project));

        return $this->render('activity/create.html.twig', [
            'user'     => $user, 
            'project'  => $project, 
            'activity' => $activity, 
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity", name="create", methods="POST")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function create(Project $project, Request $request, FormError $formError): Response
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();
        
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity, array( 'project' => $project));
        
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
                        'slug' => $project->getSlug(), 
                    ]);
                case 'save-continue':
                    return $this->redirectToRoute('activity_show', [
                        'slug' => $project->getSlug(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-edit':
                    return $this->redirectToRoute('activity_edit', [
                        'slug' => $project->getSlug(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-create':
                case 'save-default':
                default:
                    return $this->redirectToRoute('activity_index', [
                        'slug' => $project->getSlug(), 
                    ]);
            }

        }
        
        $this->addFlash('error', 'Une erreur s\'est produite. Veuillez réessayer!');

        return $this->render('activity/create.html.twig', [
            'user'     => $user, 
            'project'  => $project, 
            'activity' => $activity, 
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}", name="show", methods="GET", requirements={"activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function show(Project $project, Activity $activity, \App\Twig\AppExtension $twigExtension, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $iterations = $entityManager->getRepository(Iteration::class)->findBy(['project' => $project]);
        
        $selectedIndicators = $this->getIndicators($activity, $request);
        if( empty( $selectedIndicators ) ) {
            $data   =  $twigExtension->getChartData($activity, $iterations, false);
            $series =  $twigExtension->getChartSeries($activity);
        }else{
            $data   =  $twigExtension->getChartData($selectedIndicators, $iterations, true);
            $series =  $twigExtension->getChartSeries($selectedIndicators);
        }
        
        if ( $request->isXmlHttpRequest() ) {
            return $this->json([
                'success' => true,
                'url'     => $request->getUri(),
                'chart'      => [
                    'data'   => $data,
                    'series' => $series,
                ]
            ]);
        }
        
        $reports    = $entityManager->getRepository(Report::class)->findByActivity($activity, ['user' => null, 'orderBy' => $project->getMeta('report_order_by', 'createdAt'), 'order' => $project->getMeta('report_order', 'DESC'), 'limit' => $project->getMeta('report_count', 10) ] )->execute();
        $documents  = $entityManager->getRepository(File::class)->findByActivity($activity, ['type' => 'document', 'limit' => 10])->getResult();
        $images     = $entityManager->getRepository(File::class)->findByActivity($activity, ['type' => 'image', 'limit' => 50])->getResult();
        $indicators = $entityManager->getRepository(Indicator::class)->findBy(['activity' => $activity]);
        
        return $this->render('activity/show.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'activity'  => $activity, 
            'reports'   => $reports, 
            'documents' => $documents, 
            'images'    => $images,
            'indicators'    => $indicators,
            'chart'      => [
                'indicators' => $selectedIndicators,
                'data'       => $data,
                'series'     => $series,
            ]
        ]);
    }

    private function getIndicators(Activity $activity, Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $indicators = [];

        $ids = $request->query->get('indicators');
        if( is_array( $ids ) && ! empty( $ids ) ) {
            foreach($ids as $id ){
                $indicator = $entityManager->getRepository(Indicator::class)->find((int) $id);
                if( $indicator ) {
                    $indicators[] = $indicator;
                }
            }
        }
        
        return $indicators;
    }
    
    /**
     * @Route("/p/{slug}/activity/edit/{activity_id}", name="edit", methods="GET", requirements={"activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function edit(Project $project, Activity $activity)
    {
        $this->denyAccessUnlessGranted('edit', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(ActivityType::class, $activity, array( 'project' => $project));

        return $this->render('activity/edit.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity, 
            'form'     => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/edit/{activity_id}", name="update", methods="POST", requirements={"activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function update(Project $project, Activity $activity, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(ActivityType::class, $activity, array( 'project' => $project));
        
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
                        'slug' => $project->getSlug(), 
                    ]);
                case 'save-create':
                    return $this->redirectToRoute('activity_create', [
                        'slug' => $project->getSlug(), 
                    ]);
                case 'save-continue':
                    return $this->redirectToRoute('activity_show', [
                        'slug' => $project->getSlug(), 
                        'activity_id' => $activity->getId(), 
                    ]);
                case 'save-edit':
                case 'save-default':
                default:
                    return $this->redirectToRoute('activity_edit', [
                        'slug' => $project->getSlug(), 
                        'activity_id' => $activity->getId(), 
                    ]);

            }

            return $this->redirectToRoute('activity_edit', [
                'slug' => $project->getSlug(), 
                'activity_id' => $activity->getId(), 
            ]);
        }

        $this->addFlash('error', 'Une erreur s\'est produite. Veuillez réessayer!');

        return $this->redirectToRoute('activity_edit', [
            'slug' => $project->getSlug(), 
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
     * @Route("/activity/star", name="star", methods="POST")
     */
    public function star(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $activity = $entityManager->getRepository(Activity::class)->find($id);
                if( $activity ){
                    $this->denyAccessUnlessGranted('star', $activity);

                    $item = $entityManager
                        ->getRepository(ActivityFavorite::class)
                        ->findOneBy([
                            'user'     => $this->getUser(),
                            'activity' => $activity,
                        ]);
                    if($item){
                        $entityManager->remove($item);
                        $message = 'Activité supprimée de votre favoris avec succès';
                        $star = false;
                    }else{
                        $item = new ActivityFavorite();
                        $item->setUser($this->getUser());
                        $item->setActivity($activity);
                        $entityManager->persist($item);
                        $message = 'Activité ajoutée dans votre favoris avec succès';
                        $star = true;
                    }
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'star'    => $star,
                        'message' => $message,
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
     * @Route("/p/{slug}/activities/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/p/{slug}/activities/{type}/{page<\d+>?1}", name="list_type", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function list(Project $project, ?string $type, $page = 1, PaginatorService $paginator, SessionInterface $session)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $user = $project->getAuthor();

        $entityManager = $this->getDoctrine()->getManager();
        
        $order_by = $project->getMeta('activity_order_by', 'createdAt');
        $order    = $project->getMeta('activity_order', 'DESC');
        $query = $entityManager->getRepository(Activity::class)->findByProject($project, $order_by, $order);
        
        $activities = $paginator->paginate($query, $project->getMeta('activity_count', 20));

        if(empty($type)){
            $type = $session->get('list_type');
        }else{
            $session->set('list_type', $type);
        }
        
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
