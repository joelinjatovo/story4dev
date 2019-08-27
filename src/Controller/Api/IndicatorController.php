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
use App\Entity\Activity;
use App\Form\IndicatorType;
use App\Service\FormError;

/**
 * @Version("v1")
 * @Route(name="api_")
 */
class IndicatorController extends AbstractFOSRestController 
{
    /**
     * @Rest\Get("/indicator/{indicator_id}")
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function index(Indicator $indicator, Request $request)
    {
        $this->denyAccessUnlessGranted('view', $indicator);
        
        return $this->json(['data' => $indicator], 200, [], ['groups' => ['indicator']]);
    }
    
    /**
     * @Rest\Post("/indicator", condition="request.attributes.get('version') == 'v1'")
     */
    public function create(Request $request, FormError $formError)
    {
        $data = json_decode($request->getContent(), true);

        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($indicator);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Indicator created.'], JsonResponse::HTTP_CREATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/indicator/{indicator_id}")
     * @Entity("indicator", options={"mapping": {"indicator_id": "id"}})
     */
    public function update(Indicator $indicator, Request $reques, FormError $formErrort)
    {
        $this->denyAccessUnlessGranted('edit', $indicator);
        
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(IndicatorType::class, $indicator);
        $form->submit($data->payload);
        
        if ( $form->isSubmitted() && $form->isValid() ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($indicator);
            $em->flush();
            
            return $this->json(['status' => 'ok', 'message' => 'Indicator updated.'], JsonResponse::HTTP_UPDATED);
        }
        
        return $this->json(['status' => 'error', 'errors' => $form->getErrors()], JsonResponse::HTTP_BAD_REQUEST);
    }
    
    /**
     * @Rest\Get("/activity/{activity_id}/indicators")
     * @Entity("activity", options={"mapping": {"activity_id": "id"}})
     */
    public function list(Activity $activity, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Indicator::class);
        
        $indicators = $repository->findBy(['activity' => $activity]);
        
        return $this->json(['data' => $indicators], 200, [], ['groups' => ['indicator']]);
    }
}