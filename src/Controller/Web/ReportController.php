<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\User;
use App\Entity\Result;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\Activity;
use App\Entity\Iteration;
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
class ReportController extends AbstractController
{
    /**
     * @Route("/project/{project_id}/activity/{activity_id}/report", name="index", methods="GET", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function index(Project $project, Activity $activity)
    {
        $this->denyAccessUnlessGranted('view', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();
        
        $report = new Report();
        $report->setActivity($activity);
        $report->setAuthor($this->getUser());
        
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $activity ));
        
        return $this->render('report/create.html.twig', [
            'user'     => $user, 
            'project'  => $project, 
            'activity' => $activity,
            'report'   => $report,
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity/{activity_id}/report", name="create", methods="POST", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function create(Project $project, Activity $activity, Request $request): Response
    {
        $this->denyAccessUnlessGranted('view', $activity);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();

        $report = new Report();
        
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $activity ));
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                
                $report->setActivity($activity);
                $report->setAuthor($this->getUser());
                $report->setIp($request->getClientIp());
                
                foreach ($report->getResults() as $result) {
                    $result->setAuthor($this->getUser());
                    $entityManager->persist($result);
                }
                
                $entityManager->persist($report);
                $entityManager->flush();

                $this->addFlash('success', 'Votre rapport a été bien enregistré.');
                
                $action = strtolower( $request->request->get('submit') );
                switch($action){
                    case 'save-exit':
                        return $this->redirectToRoute('activity_show', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                        ]);
                    case 'save-continue':
                        return $this->redirectToRoute('report_show', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                            'report_id'   => $report->getId(), 
                        ]);
                    case 'save-edit':
                        return $this->redirectToRoute('report_edit', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                            'report_id'   => $report->getId(), 
                        ]);
                    case 'save-create':
                    case 'save-default':
                    default:
                        return $this->redirectToRoute('report_index', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                        ]);
                }
            }else{
                $this->addFlash('error', "Votre rapport n'a pas été enregistré. Veuillez réessayer!");
            }
        }
        
        return $this->redirectToRoute('report_index', [
            'project_id'  => $project->getId(), 
            'activity_id' => $activity->getId(), 
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity/{activity_id}/report/{report_id}", name="show", methods="GET", requirements={"project_id"="\d+","report_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function show(Project $project, Activity $activity, Report $report)
    {
        $this->denyAccessUnlessGranted('view', $report);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $user = $project->getAuthor();

        if($report->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $data =  $activity->getData();
        
        return $this->render('report/show.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity,
            'report'   => $report,
            'data'     => json_encode($data)
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity/{activity_id}/report/edit/{report_id}", name="edit", methods="GET", requirements={"project_id"="\d+","id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function edit(Project $project, Activity $activity, Report $report)
    {
        $this->denyAccessUnlessGranted('edit', $report);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        if($report->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $user = $project->getAuthor();
        
        $form = $this->createForm(ReportType::class, $report, array('activity' => $activity));
        
        return $this->render('report/edit.html.twig', [
            'user'     => $user, 
            'project'  => $project, 
            'activity' => $activity,
            'report'   => $report, 
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/project/{project_id}/activity/{activity_id}/report/edit/{report_id}", name="update", methods="POST", requirements={"project_id"="\d+","id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function update(Project $project, Activity $activity, Report $report, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $report);
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        if($report->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $user = $project->getAuthor();
        
        $originalResults = new ArrayCollection();
        foreach ($report->getResults() as $result) {
            $originalResults->add($result);
        }
        
        $form = $this->createForm(ReportType::class, $report, array('activity' => $activity));
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                
                $report->setActivity($activity);
                
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

                $this->addFlash('success', 'Votre rapport a été bien modifié avec succès.');
                
                $action = strtolower( $request->request->get('submit') );
                switch($action){
                    case 'save-exit':
                        return $this->redirectToRoute('activity_show', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                        ]);
                    case 'save-continue':
                    case 'save-edit':
                        return $this->redirectToRoute('report_edit', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                            'report_id'   => $report->getId(), 
                        ]);
                    case 'save-create':
                        return $this->redirectToRoute('report_index', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                        ]);
                    case 'save-default':
                    default:
                        return $this->redirectToRoute('report_show', [
                            'project_id'  => $project->getId(), 
                            'activity_id' => $activity->getId(), 
                            'report_id'   => $report->getId(), 
                        ]);
                }
            }else{
                $this->addFlash('error', "Votre rapport n'a pas été modifié. Une erreur s'est produite.");
            }
        }
        
        return $this->redirectToRoute('report_edit', [
            'project_id'  => $project->getId(), 
            'activity_id' => $activity->getId(),
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
                    'message' => 'Le statut du rapport a été bien changé.',
                ]);
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
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
                        'message' => 'Rapport supprimé avec succès',
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
     * @Route("/project/{project_id}/activity/{activity_id}/reports/{page<\d+>?1}", name="list2", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+"})
     * @Route("/project/{project_id}/activity/{activity_id}/reports/{type}/{page<\d+>?1}", name="list2_type", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+"})
     * @Route("/project/{project_id}/reports/{page<\d+>?1}", name="list", methods="GET", requirements={"project_id"="\d+"})
     * @Route("/project/{project_id}/reports/{type}/{page<\d+>?1}", name="list_type", methods="GET", requirements={"project_id"="\d+"})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(Project $project, $activity_id = 0, ?string $type, $page = 1, PaginatorService $paginator)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $order_by = $project->getMeta('report_order_by', 'createdAt');
        $order    = $project->getMeta('report_order', 'DESC');
        
        $user = $project->getAuthor();

        $entityManager = $this->getDoctrine()->getManager();

        $activity = null;
        if( $activity_id > 0 ){
            $activity = $entityManager->getRepository(Activity::class)->find($activity_id);

            if( ! $activity ){
                throw $this->createNotFoundException('The activity not found');
            }
            
            if($activity->getProject() != $project ){
                throw $this->createNotFoundException('The project does not match');
            }
            
            $this->denyAccessUnlessGranted('view', $activity);
            
            $query = $entityManager->getRepository(Report::class)->findByActivity($activity, null, $order_by, $order);
            
        }else{
            $this->denyAccessUnlessGranted('view', $project);
            
            $query = $entityManager->getRepository(Report::class)->findByProject($project, null, $order_by, $order);
        }
        
        $reports = $paginator->paginate($query, 10);

        if($type == 'list'){
            return $this->render('report/list.html.twig', [
                'user'     => $user,
                'project'  => $project,
                'activity' => $activity,
                'reports'  => $reports
            ]);
        }
        
        return $this->render('report/grid.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity,
            'reports'  => $reports
        ]);
    }
}
