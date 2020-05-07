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
use App\Entity\Indicator;
use App\Entity\Iteration;
use App\Entity\Result;
use App\Entity\ReportFile;
use App\Entity\ActivityFile;
use App\Entity\ProjectContribution;
use App\Entity\Meta\ProjectMeta;
use App\Form\ProjectType;
use App\Service\FormError;
use App\Service\PaginatorService;
use App\Helper\ProjectHelper;

/** 
 * @Route(name="project_")
 *
 * @IsGranted("ROLE_USER") 
 */
class ProjectController extends AbstractController
{
    const RECENT_ITEMS_COUNT = 10;
    
    /**
     * @Route("/p/{slug}/project", name="index", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function index(Project $project)
    {
        $user = $this->getUser();
        
        $_project = $project->duplicate();
        $_project->setParent($project); // Set as child project
        $form = $this->createForm(ProjectType::class, $_project);
        
        return $this->render('project/create.html.twig', [
            'user' => $user,
            'project' => $project,
            '_project' => $_project,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/project", name="create", methods="POST")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function create(Request $request, Project $project)
    {
        $user = $this->getUser();
        
        $_project = $project->duplicate(); 
        $form = $this->createForm(ProjectType::class, $_project);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if ( $form->isValid() ) {
                
                $_project->setParent($project); // Set as child project

                if( $_project->getAuthor() == null ) {
                    $_project->setAuthor( $this->getUser() );
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($_project);

                if( $project->getAuthor() ) {
                    $contribution = new ProjectContribution();
                    $contribution->setUser( $project->getAuthor() );
                    $contribution->setProject( $_project );
                    $contribution->setStatus( ProjectContribution::STATUS_ACTIVE );
                    $contribution->setRoles(['ROLE_ADMIN']);
                    $entityManager->persist( $contribution );
                }
                
                if( $project->getAuthor() != $_project->getAuthor()) {
                    $contribution = new ProjectContribution();
                    $contribution->setUser( $_project->getAuthor() );
                    $contribution->setProject( $_project );
                    $contribution->setStatus( ProjectContribution::STATUS_ACTIVE );
                    $contribution->setRoles(['ROLE_ADMIN']);
                    $entityManager->persist( $contribution );
                }

                $entityManager->flush();

                $this->addFlash('success', 'Sous projet créé avec succès.');
            }else{
                $this->addFlash('error', 'Une erreur s\'est produite.');
            }
        }

        return $this->redirectToRoute('project_index', [
            'slug'  => $project->getSlug(),
        ]);
    }
    
    /**
     * @Route("/p/{slug}/dashboard", name="dashboard", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function dashboard(Project $project, \App\Twig\AppExtension $twigExtension, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $user = $project->getAuthor();
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $iterations = $entityManager->getRepository(Iteration::class)->findBy(['project' => $project]);
        
        $selectedIndicators = $this->getIndicators($project, $request);
        if( empty( $selectedIndicators ) ) {
            $data   =  $twigExtension->getChartData($project, $iterations, false);
            $series =  $twigExtension->getChartSeries($project);
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
        
        $count = [];
        $count['activities'] = $entityManager->getRepository(Activity::class)->createQueryBuilder('a')->select('count(a.id)')->where('a.project = :project')->setParameter('project', $project)->getQuery()->getSingleScalarResult();
        $count['reports']    = $entityManager->getRepository(Report::class)->createQueryBuilder('r')->select('count(r.id)')->leftJoin('r.activity', 'a')->where('a.project = :project')->setParameter('project', $project)->getQuery()->getSingleScalarResult();
        $count['indicators'] = $entityManager->getRepository(Indicator::class)->createQueryBuilder('i')->select('count(i.id)')->leftJoin('i.activity', 'a')->where('a.project = :project')->setParameter('project', $project)->getQuery()->getSingleScalarResult();
        $count['files']      = $entityManager->getRepository(File::class)->countByProject($project)->getSingleScalarResult();
        
        $indicators = $entityManager->getRepository(Indicator::class)->findByProject($project)->execute();
        
        return $this->render('project/dashboard.html.twig', [
            'user'       => $user, 
            'project'    => $project,
            'count'      => $count,
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
     * @Route("/p/{slug}/", name="show", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function show(Project $project)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $user = $project->getAuthor();

        $entityManager = $this->getDoctrine()->getManager();
        $activities    = $entityManager->getRepository(Activity::class)->findByProject($project, $project->getMeta('activity_order_by', 'createdAt'), $project->getMeta('activity_order', 'DESC'), $project->getMeta('activity_count', self::RECENT_ITEMS_COUNT))->execute();
        $reports       = $entityManager->getRepository(Report::class)->findByProject($project, ['user' => null, 'orderBy' => $project->getMeta('report_order_by', 'createdAt'), 'order' => $project->getMeta('report_order', 'DESC'), 'limit' => $project->getMeta('report_count', self::RECENT_ITEMS_COUNT) ] )->execute();
        $contributions = $entityManager->getRepository(ProjectContribution::class)->findByProject($project, $project->getMeta('contribution_order_by', 'createdAt'), $project->getMeta('contribution_order', 'DESC'), $project->getMeta('contribution_count', self::RECENT_ITEMS_COUNT))->execute();
        $reports_count = $entityManager->getRepository(Report::class)->countByProject($project);
        
        return $this->render('project/show.html.twig', [
            'user'          => $user, 
            'project'       => $project, 
            'recent'        => [
                'activities'    => $activities,
                'reports'       => $reports,
                'contributions' => $contributions,
            ],
            'reports_count' => $reports_count,  
        ]);
    }
    
    /**
     * @Route("/p/{slug}/edit", name="edit", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function edit(Project $project, ProjectHelper $projectHelper)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();

        $fields = $projectHelper->getMetaFields($project);
        
        // remove admin only fields
        if( ! $this->isGranted('ROLE_ADMIN') ) {
            foreach($fields as $key => $value ){
                if( isset( $value['admin_only'] ) && $value['admin_only'] ) {
                    unset($fields[$key]);
                }
            }
        }
        
        $form = $this->createForm(ProjectType::class, $project, ['fields' => $fields]);
        
        return $this->render('project/edit.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            '_project' => $project, 
            'fields'  => $fields,
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/edit", name="update", methods="POST")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function update(Project $project, Request $request, FormError $formError, ProjectHelper $projectHelper)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();

        $originalIterations = new ArrayCollection();
        foreach ($project->getIterations() as $iteration) {
            $originalIterations->add($iteration);
        }
        
        $fields = $projectHelper->getMetaFields($project);
        
        // remove admin only fields
        if( ! $this->isGranted('ROLE_ADMIN') ) {
            foreach($fields as $key => $value ){
                if( isset( $value['admin_only'] ) && $value['admin_only'] ) {
                    unset($fields[$key]);
                }
            }
        }
        
        $form = $this->createForm(ProjectType::class, $project, ['fields' => $fields]);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if ( $form->isValid() ) {

                $entityManager = $this->getDoctrine()->getManager();

                // remove the relationship
                foreach ($originalIterations as $iteration) {
                    $removed = true;
                    foreach($project->getIterations() as $updated_iteration){
                        if ( ( $updated_iteration->getId() > 0 ) && ($updated_iteration->getId() === $iteration->getId()) ) {
                            $removed = false;
                            break;
                        }
                    }

                    if($removed === true){
                        if( ! $iteration->hasGoals() ) {
                            $project->removeIteration($iteration);
                            $entityManager->remove($iteration);
                        }else{
                            $project->addIteration($iteration);
                            $this->addFlash('error', 'On ne peut pas supprimer l\'itération suivante: '.$iteration->getTitle());
                        }
                    }
                }

                // set author for new iteration
                foreach($project->getIterations() as $updated_iteration){
                    if($updated_iteration->getAuthor()==null){
                        $updated_iteration->setAuthor($this->getUser());
                    }
                }

                // save project meta data
                try{
                    foreach($fields as $group => $metas){
                        $postValues = $form[$group]->getData();
                        foreach($postValues as $key => $queryMetas){
                            if(is_array($queryMetas)){
                                foreach($queryMetas as  $postKey => $metavalue){
                                    $metakey = $key.'_'.$postKey;
                                    $meta = $project->updateMeta($metakey, $metavalue);

                                    $entityManager->persist($meta);
                                }
                            }else{
                                $metakey = $group.'_'.$key;
                                $meta = $project->updateMeta($metakey, $queryMetas);
                                $entityManager->persist($meta);
                            }
                        }
                    }
                }catch(\Exception $e){
                }

                $entityManager->persist($project);
                $entityManager->flush();

                $this->addFlash('success', 'Votre modification a été bien sauvegardé.');

            }else{
                $this->addFlash('error', 'Votre modification n\'a pas été sauvegardé. Une erreur s\'est produite. ' . $form->getErrors());
            }
        }

        return $this->redirectToRoute('project_edit', [
            'slug'  => $project->getSlug(),
        ]);
    }
    
    /**
     * @Route("/project/remove", name="remove", methods="POST")
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $project = $entityManager->getRepository(Project::class)->find($id);
                if( $project && ! $project->isDeleted()){
                    $this->denyAccessUnlessGranted('remove', $project);
                    
                    $entityManager->remove($project);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => 'Projet supprimé avec succès',
                    ]);
                }
            }
        }
            
        return $this->json([
            'success' => false,
            'title'   => 'Invalid Request',
            'message' => 'An error was occured. :)',
        ]);
    }
    
    /**
     * @Route("/projects/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/u/{slug}/projects/{page<\d+>?1}", name="list2", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Route("/p/{slug}/projects/{page<\d+>?1}", name="list_child", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function list(?User $user = null, ?Project $project = null, PaginatorService $paginator, int $page)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        if($project){
            $query = $entityManager->getRepository(Project::class)->findByParent($project);
        }elseif( $user && ( $this->isGranted('ROLE_ADMIN') || ( $user == $this->getUser() ) ) ) {
            $query = $entityManager->getRepository(Project::class)->findByContributor($user);
        }else{
            $query = $entityManager->getRepository(Project::class)->findByContributor($this->getUser());
        }

        $projects = $paginator->paginate($query, 10);
        
        return $this->render('project/list.html.twig', [
            'user' => $user,
            'project' => $project, 
            'projects' => $projects, 
        ]);
    }
    
    /**
     * @Route("/project/color", name="color", methods="POST")
     */
    public function color(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $metakey = $request->request->get('metakey');
            $color = $request->request->get('color');
            $id = $request->request->get('id');
            
            $entityManager = $this->getDoctrine()->getManager();
            $project = $entityManager->getRepository(Project::class)->find($id);
            if( $project ){
                $this->denyAccessUnlessGranted('edit', $project);

                $found = false;
                foreach($project->getMetas() as $meta){
                    if($meta->getMetaKey() === $metakey ){
                        $found = true;
                        break;
                    }
                }

                if( ! $found || ! $meta ) {
                    $meta = new ProjectMeta();
                    $meta->setProject($project);
                    $meta->setMetaKey($metakey);
                }

                $meta->setMetaValue($color);

                $entityManager->persist($meta);
                $entityManager->flush();
                
                return $this->json([
                    'success' => true,
                    'metakey' => $metakey,
                    'color'   => $color,
                    'message' => 'Color changed',
                ]);
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
}
