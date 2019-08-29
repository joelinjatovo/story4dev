<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController ;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Indicator;
use App\Entity\Goal;
use App\Form\GoalType;
use App\Service\FormError;

/**
 * @Version("v1")
 */
class GoalController extends AbstractFOSRestController 
{
    /**
     * @Rest\Get("/goal/{goal_id}")
     * @Entity("goal", options={"mapping": {"goal_id": "id"}})
     */
    public function index(Goal $goal, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $goal);
        
        return $this->json(['data' => $goal], 200, [], ['groups' => ['goal']]);
    }
    
    /**
     * @Rest\Post("/goal", condition="request.attributes.get('version') == 'v1'")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);

        $goal = new Goal();
        $form = $this->createForm(GoalType::class, $goal);
        $form->submit($data['payload']);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($goal);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Goal created.'], JsonResponse::HTTP_CREATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/goal/{goal_id}")
     * @Entity("goal", options={"mapping": {"goal_id": "id"}})
     */
    public function update(Goal $goal, Request $reques, FormError $formErrort)
    {
        $this->denyAccessUnlessGranted('edit', $goal);

        $form = $this->createForm(GoalType::class, $goal);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($goal);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Goal updated.'], JsonResponse::HTTP_UPDATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * @Rest\Get("/indicator/{indicator_id}/goals")
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function list(Indicator $indicator, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Goal::class);
        
        $goals = $repository->findBy(['indicator' => $indicator]);
        
        return $this->json(['data' => $goals], 200, [], ['groups' => ['goal']]);
    }
}