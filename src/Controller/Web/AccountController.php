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
     * @Route("/account/edit", name="edit")
     * @Route("/account/profile", name="profile")
     * @Route("/account/edit-profile", name="edit_profile")
     */
    public function profile()
    {
        return $this->render('account/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    
    /**
     * @Route("/account/edit-info", name="edit_info")
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
        
        return $this->render('account/edit_info.html.twig', [
            'user'   => $user,
            'errors' => $errors,
        ]);
    }
    
    /**
     * @Route("/account/edit-password", name="edit_password")
     */
    public function password(Request $request)
    {
        $user = $this->getUser();
        return $this->render('account/edit_password.html.twig', [
            'user'   => $user,
        ]);
    }
    
    /**
     * @Route("/account/edit-notification", name="edit_notification")
     */
    public function notification(Request $request)
    {
        $user = $this->getUser();
        return $this->render('account/edit_notification.html.twig', [
            'user'   => $user,
        ]);
    }
}
