<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Unit;

/** @Route("/admin/unit", name="admin_unit_") */
class UnitController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/unit/create.html.twig');
    }
    
    /**
     * @Route("/", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $unit = new Unit();
        
        $errors = $validator->validate($unit);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($unit);

        $entityManager->flush();

        return new Response('Saved new unit with id '.$unit->getId());
    }
    
    /**
     * @Route("/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Unit $unit)
    {
        return $this->render('admin/unit/show.html.twig', ['unit' => $unit]);
    }
    
    /**
     * @Route("/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Unit $unit)
    {
        return $this->render('admin/unit/edit.html.twig', ['unit' => $unit]);
    }
    
    /**
     * @Route("/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Unit $unit)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $unit->setTitle('New unit name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('admin_unit_show', ['id' => $unit->getId()]);
    }
    
    /**
     * @Route("/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Unit $unit)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($unit);
        
        $entityManager->flush();
        
        return new Response('unit removed successfully');
    }
    
    /**
     * @Route("s", name="list", defaults={"page": "1"}, methods="GET", requirements={"page"="\d+"})
     * @Route("s/page/{page}", name="list_paginated", methods="GET", requirements={"page"="\d+"})
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $units = [];
        
        return $this->render('admin/unit/list.html.twig', ['units' => $units]);
    }
}
