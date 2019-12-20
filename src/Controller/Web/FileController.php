<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Vich\UploaderBundle\Handler\DownloadHandler;

use App\Entity\File;
use App\Entity\Download;
use App\Entity\Activity;
use App\Entity\User;
use App\Entity\Project;
use App\Service\PaginatorService;

/** 
 * @Route(name="file_") 
 *
 * @IsGranted("ROLE_USER") 
 */
class FileController extends AbstractController
{
    
    /**
     * @Route("/files/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/p/{slug}/files/{page<\d+>?1}", name="list_project", methods="GET")
     * @Route("/p/{slug}/activity/{activity_id}/files/{page<\d+>?1}", name="list_activity", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function list(?Project $project = null, Activity $activity = null, $page = 1, Request $request, PaginatorService $paginator)
    {
        $type = $request->query->get('type');
        $template = 'file/images-list.html.twig';
        if($type == 'docs'){
            $filterType = 'document';
            $template = 'file/docs-list.html.twig';
        }else{
            $filterType = 'image';
            $type = 'images';
        }
        
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $projects = $entityManager->getRepository(Project::class)
            ->findByContributor($user)
            ->execute();
            
        $limit = 20;

        if( $activity != null ) {
            $this->denyAccessUnlessGranted('view', $activity);
            $limit =  $project->getMeta('file_count', $limit);
            $query = $entityManager->getRepository(File::class)
                ->findByActivity($activity, ['type' => $filterType]);
        }else if( $project != null ) {
            $this->denyAccessUnlessGranted('view', $project);
            $limit =  $project->getMeta('file_count', $limit);
            $query = $entityManager->getRepository(File::class)
                ->findByProject($project, ['type' => $filterType]);
        } else {
            $query = $entityManager->getRepository(File::class)
                ->findByUser($user, ['type' => $filterType]);
        }

        $files = $paginator->paginate($query, $limit);
        
        return $this->render($template, [
            'files'    => $files,
            'user'     => $user,
            'activity' => $activity,
            'project'  => $project,
            'projects' => $projects,
            'type'     => $type,
        ]);
    }
    
    /**
     * @Route("/file/{file_id}", name="show", methods="GET")
     * @Route("/p/{slug}/file/{file_id}", name="show_project", methods="GET")
     * @Route("/p/{slug}/activity/{activity_id}/file/{file_id}", name="show_activity", methods="GET")
     * @Entity("project", options={"mapping": {"slug": "slug"}})
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     * @Entity("file", options={"mapping": {"file_id": "id"}})
     */
    public function show(File $file, Project $project = null, Activity $activity = null)
    {
        $this->denyAccessUnlessGranted('view', $file);

        $user = $this->getUser();
        
        $entityManager = $this->getDoctrine()->getManager();

        $projects = $entityManager->getRepository(Project::class)->findByContributor($user)->execute();
        
        return $this->render('file/show.html.twig', [
            'file'     => $file,
            'user'     => $user,
            'project'  => $project,
            'projects' => $projects,
            'activity' => $activity,
        ]);
    }
    
    /**
     * @Route("/download/{id}", name="download", methods="GET", requirements={"id"="\d+"})
     * @Entity("file", options={"mapping": {"id": "id"}})
     */
    public function download(File $file, DownloadHandler $downloadHandler, Request $request)
    {
        $this->denyAccessUnlessGranted('download', $file);

        $download = new Download();
        $download->setUser($this->getUser());
        $download->setFile($file);
        $download->setIp($request->getClientIp());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($download);
        $entityManager->flush();

        return $downloadHandler->downloadObject($file, 'file');
    }
}
