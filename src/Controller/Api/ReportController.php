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
     * @Rest\Post("/report")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);
        
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report, ['csrf_protection' => false]);
        $form->submit($data['payload']);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            try{
                $report->setAuthor($this->getUser());
                $report->setStatus(Report::STATUS_CLOSED);

                $em = $this->getDoctrine()->getManager();

                foreach ($report->getResults() as $result) {
                    $result->setAuthor($this->getUser());
                    $em->persist($result);
                }

                $em->persist($report);
                $em->flush();

                return $this->json([
                    'status'  => 'success', 
                    'message' => 'report_created', 
                    'data'    => $report
                ], JsonResponse::HTTP_CREATED, [], ['groups' => ['report']]);
            }catch(\Exception $e){
                return $this->json([
                    'status' => 'error', 
                    'error'  => "bad_request",
                    'errors' => []
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }
        
        return $this->json([
            'status' => 'error', 
            'error'  => "invalid_form",
            'errors' => $formError->getErrorMessages($form)
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

}