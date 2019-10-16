<?php

namespace App\Security;

use App\Entity\User; // your user entity
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\AccountDeletedException;
use App\Exception\AccountPingedException;
use App\Exception\AccountBlockedException;
use App\Exception\AccountCanceledException;
use App\Exception\EmailNotConfirmedException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FacebookAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    private $mailer;
    private $passwordEncoder;
    private $tokenGenerator;
    private $templating;
    private $session;

    public function __construct(SessionInterface $session, ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer, TokenGenerator $tokenGenerator, \Twig\Environment $templating)
    {
        $this->em = $em;
        $this->clientRegistry = $clientRegistry;
	    $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->templating = $templating;
        $this->session = $session;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()
            ->fetchUserFromToken($credentials);

        // 1) have they logged in with Facebook before? Easy!
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['facebookId' => $facebookUser->getId()]);
        if ($existingUser) {
            return $existingUser;
        }

        // 2) do we have a matching user by email?
        $email = $facebookUser->getEmail();
        $user = $this->em->getRepository(User::class)
                    ->findOneBy(['email' => $email]);
        
        $newAccount = false;
        $token = "";
        if (!$user) {
            $picture_url = $facebookUser->getPictureUrl();

            $plain_password = random_bytes(10);
            $username = str_replace('@', '', $email);
            $username = str_replace('.', '-', $username);

            $user = new User();
            $user->setEmail( $email );
            $user->setFullname( $facebookUser->getName() );
            $user->setPassword( $this->passwordEncoder->encodePassword($user, $plain_password) );
            $user->setUsername( $username );
            $user->setRoles( ['ROLE_USER'] );
            $user->setStatus( User::STATUS_PING );

            $user->setConfirmToken(null);
            $user->setConfirmedAt( new \DateTime() );
            
            $token = $this->tokenGenerator->generateToken();
            $user->setResetToken($token);
            $user->setResetedAt(new \DateTime());
            $newAccount = true;

        }
        
        // 3) Maybe you just want to "register" them by creating
        // a User object
        $user->setFacebookId($facebookUser->getId());
        $this->em->persist($user);
        $this->em->flush();

        if($newAccount){
            $url = $this->router->generate('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
 
            $message = (new \Swift_Message('Nouveau compte'))
                ->setFrom(array('joelinjatovo@gmail.com' => 'Admin'))
                ->setTo($user->getEmail());
            
            $message->setBody(
                $this->templating->render(
                    'security/emails/forgot.html.twig',
                    [
                        'user' => $user,
                        'url'  => $url,
                    ]
                ),
                'text/html'
            );
            $this->mailer->send($message);
        }

        return $user;
    }

    /**
     * @return FacebookClient
     */
    private function getFacebookClient()
    {
        return $this->clientRegistry
            // "facebook_main" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('facebook_main');
	}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate('account_profile');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if( ( $exception instanceof AccountDeletedException )
            || ( $exception instanceof AccountPingedException )
            || ( $exception instanceof AccountBlockedException )
            || ( $exception instanceof AccountCanceledException )
            || ( $exception instanceof EmailNotConfirmedException ) ){
            $message = $exception->getMessage();
        }else{
            $message = 'Une erreur s\'est produite. Veuillez réessayer, s\'il vous plaît';
        }

        $this->session->getFlashBag()->add('error', $message);

        $targetUrl = $this->router->generate('app_login');

        return new RedirectResponse($targetUrl);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    // ...
}