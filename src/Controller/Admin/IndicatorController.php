<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Indicator;

/** @Route("/admin/indicator", name="admin_indicator_") */
class IndicatorController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/indicator/create.html.twig');
    }
    
    /**
     * @Route("/", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $indicator = new Indicator();
        
        $errors = $validator->validate($indicator);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($indicator);

        $entityManager->flush();

        return new Response('Saved new indicator with id '.$indicator->getId());
    }
    
    /**
     * @Route("/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Indicator $indicator)
    {
        return $this->render('admin/indicator/show.html.twig', ['indicator' => $indicator]);
    }
    
    /**
     * @Route("/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Indicator $indicator)
    {
        return $this->render('admin/indicator/edit.html.twig', ['indicator' => $indicator]);
    }
    
    /**
     * @Route("/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Indicator $indicator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $indicator->setTitle('New indicator name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('admin_indicator_show', ['id' => $indicator->getId()]);
    }
    
    /**
     * @Route("/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Indicator $indicator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($indicator);
        
        $entityManager->flush();
        
        return new Response('indicator removed successfully');
    }
    
    /**
     * @Route("s", name="list", defaults={"page": "1"}, methods="GET", requirements={"page"="\d+"})
     * @Route("s/page/{page}", name="list_paginated", methods="GET", requirements={"page"="\d+"})
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $indicators = $entityManager->getRepository(Indicator::class)->findAll();
        
        return $this->render('admin/indicator/list.html.twig', ['indicators' => $indicators]);
    }
}
