<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
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

/** @Route(name="report_") */
class ReportController extends AbstractController
{
    /**
     * @Route("/{slug}/project/{project_id}/report", name="index", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function index(User $user, Project $project)
    {
        return $this->render('report/create.html.twig');
    }
    
    /**
     * @Route("/{slug}/project/{project_id}/report", name="create", methods="POST")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     * @Entity("project", options={"mapping": {"project_id": "id"}})
     */
    public function create(User $user, Project $project, ValidatorInterface $validator): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $report = new Report();
        $report->setTitle('Keyboard');
        $report->setDescription('Ergonomic and stylish!');
        
        $errors = $validator->validate($report);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($report);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new report with id '.$report->getId());
    }
}
