<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        
        return $this->json([
            'status'  => 'success', 
            'message' => 'project_found', 
            'data'    => $project
        ], 200, [], ['groups' => ['project', 'date']]);
    }
}