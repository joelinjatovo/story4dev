<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\User;
use App\Entity\Result;
use App\Entity\Project;
use App\Entity\Activity;
use App\Entity\Iteration;
use App\Form\ActivityType;
use App\Form\ProjectType;
use App\Form\ResultType;
use App\Service\PaginatorService;

/** 
 * @Route(name="result_")
 *
 * @IsGranted("ROLE_USER") 
 */
class ResultController extends AbstractController
{
    /**
     * @Route("/result", name="index", methods="GET")
     */
    public function index()
    {
        return $this->render('result/create.html.twig');
    }
    
    /**
     * @Route("/result", name="create", methods="POST")
     */
    public function create(ValidatorInterface $validator): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $result = new Result();
        $result->setTitle('Keyboard');
        $result->setDescription('Ergonomic and stylish!');
        
        $errors = $validator->validate($result);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($result);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new result with id '.$result->getId());
    }
}
