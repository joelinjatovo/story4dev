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

        if ($form->isSubmitted()){
            if( ! $form->isValid() ) {
                $errors = $formError->getErrorMessages($form);
                return $this->json(['status' => 0,'errors' => $errors], 400, [], ['groups' => ['file']]);
            }
            
            $uploadedFile = $form['file']->getData();
            if ($uploadedFile) {
                try{
                    $uploadedFileName = $uploader->upload($uploadedFile);
                    $file->setPath($uploadedFileName);
                } catch (FileException $e) {
                    return $this->json(['status' => 0,'error' => 'Can not upload file', 'message'=> $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
        }
        
        return $this->json(['data' => $file], 200, [], ['groups' => ['file']]);
        
    }
    /**
     * @Rest\Post("/vich-upload")
     */
    public function vichUpload(FileUploader $uploader, Request $request, FormError $formError)
    {
        $file = new File();
        
        $form = $this->createForm(UploadType::class, $file, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if( ! $form->isValid() ) {
                $errors = $formError->getErrorMessages($form);
                return $this->json(['status' => 0,'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST, [], ['groups' => ['file']]);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($file);
            $entityManager->flush();
        
            return $this->json(['data' => $file], 200, [], ['groups' => ['file']]);
        }
        
        return $this->json(['status' => 0,'errors' => "Form not submitted"], JsonResponse::HTTP_BAD_REQUEST);
        
    }

}