<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

/** @Route("/admin/user", name="admin_user_") */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/user/create.html.twig');
    }
    
    /**
     * @Route("/", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new user with id '.$project->getId());
    }
    
    /**
     * @Route("/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(User $user)
    {
        return $this->render('admin/user/show.html.twig', ['user' => $user]);
    }
    
    /**
     * @Route("/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(User $user)
    {
        return $this->render('admin/user/edit.html.twig', ['user' => $user]);
    }
    
    /**
     * @Route("/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $project->setTitle('New user name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }
    
    /**
     * @Route("/remove/{id}", name="remove", methods="POST")
     */
    public function remove(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($user);
        
        $entityManager->flush();
        
        return new Response('User removed successfully');
    }
    
    /**
     * @Route("/list", name="list", defaults={"page": "1"}, methods="GET", requirements={"page"="\d+"})
     * @Route("/list/page/{page}", name="list_paginated", methods="GET", requirements={"page"="\d+"})
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $users = [];
        
        return $this->render('admin/user/list.html.twig', ['users' => $users]);
    }
}
