<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Project;
use App\Form\FeedbackType;
use App\Helper\MessageHelper;
use App\Service\OptionService;

/** 
 * @Route(name="app_")
 *
 */
class IndexController extends AbstractController
{
    /**
    * @Route("/mail", name="mail")
    */
    public function mail(\Swift_Mailer $mailer)
    {
        $data = [
            'user' => $this->getUser(),
            'url'   => "#",
            'label' => "Confirmer mon compte",
        ];
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('admin@story4dev.com')
            ->setTo('joelinjatovo@gmail.com')
            ->setBody(
                $this->renderView(
                    'emails/confirm.html.twig',
                    $data
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $this->render('emails/confirm.html.twig', $data);
    }

    /**
    * @Route("/", name="index")
    */
    public function index(Request $request)
    {
        if( $this->getUser() ){
            
            $contributions = $this->getUser()->getProjectContributions();
            if( $contributions->count() === 1 ){
                foreach($contributions as $contribution){
                    $project = $contribution->getProject();
                    return $this->redirectToRoute('project_show',[
                        'slug' => $project->getSlug(), 
                    ]);
                }
            }
            
            if( $contributions->count() === 0 ){
                return $this->redirectToRoute('account_profile');
            }
            
            return $this->redirectToRoute('project_list');
        }
        return $this->redirectToRoute('app_login');
    }
    
    /**
    * @Route("/faq", name="faq")
    */
    public function faq(Request $request)
    {
        return $this->render('faq/index.html.twig');
    }
    
    /**
    * @Route("/feedback", name="feedback")
    */
    public function feedback(Request $request, \Swift_Mailer $mailer, OptionService $optionService)
    {
        $form = $this->createForm(FeedbackType::class);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid() ) {
            $name    = $form->get('name')->getData();
            $phone   = $form->get('phone')->getData();
            $email   = $form->get('email')->getData();
            $message = $form->get('message')->getData();
            
            $body = $this->renderView('emails/feedback.html.twig',[
                    'url'     => '',
                    'label'   => "Voir",
                    'name'    => $name,
                    'phone'   => $phone,
                    'email'   => $email,
                    'message' => $message,
                ]
            );
            
            $message = MessageHelper::getMessage($optionService, 'Demande d\'aide de ' . $email, $body, 'text/html')
                ->setTo('joelinjatovo@gmail.com');
            
            $mailer->send($message);
            
            $this->addFlash('success', 'Votre Demande d\'aide a été bien envoyé aux responsables.');
            return $this->redirectToRoute('app_feedback');
        }
        
        return $this->render('feedback/index.html.twig', [
            'user'    => $this->getUser(), 
            'form'    => $form->createView()
        ]);
    }
}