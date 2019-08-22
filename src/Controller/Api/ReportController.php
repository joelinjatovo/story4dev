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
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        $report = new Report();
        
        $form = $this->createForm(ReportType::class, $report, ['csrf_protection' => false]);
        
        $form->submit($data);
        
        if (!$form->isValid()) {
            
            return $this->json([
                        'status' => 'error',
                        'errors' => $form->getErrors(),
                    ], JsonResponse::HTTP_BAD_REQUEST);
            
        }
        
        $result->setAuthor($this->getUser());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($report);
        $em->flush();
        
        return $this->json($request->request);
    }

}