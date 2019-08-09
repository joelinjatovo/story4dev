<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Goal;

/** @Route("/admin/goal", name="admin_goal_") */
class GoalController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/goal/create.html.twig');
    }
    
    /**
     * @Route("/", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $goal = new Goal();
        
        $errors = $validator->validate($goal);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($goal);

        $entityManager->flush();

        return new Response('Saved new goal with id '.$goal->getId());
    }
    
    /**
     * @Route("/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Goal $goal)
    {
        return $this->render('admin/goal/show.html.twig', ['goal' => $goal]);
    }
    
    /**
     * @Route("/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Goal $goal)
    {
        return $this->render('admin/goal/edit.html.twig', ['goal' => $goal]);
    }
    
    /**
     * @Route("/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Goal $goal)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $goal->setTitle('New goal name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('admin_goal_show', ['id' => $goal->getId()]);
    }
    
    /**
     * @Route("/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Goal $goal)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($goal);
        
        $entityManager->flush();
        
        return new Response('goal removed successfully');
    }
    
    /**
     * @Route("s", name="list", defaults={"page": "1"}, methods="GET", requirements={"page"="\d+"})
     * @Route("s/page/{page}", name="list_paginated", methods="GET", requirements={"page"="\d+"})
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $goals = [];
        
        return $this->render('admin/goal/list.html.twig', ['goals' => $goals]);
    }
}
