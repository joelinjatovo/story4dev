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
use App\Entity\Meta\ProjectMeta;
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
     * @Route("/project/{id}/dashboard", name="dashboard", methods="GET", requirements={"id"="\d+"})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function dashboard(Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);

        $user =$project->getAuthor();
        
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
     * @Route("/project/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function show(Project $project)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        $user = $project->getAuthor();

        $entityManager = $this->getDoctrine()->getManager();
        $reports = $entityManager->getRepository(Report::class)->findByProject($project)->execute();
        $data   =  $project->getData();
        $series =  $project->getSerie();
        
        return $this->render('project/show.html.twig', [
            'user'          => $user, 
            'project'       => $project, 
            'reports'       => $reports,
            'reports_count' => count($reports),  
            'data'          => json_encode($data),
            'series'        => json_encode($series),
        ]);
    }
    
    /**
     * @Route("/project/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function edit(Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();

        $form = $this->createForm(ProjectType::class, $project);
        
        return $this->render('project/edit.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/project/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function update(Project $project, Request $request, FormError $formError)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $user = $project->getAuthor();

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

            try{
                $metakeys = [
                    'header_bg_color',
                ];
                $colors = $request->request->get('colors');
                foreach($colors as $metakey => $color ){
                    if( ! in_array($metakey, $metakeys) ){
                        continue;
                    }

                    if(strlen($color) > 8 ){
                        continue;
                    }

                    $found = false;
                    foreach($project->getMetas() as $meta){
                        if($meta->getMetaKey() === $metakey ){
                            $found = true;
                            break;
                        }
                    }

                    if( ! $found || ! $meta ) {
                        $meta = new ProjectMeta();
                        $meta->setProject($project);
                        $meta->setMetaKey($metakey);
                    }

                    $meta->setMetaValue($color);

                    $entityManager->persist($meta);
                }
            }catch(\Exception $e){
            }

            $entityManager->flush();
        
            $this->addFlash('success', 'Votre modification a été bien sauvegardé.');
            
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
        
        $this->addFlash('error', 'Votre modification n\'a pas été sauvegardé. Une erreur s\'est produite. ' . $form->getErrors());

        return $this->redirectToRoute('project_edit', [
            'slug' => $user->getSlug(),
            'id'   => $project->getId(), 
        ]);
    }
    
    /**
     * @Route("/project/remove", name="remove", methods="POST")
     */
    public function remove(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $id = (int) $request->request->get('id');
            
            if( $id > 0 ) {
                $entityManager = $this->getDoctrine()->getManager();
                $project = $entityManager->getRepository(Project::class)->find($id);
                if( $project && ! $project->isDeleted()){
                    $this->denyAccessUnlessGranted('remove', $project);

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
     * @Route("/projects/{page<\d+>?1}", name="list", methods="GET")
     * @Route("/{slug}/projects/{page<\d+>?1}", name="list2", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     */
    public function list(?User $user = null, PaginatorService $paginator, int $page)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        if( $user && ( $this->isGranted('ROLE_ADMIN') ||  ( $user == $this->getUser() ) ) ) {
            $query = $entityManager->getRepository(Project::class)->findByContributor($user);
        }else{
            $query = $entityManager->getRepository(Project::class)->findByContributor($this->getUser());
        }

        $projects = $paginator->paginate($query, 10);
        
        return $this->render('project/list.html.twig', [
            'user'     => $user,
            'projects' => $projects, 
        ]);
    }
    
    /**
     * @Route("/project/color", name="color", methods="POST")
     */
    public function color(Request $request)
    {
        if ( $request->isXmlHttpRequest() ) {
            $metakey = $request->request->get('metakey');
            $color = $request->request->get('color');
            $id = $request->request->get('id');
            
            $entityManager = $this->getDoctrine()->getManager();
            $project = $entityManager->getRepository(Project::class)->find($id);
            if( $project ){
                $this->denyAccessUnlessGranted('edit', $project);

                $found = false;
                foreach($project->getMetas() as $meta){
                    if($meta->getMetaKey() === $metakey ){
                        $found = true;
                        break;
                    }
                }

                if( ! $found || ! $meta ) {
                    $meta = new ProjectMeta();
                    $meta->setProject($project);
                    $meta->setMetaKey($metakey);
                }

                $meta->setMetaValue($color);

                $entityManager->persist($meta);
                $entityManager->flush();
                
                return $this->json([
                    'success' => true,
                    'metakey' => $metakey,
                    'color'   => $color,
                    'message' => 'Color changed',
                ]);
            }
            
            return $this->json([
                'success' => false,
                'title'   => 'Invalid Request',
                'message' => 'An error was occured. :)',
            ]);
        }
    }
}
