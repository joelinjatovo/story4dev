<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Unit;

/** @Route("/admin", name="admin_unit_") */
class UnitController extends AbstractController
{
    /**
     * @Route("/unit", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('admin/unit/create.html.twig');
    }
    
    /**
     * @Route("/unit", name="create", methods="POST")
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
     * @Route("/unit/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Unit $unit)
    {
        return $this->render('admin/unit/show.html.twig', ['unit' => $unit]);
    }
    
    /**
     * @Route("/unit/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Unit $unit)
    {
        return $this->render('admin/unit/edit.html.twig', ['unit' => $unit]);
    }
    
    /**
     * @Route("/unit/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Unit $unit)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $unit->setTitle('New unit name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('admin_unit_show', ['id' => $unit->getId()]);
    }
    
    /**
     * @Route("/unit/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Unit $unit)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($unit);
        
        $entityManager->flush();
        
        return new Response('unit removed successfully');
    }
    
    /**
     * @Route("/units/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $units = $entityManager->getRepository(Unit::class)->findAll();
        
        return $this->render('admin/unit/list.html.twig', ['units' => $units]);
    }
}
