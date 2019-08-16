<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Activity;
use App\Form\ActivityType;
use App\Form\ProjectType;
use App\Service\PaginatorService;

/** @Route(name="project_") */
class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('project/create.html.twig');
    }
    
    /**
     * @Route("/project", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $project = new Project();
        $project->setTitle('Keyboard');
        $project->setDescription('Ergonomic and stylish!');
        
        $errors = $validator->validate($project);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($project);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$project->getId());
    }
    
    /**
     * @Route("/{slug}/project/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function show(User $user, Project $project)
    {
        if($project->getAuthor() != $user ){
            throw $this->createNotFoundException('The author does not match');
        }
        
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        
        return $this->render('project/show.html.twig', [
            'user'    => $user, 
            'project' => $project, 
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/{slug}/project/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"id": "id"}})
     */
    public function edit(User $user, Project $project)
    {
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
    public function update(User $user, Request $request, Project $project)
    {
        //dump($request->request); exit;
        
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if ( ! $form->isValid() ) {
                
                $errors = [];
                foreach ($form->all() as $child) {
                    if (!$child->isValid()) {
                       $errors[$child->getName()] = (String) $form[$child->getName()]->getErrors();
                    }
                }
                
                return $this->json([
                    'success' => false,
                    'title'   => 'Validation Error',
                    'status'  => 'error',
                    'message' => 'An error was occured. :)',
                    'errors'  => $errors
                ]);
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();
            
            if ( $request->isXmlHttpRequest() ) {
                return $this->json([
                    'success' => true,
                    'title'   => 'Success',
                    'status'  => 'success',
                    'message' => 'Project updated successfully.',
                ]);
            }

            return $this->redirectToRoute('admin_project_edit', ['id' => $project->getId()]);
            
        }
        
        if ( $request->isXmlHttpRequest() ) {
            return $this->json([
                'success' => false,
                'title'   => 'Error',
                'status'  => 'error',
                'message' => 'Something went wrong. :)',
                'errors'  => [],
            ]);
        }

        return $this->redirectToRoute('project_show', [
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
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($product);
        
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
        
        $query = $entityManager->getRepository(Project::class)->getAllByAuthor($user);

        $projects = $paginator->paginate($query, 2);
        
        return $this->render('project/list.html.twig', [
            'user'     => $user,
            'projects' => $projects, 
        ]);
    }
}
