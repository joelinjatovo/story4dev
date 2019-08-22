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

use App\Entity\Result;
use App\Form\ResultType;

/**
 * @Version("v1")
 * @Route(name="result_")
 */
class ResultController extends AbstractController
{
    /**
     * @Rest\Post("/result", name="create")
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        $result = new Result();
        
        $form = $this->createForm(ResultType::class, $result, ['csrf_protection' => false]);
        
        $form->submit($data);
        
        if ($form->isSubmitted() && !$form->isValid()) {
            
            return $this->json([
                        'status' => 'error',
                        'errors' => $form->getErrors(),
                    ], JsonResponse::HTTP_BAD_REQUEST);
            
        }
        
        $result->setAuthor($this->getUser());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($result);
        $em->flush();
        
        return $this->json($request->request);
    }

}