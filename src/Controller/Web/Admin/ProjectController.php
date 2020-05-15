<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Service\PaginatorService;
use App\Entity\ProjectContribution;
use App\Controller\Web\BaseController;

/** 
 * @Route(name="admin_project_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class ProjectController extends BaseController
{
    /**
     * @Route("/admin/project", name="index", methods="GET")
     */
    public function index()
    {
        $user = $this->getUser();
        
        $project = new Project();
        
        $form = $this->createForm(ProjectType::class, $project);
        
        return $this->render('project/create.html.twig', [
            'user'    => $user,
            'project' => $project,
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/admin/project", name="create", methods="POST")
     */
    public function create(Request $request)
    {
        $user = $this->getUser();
        
        $project = new Project();
        
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if ( $form->isValid() ) {
                if( $project->getAuthor() == null ) {
                    $project->setAuthor( $this->getUser() );
                }
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($project);

                $contribution = new ProjectContribution();
                $contribution->setUser( $project->getAuthor() );
                $contribution->setProject( $project );
                $contribution->setStatus( ProjectContribution::STATUS_ACTIVE );
                $contribution->setRoles(['ROLE_ADMIN']);
                $entityManager->persist( $contribution );

                $entityManager->flush();

                $this->addFlash('success', $this->trans('controller.project.created'));

            }else{
                $this->addFlash('error', $this->trans('controller.error.occured'));
            }
        }
        
        return $this->redirectToRoute('admin_project_index');
    }
    
    /**
     * @Route("/admin/projects/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list(PaginatorService $paginator, int $page, Request $request)
    {
        $search = $request->query->get('s');
        if( strlen($search) > 20 ) {
            $search = substr($search, 0, 20);
        }

        $entityManager = $this->getDoctrine()->getManager();
        
        $query = $entityManager->getRepository(Project::class)->getAll();

        $projects = $paginator->paginate($query);
        
        return $this->render('admin/project/list.html.twig', [
            'projects' => $projects, 
            'search'   => $search, 
        ]);
    }
    
    /**
     * @Route("/admin/project/trash", name="trash", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function trash(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
        	
                $entityManager->getFilters()->disable("deleted");
                
                $project = $entityManager->getRepository(Project::class)->find($id);
                if( $project && ! $project->isDeleted()){
                    $entityManager->remove($project);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.project.deleted'),
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
    
    /**
     * @Route("/admin/project/remove", name="remove", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
        	
                $entityManager->getFilters()->disable("deleted");
                
                $project = $entityManager->getRepository(Project::class)->find($id);
                if( $project && $project->isDeleted()){
                    $entityManager->remove($project);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.project.deleted.completely'),
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
    
    /**
     * @Route("/admin/project/restore", name="restore", methods="POST")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN") 
     * 
     */
    public function restore(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
        	
                $entityManager->getFilters()->disable("deleted");

                $project = $entityManager->getRepository(Project::class)->find($id);
                if( $project && $project->isDeleted()){
                    $project->setDeletedAt(null);
                    $entityManager->persist($project);
                    $entityManager->flush();
                    
                    return $this->json([
                        'success' => true,
                        'message' => $this->trans('controller.project.restored'),
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => $this->trans('controller.bad.request'),
                'message' => $this->trans('controller.error.occured'),
            ]);
        }
    }
}
