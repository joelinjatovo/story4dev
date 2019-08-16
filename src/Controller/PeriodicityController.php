<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Periodicity;

/** @Route(name="periodicity_") */
class PeriodicityController extends AbstractController
{
    /**
     * @Route("/periodicity", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('periodicity/create.html.twig');
    }
    
    /**
     * @Route("/periodicity", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $periodicity = new Periodicity();
        
        $errors = $validator->validate($periodicity);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $entityManager->persist($periodicity);

        $entityManager->flush();

        return new Response('Saved new periodicity with id '.$periodicity->getId());
    }
    
    /**
     * @Route("/periodicity/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Periodicity $periodicity)
    {
        return $this->render('periodicity/show.html.twig', ['periodicity' => $periodicity]);
    }
    
    /**
     * @Route("/periodicity/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Periodicity $periodicity)
    {
        return $this->render('periodicity/edit.html.twig', ['periodicity' => $periodicity]);
    }
    
    /**
     * @Route("/periodicity/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Periodicity $periodicity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $periodicity->setTitle('New periodicity name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('periodicity_show', ['id' => $periodicity->getId()]);
    }
    
    /**
     * @Route("/periodicity/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Periodicity $periodicity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($periodicity);
        
        $entityManager->flush();
        
        return new Response('Periodicity removed successfully');
    }
    
    /**
     * @Route("/periodicities/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $periodicities = $entityManager->getRepository(Periodicity::class)->findAll();
        
        return $this->render('periodicity/list.html.twig', ['periodicities' => $periodicities]);
    }
}
