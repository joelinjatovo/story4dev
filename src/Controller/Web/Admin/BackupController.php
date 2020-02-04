<?php

namespace App\Controller\Web\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Download;

/** 
 * @Route("/admin", name="admin_backup_")
 *
 * @IsGranted("ROLE_SUPER_ADMIN") 
 *
 */
class BackupController extends AbstractController
{
    const TYPE_SQL  = 'backup_sql';
    const TYPE_FILE = 'backup_file';
    /**
    * @Route("/backup/{type}", name="download")
    */
    public function download($type, Request $request)
    {
        $download = new Download();
        $download->setUser($this->getUser());
        $download->setIp($request->getClientIp());
        
        if($type=='database'){
            $download->setType(self::TYPE_SQL);
            $filePath = $this->getParameter('kernel.project_dir').'/../backup_story4dev.com.sql.gz';
        }else{
            $download->setType(self::TYPE_FILE);
            $filePath = $this->getParameter('kernel.project_dir').'/../backup_story4dev.com.tar.gz';
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($download);
        $entityManager->flush();
        
        return $this->file($filePath);
    }
}