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
use App\Service\FileUploader;

/**
 * @Version("v1")
 * @Route(name="upload_")
 */
class UploadController extends AbstractController
{
    /**
     * @Rest\Get("/upload-chunk", name="index")
     */
    public function upload(FileUploader $uploader, Request $request)
    {
    }

}