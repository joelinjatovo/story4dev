<?php

namespace App\Controller\Web\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\RegistrationFormType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Entity\User;
use App\Service\TokenGenerator;
use App\Form\AccountPasswordType;

class LoginFormController extends AbstractController
{
    private $view = 'security/login.html.twig';

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
        if ($this->getUser()) {
            //$this->redirectToRoute('account_profile');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render($this->view, [
            'last_username' => $lastUsername, 
            'error' => $error, 
            'active_form' => 'signin',
        ]);
    }
 
    /**
     * @Route("/register", name="app_register", methods="GET|POST")
     */
    public function register(Request $request, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, \Swift_Mailer $mailer): Response
    {
        if ( $request->isXmlHttpRequest() ) {
            $user = new User();
            $username = $request->request->get('email') ;
            $username = str_replace('@', '', $username);
            $username = str_replace('.', '-', $username);
            $user->setUsername( $username );
            $user->setEmail( $request->request->get('email') );
            $user->setAgree( (bool) $request->request->get('agree') );
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $request->request->get('password')
                )
            );
            
            $errors = $validator->validate($user);
            if ( count($errors) > 0) {
                return $this->json([
                    'success' => false,
                    'errors'  => $errors,
                    'error'   => (string) $errors
                ]);
            }

            $token = $tokenGenerator->generateToken();
            $user->setConfirmToken( $token );
            $user->setConfirmedAt( new \DateTime() );
            $user->setStatus(User::STATUS_PING);
            $user->setActive(false);
            $user->setRoles(['ROLE_USER']);
 
            $url = $this->generateUrl('app_confirm', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Nouveau compte - Confirmation'))
                ->setFrom(array('joelinjatovo@gmail.com'=> 'Admin'))
                ->setTo($user->getEmail());
            
            $message->setBody(
                    $this->renderView(
                        'security/emails/confirm.html.twig',
                        [
                            'user' => $user,
                            'url'  => $url,
                            //'logo' => $message->embed(\Swift_Image::fromPath(public_path().'/images/logo.png'))
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'message' => 'Thank you. To complete your registration please check your email.',
                'confirm_url' => $url
            ]);
        }

        return $this->render($this->view, [
            'last_username' => '',
            'error' => '',
            'active_form' => 'signup',
        ]);
    }
 
    /**
     * @Route("/confirm/{token}", name="app_confirm", methods="GET")
     */
    public function confirm(Request $request, String $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $user = $entityManager->getRepository(User::class)->findOneByConfirmToken($token);

        if ($user === null) {
            $this->addFlash('danger', 'Mot de passe non reconnu');
            return $this->redirectToRoute('app_index');
        }

        try{
            //$user->setStatus(User::STATUS_ACTIVE);
            //$user->setActive(true);
            $user->setConfirmToken(null);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('app_index');
        }
        
        $this->addFlash('notice', 'Mot de passe mis à jour !');
        
        return $this->redirectToRoute('app_login');
    }
    
    /**
     * @Route("/forgot", name="app_forgot_password", methods="GET|POST")
     */
    public function forgot(Request $request, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer): Response
    {
        if ($request->isMethod('post')) {
 
            $email = $request->request->get('email');
 
            $entityManager = $this->getDoctrine()->getManager();
        
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
 
            if ($user === null) {
                
                if ( $request->isXmlHttpRequest() ) {
                    return $this->json(array( 
                        'success'  => false,
                        'error'    => 'Email Inconnu, recommence !'
                    ));
                }
                
                $this->addFlash('danger', 'Email Inconnu, recommence !');
            
                return $this->redirectToRoute('app_forgot_password');
            }
            
            $token = $tokenGenerator->generateToken();
            try{
                $user->setResetToken($token);
                $user->setResetedAt(new \DateTime());
                $entityManager->flush();
            } catch (\Exception $e) {
                if ( $request->isXmlHttpRequest() ) {
                    return $this->json(array( 
                        'success' => false,
                        'error' => $e->getMessage()
                    ));
                }
                
                $this->addFlash('warning', $e->getMessage());

                return $this->redirectToRoute('app_index');
            }
 
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
 
            $message = (new \Swift_Message('Oubli de mot de passe - Réinitialisation'))
                ->setFrom(array('joelinjatovo@gmail.com' => 'Admin'))
                ->setTo($user->getEmail());
            
            $message->setBody(
                    $this->renderView(
                        'security/emails/forgot.html.twig',
                        [
                            'user' => $user,
                            'url'  => $url,
                            //'logo' => $message->embed(\Swift_Image::fromPath(public_path().'/images/logo.png'))
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
            
            if ( $request->isXmlHttpRequest() ) {
                return $this->json(array( 
                    'success'  => true,
                    'message' => 'Cool! Password recovery instruction has been sent to your email.',
                    'reset_url' => $url
                ));
            }
 
            $this->addFlash('notice', 'Cool! Password recovery instruction has been sent to your email.');
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render($this->view, [
            'last_username' => '',
            'error' => '',
            'active_form' => 'forgot',
        ]);
    }
 
    /**
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(String $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
        
        if ($user === null) {
            $this->addFlash('danger', 'Utilisateur non reconnu');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AccountPasswordType::class);
        $form->remove('password');
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $data = $form->getData();
                
                $new_password = $data['newpassword'];
                
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $new_password);

                $user->setPassword($newEncodedPassword);
                $user->setStatus(User::STATUS_ACTIVE);
                $user->setResetToken(null);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe à bien été changé !');

                return $this->render($this->view, [
                    'last_username' => '',
                    'error' => '',
                    'active_form' => 'forgot',
                ]);
                
            }else{
                $this->addFlash('error', 'Invalid request. Try again!');
            }
        }
        
        return $this->render('security/reset.html.twig', [
            'last_username' => '',
            'error' => '',
            'active_form' => 'signup',
            'form' => $form->createView()
        ]);
 
    }
}