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
use App\Entity\Indicator;
use App\Entity\Iteration;
use App\Entity\IndicatorFavorite;
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
class IndicatorController extends BaseController
{
    
    /**
     * @Route("/p/{slug}/indicator", name="index", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function index(Project $project)
    {
        //$this->denyAccessUnlessGranted('create_indicator', $activity);
        
        $user = $project->getAuthor();

        $indicator = new Indicator();
        foreach($project->getIterations() as $iteration){
            $goal = new Goal();
            $goal->setValue(0);
            $goal->setAuthor($this->getUser());
            $goal->setIteration($iteration);
            $indicator->addGoal($goal);
        }

        $form = $this->createForm(IndicatorType::class, $indicator, ['project' => $project]);

        return $this->render('indicator/create.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'indicator' => $indicator,
            'form'      => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/p/{slug}/indicator", name="create", methods="POST")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function create(Project $project, Request $request, FormError $formError)
    {
        //$this->denyAccessUnlessGranted('create_indicator', $activity);
        
        $user = $project->getAuthor();

        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator, ['project' => $project]);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted()) {
            if ( $form->isValid() ) {
                $indicator->setAuthor($this->getUser());

                foreach($indicator->getGoals() as $goal){
                    $goal->setAuthor($this->getUser());
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($indicator);

                $entityManager->flush();

                $this->addFlash('success', $this->trans('controller.indicator.created'));
            }else{
                $this->addFlash('error', $this->trans('controller.error.occured'));
            }
        }

        return $this->redirectToRoute('indicator_index', [
            'slug'  => $project->getSlug(),
        ]);
    }
    
    /**
     * @Route("/p/{slug}/indicator/{indicator_id}", name="show", methods="GET", requirements={"indicator_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function show(Project $project, Indicator $indicator, \App\Twig\AppExtension $twigExtension)
    {
        $this->denyAccessUnlessGranted('view', $indicator);
        
        $user = $project->getAuthor();
        
        $goal = new Goal();
        $form = $this->createForm(GoalType::class, $goal);
        
        $entityManager = $this->getDoctrine()->getManager();
        $iterations = $entityManager->getRepository(Iteration::class)->findBy(['project' => $project]);
        $data   =  $twigExtension->getChartData($indicator, $iterations, true);
        $series =  $twigExtension->getChartSeries($indicator);
        
        return $this->render('indicator/show.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'indicator' => $indicator, 
            'form'      => $form->createView(),
            'chart'     => ['data' => $data],
        ]);
    }
    
    /**
     * @Route("/p/{slug}/indicator/edit/{indicator_id}", name="edit", methods="GET", requirements={"indicator_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function edit(Project $project, Indicator $indicator)
    {
        $this->denyAccessUnlessGranted('edit', $indicator);
        
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Goal::class);
        
        $user = $project->getAuthor();
        
        foreach($project->getIterations() as $iteration){
            $goal = $repository->findOneBy(['iteration' => $iteration, 'indicator' => $indicator]);
            if( is_null( $goal ) ) {
                $goal = new Goal();
                $goal->setValue(0);
                $goal->setAuthor($this->getUser());
                $goal->setIteration($iteration);
                $indicator->addGoal($goal);
            }
        }
        $form = $this->createForm(IndicatorType::class, $indicator);
        
        return $this->render('indicator/edit.html.twig', [
            'user'      => $user,
            'project'   => $project,
            'indicator' => $indicator, 
            'form'      => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/p/{slug}/indicator/edit/{indicator_id}", name="update", methods="POST", requirements={"indicator_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function update(Project $project, Indicator $indicator, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $indicator);
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(IndicatorType::class, $indicator);

        $form->handleRequest($request);

        if ( $form->isSubmitted() ) {
            if ( $form->isValid() ) {
                foreach($indicator->getGoals() as $goal){
                    $value = $goal->getValue();
                    if( is_null($value) ) {
                        $goal->setValue(0);
                    }
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($indicator);
                $entityManager->flush();

                $this->addFlash('success', $this->trans('controller.indicator.updated'));
            }else{
                $this->addFlash('error', $this->trans('controller.error.occured'));
            }
        }

        return $this->redirectToRoute('indicator_edit', [
            'slug'  => $project->getSlug(),
            'indicator_id' => $indicator->getId()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/indicators/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/p/{slug}/indicators/{type}/{page<\d+>?1}", name="list_type", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function list(Project $project, ?string $type, $page = 1, PaginatorService $paginator, Request $request, SessionInterface $session)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $activity_id = (int) $request->query->get('activity_id');
        $activity = $entityManager->getRepository(Activity::class)->find($activity_id);
        if($activity){
            if($activity->getProject() != $project ){
                throw $this->createNotFoundException($this->trans('controller.error.not.matched.project'));
            }
            
            $this->denyAccessUnlessGranted('view', $activity);
        }
        
        $user = $project->getAuthor();
        
        $search = $request->query->get('s');
        if( strlen($search) > 20 ) {
            $search = substr($search, 0, 20);
        }
        
        if($activity){
            $query = $entityManager->getRepository(Indicator::class)->findByActivity($activity, $search);
        }else{
            $query = $entityManager->getRepository(Indicator::class)->findByProject($project, $search);
        }
        
        $indicators = $paginator->paginate($query, $project->getMeta('indicator_count', 20));
        
        if(empty($type)){
            $type = $session->get('list_type');
        }else{
            $session->set('list_type', $type);
        }
        
        $activities = $entityManager->getRepository(Activity::class)->findBy(['project' => $project], ['title' => 'ASC']);
        if($type == 'list'){
            return $this->render('indicator/list.html.twig', [
                'user'       => $user,
                'project'    => $project,
                'activity'   => $activity,
                'indicators' => $indicators,
                'activities' => $activities,
                'search' => $search,
            ]);
        }
        
        return $this->render('indicator/grid.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'activity'   => $activity,
            'indicators' => $indicators,
            'activities' => $activities,
            'search' => $search,
        ]);
    }
    
    /**
     * @Route("/indicator/remove", name="remove", methods="POST")
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $indicator = $entityManager->getRepository(Indicator::class)->find($id);
                if( $indicator && ! $indicator->isDeleted()){
                    $this->denyAccessUnlessGranted('remove', $indicator);

                    $entityManager->remove($indicator);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.indicator.deleted'),
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
    
    /**
     * @Route("/indicator/star", name="star", methods="POST")
     */
    public function star(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $indicator = $entityManager->getRepository(Indicator::class)->find($id);
                if( $indicator ) {
                    $this->denyAccessUnlessGranted('star', $indicator);

                    $item = $entityManager
                        ->getRepository(IndicatorFavorite::class)
                        ->findOneBy([
                            'user'     => $this->getUser(),
                            'indicator' => $indicator,
                        ]);
                    if($item){
                        $entityManager->remove($item);
                        $message = $this->trans('controller.indicator.unstared');
                        $star = false;
                    }else{
                        $item = new IndicatorFavorite();
                        $item->setUser($this->getUser());
                        $item->setIndicator($indicator);
                        $entityManager->persist($item);
                        $message = $this->trans('controller.indicator.stared');
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
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
}
