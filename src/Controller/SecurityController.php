<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;

class SecurityController extends AbstractController
{

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
 
    /**
     * @Route("/forgot", name="app_forgot_password", methods="GET|POST")
     */
    public function forgot(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer): Response
    {
        if ($request->isMethod('post')) {
 
            $email = $request->request->get('email');
 
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
 
            if ($user === null) {
                if ( $request->isXmlHttpRequest() ) {
                    return $this->json(array( 
                        'success'  => false,
                        'message' => 'Email Inconnu, recommence !'
                    ));
                }
                
                $this->addFlash('danger', 'Email Inconnu, recommence !');
                return $this->redirectToRoute('app_forgot_password');
            }
            
            /*
            $token = $this->tokenGenerator->generateToken();
            try{
                //$user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                if ( $request->isXmlHttpRequest() ) {
                    return $this->json(array( 
                        'success'  => false,
                        'message' => $e->getMessage()
                    ));
                }
                
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('home');
            }
 
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
 
            $message = (new \Swift_Message('Oubli de mot de passe - Réinisialisation'))
                ->setFrom(array('joelinjatovo@gmail.com'=> 'Admin'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'security/emails/forgot.html.twig',
                        [
                            'user'=>$user,
                            'url'=>$url
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
            */
            
            if ( $request->isXmlHttpRequest() ) {
                return $this->json(array( 
                    'success'  => true,
                    'message' => 'Cool! Password recovery instruction has been sent to your email.'
                ));
            }
 
            $this->addFlash('notice', 'Cool! Password recovery instruction has been sent to your email.');
            
            return $this->redirectToRoute('app_login');
        }
 
        return $this->redirectToRoute('app_login');
    }
 
    /**
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(Request $request, String $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        //Reset avec le mail envoyé
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
 
            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
 
            if ($user === null) {
                $this->addFlash('danger', 'Mot de passe non reconnu');
                return $this->redirectToRoute('home');
            }
 
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();
 
            $this->addFlash('notice', 'Mot de passe mis à jour !');
 
            return $this->redirectToRoute('app_login');
        }else {
            return $this->redirectToRoute('app_login');
        }
 
    }
}