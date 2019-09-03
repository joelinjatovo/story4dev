<?php

namespace App\Controller\Web;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Form\AccountProfileType;
use App\Form\AccountInfoType;
use App\Form\AccountPasswordType;
use App\Form\AccountNotificationType;
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
    public function profile(Request $request)
    {
        $user = $this->getUser();
        
        $form = $this->createForm(AccountProfileType::class, $user);
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
        
                $this->addFlash('success', 'Account profile successfully updated.');
            }else{
                $this->addFlash('error', 'Invalid request. Try again!');
            }
        }
        
        return $this->render('account/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/account/info", name="info")
     */
    public function info(Request $request)
    {
        $errors = [];
        $user = $this->getUser();
        
        $form = $this->createForm(AccountInfoType::class, $user);
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
        
                $this->addFlash('success', 'Account info successfully updated.');
            }else{
                $this->addFlash('error', 'Invalid request. Try again!');
            }
        }
        
        return $this->render('account/info.html.twig', [
            'user'   => $user,
            'form'   => $form->createView(),
            'errors' => $errors,
        ]);
    }
    
    /**
     * @Route("/account/password", name="password")
     */
    public function password(Request $request)
    {
        $user = $this->getUser();
        
        $form = $this->createForm(AccountInfoType::class);
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                
            }
        }
        
        return $this->render('account/password.html.twig', [
            'user'   => $user,
            'form'   => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/account/notification", name="notification")
     */
    public function notification(Request $request)
    {
        $user = $this->getUser();
        
        $form = $this->createForm(AccountInfoType::class);
        $form->handleRequest($request);
        if ( $form->isSubmitted() ) {
            if( $form->isValid()) {
                
            }
        }
        return $this->render('account/notification.html.twig', [
            'user'   => $user,
            'form'   => $form->createView(),
        ]);
    }
}
