<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Form\Type\VichImageType;

use App\Entity\User;
use App\Entity\File;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\Activity;
use App\Entity\Iteration;
use App\Form\ActivityType;
use App\Form\ProjectType;
use App\Service\FormError;
use App\Service\PaginatorService;

/** 
 * @Route(name="project_")
 *
 * @IsGranted("ROLE_USER") 
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/{slug}/project/{id}/dashboard", name="dashboard", methods="GET", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function dashboard(User $user, Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $reports = $entityManager->getRepository(Report::class)->findByProject($project)->execute();
        $users = $entityManager->getRepository(User::class)->findAll();
        
        $files = $entityManager->getRepository(File::class)->findByProject($project)->getResult();
        $files_per_date = $entityManager->getRepository(File::class)->findByProjectPerMonth($project);
        $files_per_activity = $entityManager->getRepository(File::class)->findByProjectPerActivity($project);
        
        $data   =  $project->getData();
        $series =  $project->getSerie();
        
        return $this->render('project/dashboard.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'reports' => $reports, 
            'users'   => $users,
            'data'    => json_encode($data),
            'series'   => json_encode($series),
            'filesCount'         => count($files),
            'files'              => $files,
            'files_per_activity' => $files_per_activity,
        ]);
    }
    
    /**
     * @Route("/{slug}/project/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function show(User $user, Project $project)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }

        $entityManager = $this->getDoctrine()->getManager();
        
        if ($this->isGranted('edit', $project)) {
            $reports = $entityManager->getRepository(Report::class)->findByProject($project)->execute();
        }else{
            $reports = $entityManager->getRepository(Report::class)->findByProject($project, $this->getUser())->execute();
        }
        
        $data   =  $project->getData();
        $series =  $project->getSerie();
        
        //dump($data); exit;
        
        return $this->render('project/show.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'reports' => $reports, 
            'data'    => json_encode($data),
            'series'   => json_encode($series),
        ]);
    }
    
    /**
     * @Route("/{slug}/project/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function edit(User $user, Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $form = $this->createForm(ProjectType::class, $project);
        
        return $this->render('project/edit.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/{slug}/project/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function update(User $user, Project $project, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $originalIterations = new ArrayCollection();
        foreach ($project->getIterations() as $iteration) {
            $originalIterations->add($iteration);
        }
        
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) {
            
            $entityManager = $this->getDoctrine()->getManager();
            
            // remove the relationship
            foreach ($originalIterations as $iteration) {
                $removed = true;
                foreach($project->getIterations() as $updated_iteration){
                    if ( ( $updated_iteration->getId() > 0 ) && ($updated_iteration->getId() === $iteration->getId()) ) {
                        $removed = false;
                        break;
                    }
                }
                
                if($removed === true){
                    if( ! $iteration->hasGoals() ) {
                        $project->removeIteration($iteration);
                        $entityManager->remove($iteration);
                    }else{
                        $project->addIteration($iteration);
                        $this->addFlash('error', 'On ne peut pas supprimer l\'itération suivante: '.$iteration->getTitle());
                    }
                }
            }
            
            // set author for new iteration
            foreach($project->getIterations() as $updated_iteration){
                if($updated_iteration->getAuthor()==null){
                    $updated_iteration->setAuthor($this->getUser());
                }
            }

            $entityManager->persist($project);
            $entityManager->flush();
        
            $this->addFlash('success', 'Project updated succesfully.');
            
            $args = [
                'slug' => $user->getSlug(),
                'id'   => $project->getId()
            ];

            $action = strtolower( $request->request->get('submit') );
            switch($action){
                case 'save-create':
                    return $this->redirectToRoute('admin_project_create');
                case 'save-continue':
                case 'save-edit':
                    return $this->redirectToRoute('project_edit', $args);
                case 'save-exit':
                case 'save-default':
                default:
                    return $this->redirectToRoute('project_show', $args);
            }
            
            return $this->redirectToRoute('project_edit', $args);
            
        }
        
        $this->addFlash('error', 'Something went wrong.' . $form->getErrors());

        return $this->redirectToRoute('project_edit', [
            'slug' => $user->getSlug(),
            'id'   => $project->getId(), 
        ]);
    }
    
    /**
     * @Route("/{slug}/project/remove/{id}", name="remove", methods="POST")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function remove(User $user, Project $project)
    {
        $this->denyAccessUnlessGranted('remove', $project);
        
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($project);
        
        $entityManager->flush();
        
        return new Response('Project removed successfully');
    }
    
    /**
     * @Route("/{slug}/projects/{page<\d+>?1}", name="list", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     */
    public function list(User $user, PaginatorService $paginator, int $page)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        if($this->isGranted('ROLE_ADMIN') || ($user == $this->getUser()) ){
            $query = $entityManager->getRepository(Project::class)->findProjectsAndContributions($user);
        }else{
            $query = $entityManager->getRepository(Project::class)->findProjectsAndContributions($user, $this->getUser());
        }

        $projects = $paginator->paginate($query, 2);
        
        return $this->render('project/list.html.twig', [
            'user'     => $user,
            'projects' => $projects, 
        ]);
    }
}
