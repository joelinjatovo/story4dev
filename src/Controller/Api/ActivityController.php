<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController ;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Activity;
use App\Entity\Project;
use App\Form\ActivityType;
use App\Service\FormError;

/**
 * @Version("v1")
 */
class ActivityController extends AbstractFOSRestController 
{
    /**
     * @Rest\Get("/activity/{activity_id}")
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function index(Activity $activity, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $activity);
        
        return $this->json(['data' => $activity], 200, [], ['groups' => ['activity']]);
    }
    
    /**
     * @Rest\Post("/activity", condition="request.attributes.get('version') == 'v1'")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);

        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->submit($data['payload']);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Activity created.'], JsonResponse::HTTP_CREATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/activity/{activity_id}")
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function update(Activity $activity, Request $reques, FormError $formErrort)
    {
        $this->denyAccessUnlessGranted('edit', $activity);
        
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ActivityType::class, $activity);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Activity updated.'], JsonResponse::HTTP_UPDATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * @Rest\Get("/project/{project_id}/activities")
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function list(Project $project, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $project);

        $repository = $this->getDoctrine()->getRepository(Activity::class);
        
        $activities = $repository->findBy(['project' => $project]);
        
        return $this->json(['data' => $activities], 200, [], ['groups' => ['activity']]);
    }
    
    /**
     * @Rest\Post("/activities")
     */
    public function listIn(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $activities = [];
        if( isset($data['payload']) && isset($data['payload']['activities']) ) {
            $ids = $data['payload']['activities'];
    
            $repository = $this->getDoctrine()->getRepository(Activity::class);

            foreach($ids as $id){
                $activity = $repository->find($id);

                if( ! $activity ) {
                    return $this->json(['status' => 'error', 'error' => "Entity not found."], JsonResponse::HTTP_BAD_REQUEST);
                }
                
                $this->denyAccessUnlessGranted('view', $activity);

                $activities[] = $activity;
            }
            
        }
        return $this->json(['data' => $activities], 200, [], ['groups' => ['activity']]);
    }
}