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
 *
 * @IsGranted("ROLE_USER") 
 */
class UploadController extends AbstractController
{
    /**
     * @Route("/xu/upload", name="get_upload", methods="GET")
     */
    public function index()
    {
        $file = new File();
        $form = $this->createForm(UploadType::class, $file);
        
        return $this->render('upload/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/u/avatar", name="get_avatar", methods={"GET"})
     */
    public function avatar()
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        
        return $this->render('upload/avatar.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/u/avatar", name="post_avatar", methods={"POST"})
     */
    public function postAvatar(Request $request, FormError $formError, FileUploader $fileUploader)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if(!$form->isValid()){
                return new JsonResponse($formError->getErrorMessages($form), JsonResponse::HTTP_BAD_REQUEST);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['123'], 200);
        }
        
        return new JsonResponse(['123'], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/xu/upload", name="post_upload", methods="POST")
     *
     * @param Request $request
     *
     * @return JsonResponse|FormInterface
     */
    public function upload(Request $request, FormError $formError, FileUploader $fileUploader)
    {
        $file = new File();
        
        $form = $this->createForm(UploadType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if(!$form->isValid()){
                return new JsonResponse($formError->getErrorMessages($form), JsonResponse::HTTP_BAD_REQUEST);
            }
            
            /*
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
            */
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return new JsonResponse(['123'], 201);
        }

        return new JsonResponse(['123'], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/u/chunk", name="get_chunk", methods={"GET"})
     */
    public function chunk()
    {
        return $this->render('upload/chunk.html.twig');
    }
}
