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
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/report", name="index", methods="GET", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function index(User $user, Project $project, Activity $activity)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }

        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $report = new Report();
        $report->setActivity($activity);
        $report->setAuthor($this->getUser());
        
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $activity ));
        
        return $this->render('report/create.html.twig', [
            'user'     => $user, 
            'project'  => $project, 
            'activity' => $activity,
            'form'     => $form->createView()
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/report", name="create", methods="POST", requirements={"project_id"="\d+","activity_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function create(User $user, Project $project, Activity $activity, Request $request): Response
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }

        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        $report = new Report();
        
        $form = $this->createForm(ReportType::class, $report, array( 'activity' => $activity ));
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                
                $report->setActivity($activity);
                $report->setAuthor($this->getUser());
                
                foreach ($report->getResults() as $result) {
                    $result->setAuthor($this->getUser());
                    $entityManager->persist($result);
                }
                
                $entityManager->persist($report);
                $entityManager->flush();

                $this->addFlash('success', 'Report Created! Knowledge is power!');
            }else{
                $this->addFlash('error', 'Report Not Created! Request not valide!');
            }
        }
        
        return $this->redirectToRoute('report_index', [
            'slug'        => $user->getSlug(), 
            'project_id'  => $project->getId(), 
            'activity_id' => $activity->getId(), 
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/report/{report_id}", name="show", methods="GET", requirements={"project_id"="\d+","report_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function show(User $user, Project $project, Activity $activity, Report $report)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        if($report->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        return $this->render('report/show.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity,
            'report'   => $report
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/report/edit/{report_id}", name="edit", methods="GET", requirements={"project_id"="\d+","id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function edit(User $user, Project $project, Activity $activity, Report $report)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        if($report->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
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
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/report/edit/{report_id}", name="update", methods="POST", requirements={"project_id"="\d+","id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function update(User $user, Project $project, Activity $activity, Report $report, Request $request)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($activity->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }

        if($report->getActivity() != $activity ){
            throw $this->createNotFoundException('The activity does not match');
        }
        
        $originalResults = new ArrayCollection();
        foreach ($report->getResults() as $result) {
            $originalResults->add($result);
        }
        
        $form = $this->createForm(ReportType::class, $report, array('project' => $project));
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                
                $entityManager->persist($report);
                $entityManager->flush();

                $this->addFlash('success', 'Report Updated! Knowledge is power!');
            }else{
                $this->addFlash('error', 'Report Not Updated! Request not valide!');
            }
            
        }
        
        return $this->redirectToRoute('report_edit', [
            'slug'        => $user->getSlug(), 
            'project_id'  => $project->getId(), 
            'activity_id' => $activity->getId(),
            'id'          => $report->getId(), 
        ]);
    }
    
    
    /**
     * @Route("/{slug}/project/{project_id}/activity/{activity_id}/reports/{page<\d+>?1}", name="list2", methods="GET", requirements={"project_id"="\d+", "activity_id"="\d+"})
     * @Route("/{slug}/project/{project_id}/reports/{page<\d+>?1}", name="list", methods="GET", requirements={"project_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(User $user, Project $project, $activity_id = 0, $page = 1, PaginatorService $paginator)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }

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
            
            $query = $entityManager->getRepository(Report::class)->findByActivity($activity);
        }else{
            $query = $entityManager->getRepository(Report::class)->findByProject($project);
        }
        
        $reports = $paginator->paginate($query, 10);
        
        return $this->render('report/list.html.twig', [
            'user'     => $user,
            'project'  => $project,
            'activity' => $activity,
            'reports'  => $reports
        ]);
    }
}
