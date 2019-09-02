<?php

namespace App\Controller\Web;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/** 
 * @Route(name="account_")
 *
 * @IsGranted("ROLE_USER") 
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account/profile", name="profile")
     */
    public function profile()
    {
        return $this->render('account/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    
    /**
     * @Route("/account/info", name="info")
     */
    public function info(Request $request)
    {
        $errors = [];
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if ( ! $form->isValid() ) {
                $errors = $form->getErrors();
            }else{
                try{
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                    
                    $this->addFlash('success', "Account updated successfully.");
                }catch( \Exception $e){
                    $this->addFlash('error', $e->getMessage());
                }
            }
        }
        
        return $this->render('account/info.html.twig', [
            'user'   => $user,
            'errors' => $errors,
        ]);
    }
    
    /**
     * @Route("/account/password", name="password")
     */
    public function password(Request $request)
    {
        $user = $this->getUser();
        return $this->render('account/password.html.twig', [
            'user'   => $user,
        ]);
    }
    
    /**
     * @Route("/account/notification", name="notification")
     */
    public function notification(Request $request)
    {
        $user = $this->getUser();
        return $this->render('account/notification.html.twig', [
            'user'   => $user,
        ]);
    }
}
