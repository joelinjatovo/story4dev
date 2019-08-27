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
use App\Entity\Result;
use App\Form\ResultType;
use App\Service\FormError;

/**
 * @Version("v1")
 * @Route(name="result_")
 */
class ResultController extends AbstractController
{
    
    /**
     * @Rest\Get("/result/{result_id}")
     * @Entity("result", options={"mapping": {"result_id": "id"}})
     */
    public function index(Result $result, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $result);
        
        return $this->json(['data' => $result], 200, [], ['groups' => ['result']]);
    }

    /**
     * @Rest\Post("/result", name="create")
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        $result = new Result();
        $form = $this->createForm(ResultType::class, $result, ['csrf_protection' => false]);
        $form->submit($data->payload);
        
        if ($form->isSubmitted() && $form->isValid() ) {
            $result->setAuthor($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($result);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Result created.'], JsonResponse::HTTP_CREATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/result/{result_id}")
     * @Entity("result", options={"mapping": {"result_id": "id"}})
     */
    public function update(Result $result, Request $reques, FormError $formErrort)
    {
        $this->denyAccessUnlessGranted('edit', $result);
        
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ResultType::class, $result);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($result);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Result updated.'], JsonResponse::HTTP_UPDATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * @Rest\Get("/results/{report_id}")
     * @Entity("report", options={"mapping": {"report_id": "id"}})
     */
    public function list(Report $report, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Result::class);
        
        $results = $repository->findAll();
        
        return $this->json(['data' => $results], 200, [], ['groups' => ['result']]);
    }

}