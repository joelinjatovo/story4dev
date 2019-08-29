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

use App\Entity\File;
use App\Entity\Project;
use App\Entity\Report;
use App\Form\ReportType;
use App\Form\UploadType;
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
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/report/{report_id}")
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function update(Report $report, Request $reques, FormError $formErrort)
    {
        $this->denyAccessUnlessGranted('edit', $report);
        
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ReportType::class, $report);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Report updated.'], JsonResponse::HTTP_UPDATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * @Rest\Post("/report/{report_id}")
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function upload(Report $report, FileUploader $uploader, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $report);

        /*
        $file = new File();
        $form = $this->createForm(UploadType::class, $file);
        $form->handleRequest($request);

        $uploadedFile = $form['file']->getData();
        if ($uploadedFile) {
            try{
                $uploadedFileName = $fileUploader->upload($uploadedFile);
                $file->setPath($uploadedFileName);
            } catch (FileException $e) {
                return new JsonResponse([
                    'status' => 0,
                    'error' => 'Can not upload file',
                    'message'=> $e->getMessage(),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $uploader->upload('file');
        */
        
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