<?php

namespace App\Controller\Web;

use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function password(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        
        $form = $this->createForm(AccountPasswordType::class);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $data = $form->getData();
                
                $password = $data['password'];
                $new_password = $data['newpassword'];
                
                if ($passwordEncoder->isPasswordValid($user, $password)) {
                    $newEncodedPassword = $passwordEncoder->encodePassword($user, $new_password);
                    $user->setPassword($newEncodedPassword);
                    
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Votre mot de passe à bien été changé !');

                } else {
                    $this->addFlash('error', 'Ancien mot de passe incorrect');
                }
                
            }else{
                $this->addFlash('error', 'Invalid request. Try again!');
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
    
    /**
     * @Route("/account/forgot", name="forgot")
     */
    public function forgot(Request $request)
    {
        // Logging user out.
        $this->get('security.token_storage')->setToken(null);

        // Invalidating the session.
        $request->getSession()->invalidate();

        // Redirecting user to login page in the end.
        $response = $this->redirectToRoute('app_forgot_password');

        // Clearing the cookies.
        /*
        $cookieNames = [
            $this->container->getParameter('session.name'),
            $this->container->getParameter('session.remember_me.name'),
        ];
        foreach ($cookieNames as $cookieName) {
            $response->headers->clearCookie($cookieName);
        }
        */

        return $response;
    }
    
    
}
