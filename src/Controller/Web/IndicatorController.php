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
class IndicatorController extends AbstractController
{
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}/indicator", name="index", methods="GET", requirements={"activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function index(Project $project, Activity $activity)
    {
        $this->denyAccessUnlessGranted('create_indicator', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();

        $indicator = new Indicator();
        foreach($project->getIterations() as $iteration){
            $goal = new Goal();
            $goal->setValue(0);
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
     * @Route("/p/{slug}/activity/{activity_id}/indicator", name="create", methods="POST", requirements={"activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function create(Project $project, Activity $activity, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('create_indicator', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();

        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $indicator->setAuthor($this->getUser());
            
            foreach($indicator->getGoals() as $goal){
                $goal->setAuthor($this->getUser());
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($indicator);

            $entityManager->flush();
        
            $this->addFlash('success', 'L\'indicateur a été bien sauvegardé avec succès.');

            $args = [
                'slug'  => $project->getSlug(),
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
        
        $this->addFlash('error', "L'indicateur n'a pas été sauvegardé. Une erreur s'est produite.");

        return $this->redirectToRoute('indicator_index', [
            'slug'  => $project->getSlug(),
            'activity_id' => $activity->getId(), 
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}/indicator/{indicator_id}", name="show", methods="GET", requirements={"activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function show(Project $project, Activity $activity, Indicator $indicator, \App\Twig\AppExtension $twigExtension)
    {
        $this->denyAccessUnlessGranted('view', $indicator);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
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
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'form'      => $form->createView(),
            'chart'     => ['data' => $data],
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}/indicator/edit/{indicator_id}", name="edit", methods="GET", requirements={"activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function edit(Project $project, Activity $activity, Indicator $indicator)
    {
        $this->denyAccessUnlessGranted('edit', $indicator);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
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
            'activity'  => $activity, 
            'indicator' => $indicator, 
            'form'      => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}/indicator/edit/{indicator_id}", name="update", methods="POST", requirements={"activity_id"="\d+", "indicator_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function update(Project $project, Activity $activity, Indicator $indicator, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $indicator);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        if($indicator->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(IndicatorType::class, $indicator);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            foreach($indicator->getGoals() as $goal){
                $value = $goal->getValue();
                if( is_null($value) ) {
                    $goal->setValue(0);
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($indicator);
            $entityManager->flush();
        
            $this->addFlash('success', "l'indicateur a été bien modifié avec succès.");
            
            $args = [
                'slug'  => $project->getSlug(),
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
        
        $this->addFlash('error', "Les modifications n'ont pas été sauvegardée. Une erreur s'est produite. Veuillez réessayer!");

        return $this->redirectToRoute('indicator_edit', [
            'slug'  => $project->getSlug(),
            'activity_id'  => $activity->getId(),
            'indicator_id' => $indicator->getId()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}/indicators/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/p/{slug}/activity/{activity_id}/indicators/{type}/{page<\d+>?1}", name="list_type", methods="GET")
     * @Route("/p/{slug}/indicators/{page<\d+>?1}", name="list2", methods="GET")
     * @Route("/p/{slug}/indicators/{type}/{page<\d+>?1}", name="list2_type", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function list(Project $project, ?Activity $activity, ?string $type, $page = 1, PaginatorService $paginator, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        if($activity){
            if($activity->getProject() != $project ){
                throw $this->createNotFoundException('The project does not match');
            }
            
            $this->denyAccessUnlessGranted('view', $activity);
        }
        
        $user = $project->getAuthor();
        
        $entityManager = $this->getDoctrine()->getManager();
        
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
        
        if($type == 'list'){
            return $this->render('indicator/list.html.twig', [
                'user'       => $user,
                'project'    => $project,
                'activity'   => $activity,
                'indicators' => $indicators,
                'search' => $search,
            ]);
        }
        
        return $this->render('indicator/grid.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'activity'   => $activity,
            'indicators' => $indicators,
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
                        'message' => 'L\'indicateur a été supprimé avec succès',
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
     * @Route("/indicator/star", name="star", methods="POST")
     */
    public function star(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $indicator = $entityManager->getRepository(Indicator::class)->find($id);
                if( $indicator ){
                    $this->denyAccessUnlessGranted('star', $indicator);

                    $item = $entityManager
                        ->getRepository(IndicatorFavorite::class)
                        ->findOneBy([
                            'user'     => $this->getUser(),
                            'indicator' => $indicator,
                        ]);
                    if($item){
                        $entityManager->remove($item);
                        $message = 'L\'indicateur a été supprimé de votre favoris avec succès';
                        $star = false;
                    }else{
                        $item = new IndicatorFavorite();
                        $item->setUser($this->getUser());
                        $item->setIndicator($indicator);
                        $entityManager->persist($item);
                        $message = 'L\'indicateur a été ajouté dans votre favoris avec succès';
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
}
