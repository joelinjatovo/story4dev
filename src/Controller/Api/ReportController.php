<?php

namespace App\Controller\Api;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Version;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Entity\File;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\ReportFile;
use App\Form\ReportType;
use App\Form\UploadType;
use App\Form\ReportFileType;
use App\Service\FormError;
use App\Service\FileUploader;

/**
 * @Version("v1")
 */
class ReportController extends AbstractController
{
    
    /**
     * @Rest\Get("/report/{report_id}")
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function index(Report $report, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $report);
        
        return $this->json(['data' => $report], 200, [], ['groups' => ['report']]);
    }
    
    /**
     * @Rest\Post("/report")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);
        
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report, ['csrf_protection' => false]);
        $form->submit($data['payload']);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            $report->setAuthor($this->getUser());

            $em = $this->getDoctrine()->getManager();
        
            foreach ($report->getResults() as $result) {
                $result->setAuthor($this->getUser());
                $em->persist($result);
            }

            $em->persist($report);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Report created.', 'data' => $report], JsonResponse::HTTP_CREATED, [], ['groups' => ['report']]);
        }
        
        return $this->json(['status' => 'error', 'errors' => $formError->getErrorMessages($form)], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/report/{report_id}")
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function update(Report $report, Request $request, FormError $formErrort)
    {
        $this->denyAccessUnlessGranted('edit', $report);

        $originalFiles = $report->getReportFiles();
        
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ReportType::class, $report, ['csrf_protection' => false]);
        $form->submit($data['payload']);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();

            foreach ($report->getReportFiles() as $reportFile) {
                $em->persist($reportFile);
            }

            $em->persist($report);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Report updated.'], JsonResponse::HTTP_OK);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * @Rest\Post("/report/{report_id}/add-file")
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function addFile(Report $report, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $report);

        $data = json_decode($request->getContent(), true);

        $reportFile = new ReportFile();
        $form = $this->createForm(ReportFileType::class, $reportFile, ['csrf_protection' => false]);

        $form->submit($data['payload']);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $reportFile->setReport($report);

            $em = $this->getDoctrine()->getManager();

            $old = $em->getRepository(ReportFile::class)->findOneBy(['report' => $report, 'file' => $reportFile->getFile()]);
            if($old){
                $old->setFile($reportFile->getFile());
                $old->setType($reportFile->getType());
                $reportFile = $old;
            }

            $em->persist($reportFile);
            $em->flush();
            
            if($old){
                return $this->json(['status' => 'ok', 'action' => 'update', 'message' => 'Report File updated.'], JsonResponse::HTTP_OK);
            }

            return $this->json(['status' => 'ok', 'action' => 'create', 'message' => 'Report File added.'], JsonResponse::HTTP_OK);
        }
        
        return $this->json(['data' => $report], 200, [], ['groups' => ['report']]);
    }
    
    /**
     * @Rest\Get("/project/{project_id}/reports")
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(Project $project, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Report::class);
        
        $reports = $repository->findBy(['project' => $project]);
        
        return $this->json(['data' => $reports], 200, [], ['groups' => ['report']]);
    }

}