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

/** 
 * @Route(name="admin_project_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class ProjectController extends AbstractController
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
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        
        $project = new Project();
        
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) {

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
        
            $this->addFlash('success', 'Projet créé avec succès.');
            
            $args = [
                'slug' => $project->getSlug(), 
            ];
            
            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-edit':
                    return $this->redirectToRoute('project_edit', $args);
                case 'save-continue':
                case 'save-exit':
                    return $this->redirectToRoute('project_show', $args);
                case 'save-create':
                case 'save-default':
                default:
                    return $this->redirectToRoute('admin_project_create');
            }

            return $this->redirectToRoute('project_edit', [
                'slug' => $project->getSlug(), 
            ]);
            
        }
        
        $this->addFlash('error', 'Une erreur s\'est produite.');

        return $this->render('project/create.html.twig', [
            'user'    => $user,
            'project' => $project,
            'form'    => $form->createView()
        ]);
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
                        'message' => 'Projet supprimé avec succès',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
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
                        'message' => 'Projet supprimé complètement avec succès',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
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
                        'message' => 'Projet restauré avec succès',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
}
