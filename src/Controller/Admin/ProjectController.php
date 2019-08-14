<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Project;
use App\Entity\Activity;
use App\Form\ActivityType;
use App\Service\PaginatorService;

/** @Route("/admin/project", name="admin_project_") */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/project/create.html.twig');
    }
    
    /**
     * @Route("/", name="create", methods="POST")
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
     * @Route("/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Project $project)
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        
        return $this->render('admin/project/show.html.twig', ['project' => $project, 'form' => $form->createView()]);
    }
    
    /**
     * @Route("/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Project $project)
    {
        return $this->render('admin/project/edit.html.twig', ['project' => $project]);
    }
    
    /**
     * @Route("/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Project $project)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $project->setTitle('New product name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('admin_project_show', ['id' => $product->getId()]);
    }
    
    /**
     * @Route("/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Project $project)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($product);
        
        $entityManager->flush();
        
        return new Response('Project removed successfully');
    }
    
    /**
     * @Route("s/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list(PaginatorService $paginator, int $page)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $query = $entityManager->getRepository(Project::class)->getAll();

        $projects = $paginator->paginate($query, 2);
        
        return $this->render('admin/project/list.html.twig', ['projects' => $projects]);
    }
}
