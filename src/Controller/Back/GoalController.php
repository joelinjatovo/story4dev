<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Goal;

/** @Route("/back", name="back_goal_") */
class GoalController extends AbstractController
{
    /**
     * @Route("/goal", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('back/goal/create.html.twig');
    }
    
    /**
     * @Route("/goal", name="create", methods="POST")
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
     * @Route("/goal/{id}", name="show", methods="GET", requirements={"id"="\d+"})
     */
    public function show(Goal $goal)
    {
        return $this->render('back/goal/show.html.twig', ['goal' => $goal]);
    }
    
    /**
     * @Route("/goal/edit/{id}", name="edit", methods="GET", requirements={"id"="\d+"})
     */
    public function edit(Goal $goal)
    {
        return $this->render('back/goal/edit.html.twig', ['goal' => $goal]);
    }
    
    /**
     * @Route("/goal/edit/{id}", name="update", methods="POST", requirements={"id"="\d+"})
     */
    public function update(Goal $goal)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $goal->setTitle('New goal name!');
        
        $entityManager->flush();

        return $this->redirectToRoute('back_goal_show', ['id' => $goal->getId()]);
    }
    
    /**
     * @Route("/goal/remove/{id}", name="remove", methods="POST")
     */
    public function remove(Goal $goal)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->remove($goal);
        
        $entityManager->flush();
        
        return new Response('goal removed successfully');
    }
    
    /**
     * @Route("/goals/{page<\d+>?1}", name="list", methods="GET")
     */
    public function list($page = 1)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $goals = $entityManager->getRepository(Goal::class)->findAll();
        
        return $this->render('back/goal/list.html.twig', ['goals' => $goals]);
    }
}
