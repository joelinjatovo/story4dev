<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Vich\UploaderBundle\Handler\DownloadHandler;

use App\Entity\File;
use App\Entity\ActivityFile;
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
     * @Route("/{slug}/files/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/{slug}/project/{project_id}/files/{page<\d+>?1}", name="list_project", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(User $user, Project $project = null, $page = 1, Request $request, PaginatorService $paginator)
    {
        if($this->getUser() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $projects = $entityManager->getRepository(Project::class)->findAll();
            
        if( $project != null ) {
            
            $this->denyAccessUnlessGranted('view', $project);

            $query = $entityManager->getRepository(File::class)->findByProject($project, $user);

            $files = $paginator->paginate($query);
            
            return $this->render('file/list.html.twig', [
                'files'    => $files,
                'user'     => $user,
                'project'  => $project,
                'projects' => $projects,
            ]);
        }
        
        $query = $entityManager->getRepository(File::class)->findAllQuery($user);

        $files = $paginator->paginate($query);
        
        return $this->render('file/list.html.twig', [
            'files'    => $files,
            'user'     => $user,
            'project'  => null,
            'projects' => $projects,
        ]);
    }
    
    /**
     * @Route("/download/{id}", name="download", methods="GET", requirements={"id"="\d+"})
     * @Entity("file", options={"mapping": {"id": "id"}})
     */
    public function download(File $file, DownloadHandler $downloadHandler)
    {
        return $downloadHandler->downloadObject($file, 'file');
    }
}
