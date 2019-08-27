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

use App\Entity\Report;
use App\Form\ReportType;
use App\Service\FormError;

/**
 * @Version("v1")
 * @Route(name="report_")
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
     * @Rest\Post("/report", name="create", condition="request.attributes.get('version') == 'v1'")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);
        
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report, ['csrf_protection' => false]);
        $form->submit($data->payload);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
        
            foreach ($report->getResults() as $result) {
                $result->setAuthor($this->getUser());
                $em->persist($result);
            }

            $em->persist($report);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Report created.'], JsonResponse::HTTP_CREATED);
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
     * @Rest\Get("/reports")
     */
    public function list(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Report::class);
        
        $reports = $repository->findAll();
        
        return $this->json(['data' => $reports], 200, [], ['groups' => ['report']]);
    }

}