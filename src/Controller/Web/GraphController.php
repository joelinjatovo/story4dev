<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\User;
use App\Entity\File;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\Activity;
use App\Entity\Graph;
use App\Entity\Axe;
use App\Entity\Indicator;
use App\Entity\Result;
use App\Entity\ActivityFile;
use App\Entity\ProjectContribution;
use App\Entity\Meta\ProjectMeta;
use App\Form\ProjectType;
use App\Form\GraphType;
use App\Service\FormError;
use App\Service\PaginatorService;
use App\Helper\ProjectHelper;

/** 
 * @Route(name="graph_")
 *
 * @IsGranted("ROLE_USER") 
 */
class GraphController extends AbstractController
{
    /**
     * @Route("/p/{slug}/graph", name="index", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function index(Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();
        
        $graph = new Graph();
        $form = $this->createForm(GraphType::class, $graph);

        return $this->render('graph/create.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'graph'   => $graph, 
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/graph", name="create", methods="POST")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function create(Project $project, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();
        
        $graph = new Graph();
        $form = $this->createForm(GraphType::class, $graph);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            
            // set author
            $graph->setAuthor($this->getUser());
            foreach($graph->getAxes() as $axe){
                $axe->setAuthor($this->getUser());
            }
            
            $entityManager->persist($graph);
            $entityManager->flush();
        
            $this->addFlash('success', 'Graphe créé avec succès.');

            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-exit':
                    return $this->redirectToRoute('project_show', [
                        'slug' => $project->getSlug(), 
                    ]);
                case 'save-continue':
                    return $this->redirectToRoute('graph_show', [
                        'slug'     => $project->getSlug(), 
                        'graph_id' => $graph->getId(), 
                    ]);
                case 'save-edit':
                    return $this->redirectToRoute('graph_edit', [
                        'slug'     => $project->getSlug(), 
                        'graph_id' => $graph->getId(), 
                    ]);
                case 'save-create':
                case 'save-default':
                default:
                    return $this->redirectToRoute('graph_index', [
                        'slug' => $project->getSlug(), 
                    ]);
            }

        }
        
        $this->addFlash('error', 'Une erreur s\'est produite. Veuillez réessayer!');

        return $this->render('graph/create.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'graph'   => $graph, 
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/graph/{graph_id}", name="show", methods="GET", requirements={"graph_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("graph", options={"mapping": {"graph_id": "id"}})
     */
    public function show(Project $project, Graph $graph, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $graph);
        
        if($graph->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $entityManager = $this->getDoctrine()->getManager();
        $axes = $entityManager->getRepository(Axe::class)->findBy(['graph' => $graph]);
        
        $graphHelper = new \App\Helper\GraphHelper($entityManager);
        
        $selectedIndicators = $this->getIndicators($project, $request);
        if( empty( $selectedIndicators ) ) {
            $data   =  $graphHelper->getChartData($project, $axes, false);
            $series =  $graphHelper->getChartSeries($project);
        }else{
            $data   =  $graphHelper->getChartData($selectedIndicators, $axes, true);
            $series =  $graphHelper->getChartSeries($selectedIndicators);
        }
        
        if ( $request->isXmlHttpRequest() ) {
            return $this->json([
                'success' => true,
                'url'     => $request->getUri(),
                'chart'   => [
                    'data'   => $data,
                    'series' => $series,
                ]
            ]);
        }
        
        $indicators = $entityManager->getRepository(Indicator::class)->findByProject($project)->execute();
        
        return $this->render('graph/show.html.twig', [
            'user'       => $user,
            'project'    => $project,
            'graph'      => $graph, 
            'indicators' => $indicators,
            'chart'      => [
                'indicators' => $selectedIndicators,
                'data'       => $data,
                'series'     => $series,
            ]
        ]);
    }

