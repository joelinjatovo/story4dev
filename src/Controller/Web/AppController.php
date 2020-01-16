<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Vich\UploaderBundle\Handler\DownloadHandler;

use App\Entity\Project;
use App\Entity\Download;
use App\Form\FeedbackType;
use App\Helper\MessageHelper;
use App\Service\OptionService;

/** 
 * @Route(name="apps_")
 *
 * @IsGranted("ROLE_USER") 
 *
 */
class AppController extends AbstractController
{
    const TYPE_ANDROID = 'android';
    const TYPE_IOS = 'ios';
    /**
    * @Route("/apps", name="applications")
    */
    public function index(Request $request)
    {
        return $this->render('apps/index.html.twig');
    }
    /**
    * @Route("/apps/download/android", name="android")
    */
    public function android(DownloadHandler $downloadHandler, Request $request)
    {
        $download = new Download();
        $download->setUser($this->getUser());
        $download->setIp($request->getClientIp());
        $download->setType(self::TYPE_ANDROID);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($download);
        $entityManager->flush();

        $filePath = $this->getParameter('kernel.project_dir').'/apps/android/com.story4dev-1.1.1-release-20200116.apk';
        return $this->file($filePath);
    }
}