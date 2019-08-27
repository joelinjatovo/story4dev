<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController ;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Service\FormError;

/**
 * @Version("v1")
 * @Route(name="api_")
 */
class ProjectController extends AbstractFOSRestController 
{
    /**
     * @Rest\Get("/project/{project_id}")
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function index(Project $project, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $project);
        
        return $this->json(['data' => $project], 200, [], ['groups' => ['project']]);
    }
    
    /**
     * @Rest\Post("/project", condition="request.attributes.get('version') == 'v1'")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Project created.'], JsonResponse::HTTP_CREATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/project/{project_id}")
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function update(Project $project, Request $reques, FormError $formErrort)
    {
        $this->denyAccessUnlessGranted('edit', $project);
        
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Project updated.'], JsonResponse::HTTP_UPDATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * @Rest\Get("/projects")
     */
    public function list(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Project::class);
        
        $projects = $repository->findAll();
        
        return $this->json(['data' => $projects], 200, [], ['groups' => ['project']]);
    }
}