    private function getIndicators(Project $project, Request $request){
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
     * @Route("/p/{slug}/graph/edit/{graph_id}", name="edit", methods="GET", requirements={"graph_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("graph", options={"mapping": {"graph_id": "id"}})
     */
    public function edit(Project $project, Graph $graph)
    {
        $this->denyAccessUnlessGranted('edit', $graph);
        
        if($graph->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(GraphType::class, $graph);

        return $this->render('graph/edit.html.twig', [
            'user'    => $user,
            'project' => $project,
            'graph'   => $graph, 
            'form'    => $form->createView() 
        ]);
    }
    
    /**
     * @Route("/p/{slug}/graph/edit/{graph_id}", name="update", methods="POST", requirements={"graph_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("graph", options={"mapping": {"graph_id": "id"}})
     */
    public function update(Project $project, Graph $graph, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $graph);
        
        if($graph->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        $originalAxes = new ArrayCollection();
        foreach ($graph->getAxes() as $axe) {
            $originalAxes->add($axe);
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(GraphType::class, $graph);
        
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $entityManager = $this->getDoctrine()->getManager();
            
            // remove the relationship
            foreach ($originalAxes as $axe) {
                $removed = true;
                foreach($graph->getAxes() as $updated_axe){
                    if ( ( $updated_axe->getId() > 0 ) && ($updated_axe->getId() === $axe->getId()) ) {
                        $removed = false;
                        break;
                    }
                }
                
                if($removed === true){
                    $graph->removeIteration($axe);
                    $entityManager->remove($iteration);
                }
            }
            
            // set author for new iteration
            foreach($graph->getAxes() as $updated_axe){
                if($updated_axe->getAuthor()==null){
                    $updated_axe->setAuthor($this->getUser());
                }
            }
            
            $entityManager->persist($graph);
            $entityManager->flush();
        
            $this->addFlash('success', 'Votre modification a été bien sauvegardé.');

            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-exit':
                    return $this->redirectToRoute('project_show', [
                        'slug' => $project->getSlug(), 
                    ]);
                case 'save-create':
                    return $this->redirectToRoute('graph_create', [
                        'slug' => $project->getSlug(), 
                    ]);
                case 'save-continue':
                    return $this->redirectToRoute('graph_show', [
                        'slug'     => $project->getSlug(), 
                        'graph_id' => $activity->getId(), 
                    ]);
                case 'save-edit':
                case 'save-default':
                default:
                    return $this->redirectToRoute('graph_edit', [
                        'slug'     => $project->getSlug(), 
                        'graph_id' => $graph->getId(), 
                    ]);

            }

            return $this->redirectToRoute('graph_edit', [
                'slug'     => $project->getSlug(), 
                'graph_id' => $graph->getId(), 
            ]);
        }

        $this->addFlash('error', 'Une erreur s\'est produite. Veuillez réessayer!');

        return $this->redirectToRoute('graph_edit', [
            'slug'     => $project->getSlug(), 
            'graph_id' => $graph->getId(), 
        ]);
    }
    
    /**
     * @Route("/graph/remove", name="remove", methods="POST")
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $graph = $entityManager->getRepository(Graph::class)->find($id);
                if( $graph ){
                    $this->denyAccessUnlessGranted('remove', $graph);

                    $entityManager->remove($graph);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Graphe supprimé avec succès',
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
     * @Route("/p/{slug}/graphes/{page<\d+>?1}", name="list", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function list(Project $project, $page = 1, PaginatorService $paginator)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $user = $project->getAuthor();

        $entityManager = $this->getDoctrine()->getManager();
        
        $order_by = $project->getMeta('graph_order_by', 'createdAt');
        $order    = $project->getMeta('graph_order', 'DESC');
        $query = $entityManager->getRepository(Graph::class)->findByProject($project, $order_by, $order);
        
        $graphes = $paginator->paginate($query, 10);

        return $this->render('graph/list.html.twig', [
            'user'    => $user,
            'project' => $project,
            'graphes' => $graphes
        ]);
    }
}
