<?php

namespace App\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Entity\User;
use App\Entity\Report;
use App\Form\AccountProfileType;
use App\Form\AccountInfoType;
use App\Form\AccountPasswordType;
use App\Service\PaginatorService;
use App\Service\TokenGenerator;

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
        
                $this->addFlash('success', 'Votre compte a été bien mis à jour.');
            }else{
                $this->addFlash('error', 'Votre demande est invalide! ' . $form->getErrors(true, true));
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
    public function info(Request $request, TokenGenerator $tokenGenerator, \Swift_Mailer $mailer)
    {
        $errors = [];

        $user = $this->getUser();

        $oldEmail = $user->getEmail();
        
        $form = $this->createForm(AccountInfoType::class, $user);
        
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() ) {
            if( $form->isValid() ) {
                $entityManager = $this->getDoctrine()->getManager();

                if($user->getEmail() != $oldEmail){
                    // send confirm email
                    $token = $tokenGenerator->generateToken();
                    $user->setConfirmToken( $token );
                    $user->setConfirmedAt( new \DateTime() );
                    $user->setFacebookId( null );
                    $user->setGoogleId( null );

                    $url = $this->generateUrl('app_confirm', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                    $message = (new \Swift_Message('Modification adresse email - Confirmation'))
                        ->setFrom(array('joelinjatovo@gmail.com'=> 'Admin'))
                        ->setTo($user->getEmail());
                    
                    $message->setBody(
                            $this->renderView(
                                'security/emails/confirm.html.twig',
                                [
                                    'user' => $user,
                                    'url'  => $url,
                                ]
                            ),
                            'text/html'
                        );
                    $mailer->send($message);
                }

                $entityManager->persist($user);
                $entityManager->flush();
        
                $this->addFlash('success', 'Votre compte a été bien mis à jour.');
                
                if($user->getEmail() != $oldEmail){
                    return $this->redirectToRoute('app_logout');
                }
            }else{
                $this->addFlash('error', 'Votre demande est invalide! ' . $form->getErrors(true, true));
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

                    $this->addFlash('success', 'Votre compte a été bien changé.');
                } else {
                    $this->addFlash('error', 'Ancien mot de passe incorrect.');
                }
        
                $this->addFlash('success', 'Votre compte a été bien mis à jour.');
            }else{
                $this->addFlash('error', 'Votre demande est invalide! ' . $form->getErrors(true, true));
            }
        }
        
        return $this->render('account/password.html.twig', [
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

        return $response;
    }
    
    /**
     * @Route("/{slug}/reports/{page<\d+>?1}", name="reports", methods="GET")
     * @Entity("user", options={"mapping": {"slug": "slug"}})
     */
    public function reports(User $user, $page = 1, PaginatorService $paginator)
    {
        $this->denyAccessUnlessGranted('list_reports', $user);
        
        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(Report::class)->findByUser($user);
        
        $reports = $paginator->paginate($query, 10);
        
        return $this->render('account/reports.html.twig', [
            'user'     => $user,
            'reports'  => $reports
        ]);
    }
    
    
}
