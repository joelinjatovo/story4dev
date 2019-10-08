<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\ProjectContribution;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\Report;
use App\Service\PaginatorService;

/** 
 * @Route(name="contribution_") 
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class ContributionController extends AbstractController
{
    
    /**
     * @Route("/{slug}/project/{project_id}/contributions", name="list", methods="GET", requirements={"project_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(User $user, Project $project)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $contributions = $project->getContributions();
        
        return $this->render('contribution/list.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'contributions' => $contributions, 
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/contribution/{contribution_id}", name="show", methods="GET", requirements={"project_id"="\d+","contribution_id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     * @Entity("projectcontribution", options={"mapping": {"contribution_id": "id"}})
     */
    public function show(User $user, Project $project, ProjectContribution $contribution)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        if($contribution->getProject() != $project ){
            throw $this->createNotFoundException('The project does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $reports = $entityManager->getRepository(Report::class)->findByContribution($project, $contribution->getUser())->getResult();
        
        return $this->render('contribution/show.html.twig', [
            'user'         => $user,
            'project'      => $project,
            'contribution' => $contribution, 
            'reports'      => $reports, 
        ]);
    }
    
    /**
     * @Route("/contribution", name="role_change", methods="POST")
     */
    public function roleChange(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $contribution = $entityManager->getRepository(ProjectContribution::class)->find($id);
                if( $contribution ){
                    $this->denyAccessUnlessGranted('edit', $contribution->getProject());

                    if($contribution->getUser() != $contribution->getProject()->getAuthor()){
                        if($contribution->isAdmin()){
                            $contribution->setRoles(['ROLE_CONTRIBUTOR']);
                        }else{
                            $contribution->setRoles(['ROLE_ADMIN', 'ROLE_CONTRIBUTOR']);
                        }

                        $entityManager->persist($contribution);
                        $entityManager->flush();

                        return $this->json([
                            'success' => true,
                            'admin'   => $contribution->isAdmin(),
                            'message' => 'Role changed',
                        ]);
                    }
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
     * @Route("/contribution/search", name="search", methods="GET")
     */
    public function search(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $search = $request->query->get('search');
            
            $project_id = $request->query->get('project');
            
            if( ! empty( $search ) && ( $project_id > 0 ) ) {
                $entityManager = $this->getDoctrine()->getManager();
                $project = $entityManager->getRepository(Project::class)->find($project_id);
                if($project){
                    $users = $entityManager->getRepository(User::class)->searchAllNotInProject($project, $search)->execute();
                    $content = $this->renderView('contribution/search.html.twig', [ 'search' => $search, 'project' => $project, 'users' => $users]);
                    return $this->json([
                        'success' => true,
                        'status'  => 'success',
                        'html'    => $content,
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'An error was occured. :)',
                'html'    => '',
                'search'    => $search,
            ]);
        }
    }
    
    /**
     * @Route("/contribution/remove", name="remove", methods="POST")
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $contribution  = $entityManager->getRepository(ProjectContribution::class)->find($id);

                if( $contribution && ! $contribution->getUser()->isAdmin()){
                    $entityManager->remove($contribution);
                    $entityManager->flush();

                    return $this->json([
                        'success' => true,
                        'status'  => 'success',
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
    
    /**
     * @Route("/contribution/add", name="add", methods="POST")
     */
    public function add(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $user_id = $request->request->get('user_id');
            $project_id = $request->request->get('project_id');
            
            if( ( $user_id > 0 ) && ( $project_id > 0 ) ) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($user_id);
                $project = $entityManager->getRepository(Project::class)->find($project_id);
                $contribution = $entityManager->getRepository(ProjectContribution::class)->findOneBy(['project'=>$project, 'user'=>$user]);
                
                if( $contribution ){
                    return $this->json([
                        'success' => true,
                        'status'  => 'success',
                        'html'    => '',
                    ]);
                }

                if( $project && $user){
                    $contribution = new ProjectContribution();
                    $contribution->setUser($user);
                    $contribution->setProject($project);
                    $contribution->setRoles(['ROLE_CONTRIBUTOR']);
                    
                    $entityManager->persist($contribution);
                    $entityManager->flush();

                    return $this->json([
                        'success' => true,
                        'status'  => 'success',
                        'html'    => $this->renderView('project/contribution.html.twig', [ 'contribution' => $contribution]),
                        'table'   => $this->renderView('contribution/list_item.html.twig', [ 'contribution' => $contribution]),
                    ]);
                }
            }
            
            return $this->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
}
