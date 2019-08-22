<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController ;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use App\Entity\Project;
use App\Form\ProjectType;

/**
 * @Version("v1")
 * @Route(name="api_")
 */
class ProjectController extends AbstractFOSRestController 
{
    /**
     * @Rest\Get("/projects", condition="request.attributes.get('version') == 'v1'")
     */
    public function list(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Project::class);
        $projects = $repository->findAll();
        
        return $this->json($projects, 200, [], [
            'groups' => ['project'],
        ]);
    }
    /**
     * @Rest\Get("/p")
     */
    public function p()
    {
        return $this->handleView($this->view(["p"]));
    }
    
    /**
     * @Rest\Post("/project", condition="request.attributes.get('version') == 'v1'")
     */
    public function create(Request $request)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }
        
        return $this->handleView($this->view($form->getErrors()));
    }
}