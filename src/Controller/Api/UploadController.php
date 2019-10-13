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

use App\Entity\File;
use App\Form\UploadType;
use App\Service\FileUploader;
use App\Service\FormError;

/**
 * @Version("v1")
 */
class UploadController extends AbstractController
{
    /**
     * @Rest\Post("/upload")
     */
    public function upload(FileUploader $uploader, Request $request, FormError $formError)
    {
        $file = new File();        
        $form = $this->createForm(UploadType::class, $file, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            if( ! $form->isValid() ) {
                //
                return $this->json([
                    'status' => 'error', 
                    'error'  => "bad_request",
                    'errors' => $formError->getApiErrorMessages($form)
                ], JsonResponse::HTTP_BAD_REQUEST, [], ['groups' => ['file']]);
            }

            try{
                $uploadedFile = $form['file']->getData();
                $file->setAuthor($this->getUser());
                $file->setMimeType($uploadedFile->getMimeType());
                $file->setSize($uploadedFile->getSize());
                $file->setDisplayName($uploadedFile->getClientOriginalName());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($file);
                $entityManager->flush();

                return $this->json([
                    'status'  => 'success', 
                    'message' => 'file_uploaded', 
                    'data'    => $file
                ], JsonResponse::HTTP_CREATED, [], ['groups' => ['file']]);
            }catch(\Exception $e){
                return $this->json([
                    'status' => 'error', 
                    'error'  => "bad_request",
                    'errors' => [
                        'exception' => $e->getMessage()
                    ]
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }
        
        return $this->json([
            'status' => 'error',
            'error'  => "form_not_submitted",
            'errors' => []
        ], JsonResponse::HTTP_BAD_REQUEST);
        
    }

}