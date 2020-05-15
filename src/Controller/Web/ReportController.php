<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

use App\Entity\User;
use App\Entity\Result;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\Activity;
use App\Entity\Iteration;
use App\Entity\Indicator;
use App\Entity\IndicatorFavorite;
use App\Form\ActivityType;
use App\Form\ProjectType;
use App\Form\ReportType;
use App\Form\ResultType;
use App\Service\PaginatorService;

/** 
 * @Route(name="report_")
 *
 * @IsGranted("ROLE_USER") 
 */
class ReportController extends BaseController
{
    /**
     * @Route("/p/{slug}/report", name="activities", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function activities(Project $project)
    {
        $this->denyAccessUnlessGranted('view', $project);

        $entityManager = $this->getDoctrine()->getManager();
        
        $activities = $entityManager->getRepository(Activity::class)->findBy(['project' => $project], ['title' => 'ASC']);
      
        return $this->render('report/activities.html.twig', [
            'project'  => $project, 
            'activities' => $activities
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}/report", name="index", methods="GET", requirements={"activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function index(Project $project, Activity $activity)
    {
        $this->denyAccessUnlessGranted('view', $activity);

        $entityManager = $this->getDoctrine()->getManager();
        
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $activity, 'user' => $this->getUser() ));
        
        return $this->render('report/create.html.twig', [
            'project'  => $project, 
            'report'   => $report,
            'activity' => $activity,
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/activity/{activity_id}/report", name="create", methods="POST", requirements={"activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function create(Project $project, Activity $activity, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view', $activity);

        $entityManager = $this->getDoctrine()->getManager();
        
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $activity, 'user' => $this->getUser() ));
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                $report->setAuthor($this->getUser());
                $report->setIp($request->getClientIp());
                foreach ($report->getResults() as $result) {
                    $result->setAuthor($this->getUser());
                    $entityManager->persist($result);
                }
                
                $entityManager->persist($report);
                $entityManager->flush();

                $this->addFlash('success', $this->trans('controller.report.created'));
            }else{
                $this->addFlash('error', $this->trans('controller.error.occured'));
            }
        }
        
        return $this->redirectToRoute('report_index', [
            'slug' => $project->getSlug(),
            'activity_id' => $activity->getId(),
        ]);
    }
    
    /**
     * @Route("/p/{slug}/report/{report_id}", name="show", methods="GET", requirements={"report_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function show(Project $project, Report $report, \App\Twig\AppExtension $twigExtension)
    {
        $this->denyAccessUnlessGranted('view', $report);
        
        return $this->render('report/show.html.twig', [
            'project'  => $project,
            'report'   => $report,
        ]);
    }
    
    /**
     * @Route("/p/{slug}/report/{report_id}/pdf", name="pdf", methods="GET", requirements={"report_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function pdf(Project $project, Report $report, \App\Twig\AppExtension $twigExtension, \Knp\Snappy\Pdf $knpSnappy)
    {
        $this->denyAccessUnlessGranted('view', $report);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('report/pdf.html.twig', [
            'project'  => $project,
            'report'   => $report,
        ]);

        return new PdfResponse(
            $knpSnappy->getOutputFromHtml($html),
            'file.pdf'
        );
    }
    
    /**
     * @Route("/p/{slug}/report/edit/{report_id}", name="edit", methods="GET", requirements={"report_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function edit(Project $project, Report $report)
    {
        $this->denyAccessUnlessGranted('edit', $report);
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $report->getActivity(), 'user' => $this->getUser() ));
        
        return $this->render('report/edit.html.twig', [
            'project'  => $project,
            'activity' => $report->getActivity(),
            'report'   => $report,
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/p/{slug}/report/edit/{report_id}", name="update", methods="POST", requirements={"report_id"="\d+"})
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function update(Project $project, Report $report, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $report);

        $entityManager = $this->getDoctrine()->getManager();
        
        $originalResults = new ArrayCollection();
        foreach ($report->getResults() as $result) {
            $originalResults->add($result);
        }
        
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $report->getActivity(), 'user' => $this->getUser() ));
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                // remove the relationship
                foreach ($originalResults as $result) {
                    $removed = true;
                    foreach($report->getResults() as $updated_result){
                        if ( ( $updated_result->getId() > 0 ) && ($updated_result->getId() === $result->getId()) ) {
                            $removed = false;
                            break;
                        }
                    }

                    if($removed === true){
                        $report->removeResult($result);
                        $entityManager->remove($result);
                    }
                }
            
                // set author for new iteration
                foreach($report->getResults() as $updated_result){
                    if($updated_result->getAuthor()==null){
                        $updated_result->setAuthor($this->getUser());
                    }
                }
                
                $entityManager->persist($report);
                
                $entityManager->flush();

                $this->addFlash('success', $this->trans('controller.report.updated'));
            }else{
                $this->addFlash('error', $this->trans('controller.error.occured'));
            }
        }
        
        return $this->redirectToRoute('report_edit', [
            'slug'  => $project->getSlug(),
            'report_id'   => $report->getId(), 
        ]);
    }
    
    /**
     * @Route("/report/status", name="status_change", methods="POST")
     */
    public function statusChange(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $status = $request->request->get('status');
            $id = $request->request->get('id');
            
            $entityManager = $this->getDoctrine()->getManager();
            $report = $entityManager->getRepository(Report::class)->find($id);
            if( $report ){
                $this->denyAccessUnlessGranted('edit', $report);
                
                if( $status == Report::STATUS_CLOSED ){
                    $report->setStatus( Report::STATUS_CLOSED );
                }else if( $status == Report::STATUS_TERMINATED ) {
                    $report->setStatus( Report::STATUS_TERMINATED );
                }else{
                    $status = Report::STATUS_OPENED;
                    $report->setStatus( Report::STATUS_OPENED );
                }
                $entityManager->persist($report);
                $entityManager->flush();
                
                return $this->json([
                    'success' => true,
                    'status'  => $report->getStatusLabel(),
                    'class'   => $report->getStatusClass(),
                    'message' => $this->trans('controller.report.status.updated'),
                ]);
            }
            
            return $this->json([
                'success' => false,
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
    
    /**
     * @Route("/report/remove", name="remove", methods="POST")
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $report = $entityManager->getRepository(Report::class)->find($id);
                if( $report && ! $report->isDeleted() ){
                    $this->denyAccessUnlessGranted('remove', $report);

                    $entityManager->remove($report);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.report.deleted'),
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
     * @Route("/p/{slug}/reports/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/p/{slug}/reports/{type}/{page<\d+>?1}", name="list_type", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     */
    public function list(Project $project, ?string $type, $page = 1, PaginatorService $paginator, Request $request, SessionInterface $session)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $search = $request->query->get('s');
        $status = $request->query->get('status');
        
        $order_by = $project->getMeta('report_order_by', 'createdAt');
        $order    = $project->getMeta('report_order', 'DESC');
        
        $entityManager = $this->getDoctrine()->getManager();

        $activity_id = (int) $request->query->get('activity_id');
        $activity = $entityManager->getRepository(Activity::class)->find($activity_id);
        if( $activity_id > 0 ){
            $activity = $entityManager->getRepository(Activity::class)->find($activity_id);

            if( ! $activity ){
                throw $this->createNotFoundException($this->trans('controller.not.found.activity'));
            }
            
            if($activity->getProject() != $project ){
                throw $this->createNotFoundException($this->trans('controller.error.not.matched.project'));
            }
            
            $this->denyAccessUnlessGranted('view', $activity);
            
            $query = $entityManager->getRepository(Report::class)
                    ->findByActivity($activity, [
                        'user'    => null,
                        'orderBy' => $order_by,
                        'order'   => $order,
                        'search'  => $search,
                        'status'  => $status
                    ]);
            
        }else{
            $this->denyAccessUnlessGranted('view', $project);
            
            $query = $entityManager->getRepository(Report::class)
                    ->findByProject($project, [
                        'user'    => null,
                        'orderBy' => $order_by,
                        'order'   => $order,
                        'search'  => $search,
                        'status'  => $status
                    ]);
        }
        
        $reports = $paginator->paginate($query, $project->getMeta('report_count', 20));

        if(empty($type)){
            $type = $session->get('list_type');
        }else{
            $session->set('list_type', $type);
        }
        
        $activities = $entityManager->getRepository(Activity::class)->findBy(['project' => $project], ['title' => 'ASC']);
        
        if($type == 'list'){
            return $this->render('report/list.html.twig', [
                'project'  => $project,
                'activity' => $activity,
                'activities' => $activities,
                'reports'  => $reports,
                'status'   => $status,
                'search'   => $search,
                'type'     => 'list'
            ]);
        }
        
        return $this->render('report/grid.html.twig', [
            'project'  => $project,
            'activity' => $activity,
            'activities' => $activities,
            'reports'  => $reports,
            'status'   => $status,
            'search'   => $search,
            'type'     => 'grid'
        ]);
    }
}
