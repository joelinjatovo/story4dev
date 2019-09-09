<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\User;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Service\FormError;
use App\Entity\Contribution\ProjectContribution;

/** 
 * @Route(name="admin_project_")
 *
 * @IsGranted("ROLE_ADMIN") 
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/admin/project", name="index", methods="GET")
     */
    public function index()
    {
        $user = $this->getUser();
        
        $project = new Project();
        
        $form = $this->createForm(ProjectType::class, $project);
        
        return $this->render('project/create.html.twig', [
            'user'    => $user,
            'project' => $project,
            'form'    => $form->createView()
        ]);
    }
    
    /**
     * @Route("/admin/project", name="create", methods="POST")
     */
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        
        $project = new Project();
        
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) {

            if( $project->getAuthor() == null ) {
                $project->setAuthor( $this->getUser() );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            
            $contribution = new ProjectContribution();
            $contribution->setUser( $project->getAuthor() );
            $contribution->setProject( $project );
            $contribution->setRoles(['ROLE_ADMIN']);
            $entityManager->persist( $contribution );

            $entityManager->flush();
        
            $this->addFlash('success', 'Project created succesfully.');

            return $this->redirectToRoute('project_edit', [
                'slug' => $project->getAuthor()->getSlug(),
                'id'   => $project->getId()
            ]);
            
        }
        
        $this->addFlash('error', 'Something went wrong.');

        return $this->redirectToRoute('admin_project_create');
    }
}
