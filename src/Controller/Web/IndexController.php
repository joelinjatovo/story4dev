<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Project;

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
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('admin@story4dev.com')
            ->setTo('haja@emediaplace.com')
            ->setBody(
                $this->renderView(
                    'security/emails/test.html.twig'
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $this->render('faq/index.html.twig');
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
                        'slug' => $project->getAuthor()->getSlug(),
                        'id'   => $project->getId(),
                    ]);
                }
            }
            
            if( $contributions->count() === 0 ){
                return $this->redirectToRoute('account_profile');
            }
            
            return $this->redirectToRoute('project_list',[
                'slug' => $this->getUser()->getSlug()
            ]);
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
    public function feedback(Request $request)
    {
        return $this->render('feedback/index.html.twig');
    }
}