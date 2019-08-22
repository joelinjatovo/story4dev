<?php

namespace App\Controller\Api;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Version;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use App\Service\FormError;
use App\Entity\Report;
use App\Form\ReportType;

/**
 * @Version("v1")
 * @Route(name="report_")
 */
class ReportController extends AbstractController
{
    /**
     * @Rest\Post("/report", name="create")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);
        
        $report = new Report();
        
        $form = $this->createForm(ReportType::class, $report, ['csrf_protection' => false]);
        
        $form->submit($data);
        
        if ($form->isSubmitted() && !$form->isValid() ) {
            return $this->json([
                'status' => 'error',
                'errors' => $formError->getErrorMessages($form),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($report->getResults() as $result) {
            $result->setAuthor($this->getUser());
            $em->persist($result);
        }
        
        $report->setAuthor($this->getUser());
        
        $em->persist($report);
        
        $em->flush();
        
        return $this->json([
                        'status'  => 'success',
                        'message' => "Report successfully created",
                    ]);
    }

}