<?php

namespace App\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\File;
use App\Entity\User;
use App\Form\UploadType;
use App\Form\UserType;
use App\Service\FormError;
use App\Service\FileUploader;

/**
 * @IsGranted("ROLE_USER") 
 */
class UploadController extends AbstractController
{

    /**
     * @Route("/upload/vich", name="upload", methods="POST")
     */
    public function upload(Request $request, FormError $formError, FileUploader $fileUploader)
    {
        $file = new File();
        
        $form = $this->createForm(UploadType::class, $file, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            
            if( ! $form->isValid() ) {
                $errors = $formError->getErrorMessages($form);
                return $this->json(['status' => 0,'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST, [], ['groups' => ['file']]);
            }
            
            $uploadedFile = $form['file']->getData();
            $file->setAuthor($this->getUser());
            $file->setMimeType($uploadedFile->getMimeType());
            $file->setSize($uploadedFile->getSize());
            $file->setDisplayName($uploadedFile->getClientOriginalName());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($file);
            $entityManager->flush();
        
            return $this->json([
                'data' => $file,
                'html' => $this->renderView('upload/file.html.twig', [
                    'file' => $file
                ]),
                'fileHtml' => $this->renderView('file/list_item.html.twig', [
                    'file' => $file
                ])
            ], 200, [], ['groups' => ['file']]);
        }
        
        return $this->json(['status' => 0,'errors' => "Form not submitted"], JsonResponse::HTTP_BAD_REQUEST);
    }
}
