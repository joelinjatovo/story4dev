<?php

namespace App\Security;

use App\Entity\User;
use App\Events\UserCreatedEvent;
use App\Events\UserLoggedInEvent;
use App\Helper\MessageHelper;
use App\Service\TokenGenerator;
use App\Service\OptionService;
use App\Exception\AccountDeletedException;
use App\Exception\AccountPingedException;
use App\Exception\AccountBlockedException;
use App\Exception\AccountCanceledException;
use App\Exception\EmailNotConfirmedException;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class GoogleAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    private $mailer;
    private $passwordEncoder;
    private $tokenGenerator;
    private $templating;
    private $session;
    private $optionService;
    private $targetDirectory;
    private $dispatcher;

    public function __construct($targetDirectory, OptionService $optionService, SessionInterface $session, ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer, TokenGenerator $tokenGenerator, \Twig\Environment $templating, EventDispatcherInterface $dispatcher)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->templating = $templating;
        $this->session = $session;
        $this->optionService = $optionService;
        $this->targetDirectory = $targetDirectory;
        $this->dispatcher = $dispatcher;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        
        $newAccount = false;
        if (!$user) {
            $plain_password = random_bytes(10);
            $username = str_replace('@', '', $email);
            $username = str_replace('.', '-', $username);

            $user = new User();
            $user->setEmail($googleUser->getEmail());
            $user->setFullname($googleUser->getName());
            $user->setPassword( $this->passwordEncoder->encodePassword($user, $plain_password) );
            $user->setUsername( $username );
            $user->setRoles( ['ROLE_USER'] );
            $user->setStatus( User::STATUS_PING );

            $user->setConfirmToken(null);
            $user->setConfirmedAt( new \DateTime() );
            
            $token = $this->tokenGenerator->generateToken();
            $user->setResetToken($token);
            $user->setResetedAt(new \DateTime());
            
            $picture_url = $googleUser->getAvatar();
            $finalName = md5(uniqid(rand(), true))."_avatar.jpg";
            $newfile = $this->targetDirectory . '/user/'.$finalName;
            if ( copy($picture_url, $newfile) ) {
                $user->setAvatar($finalName);
            }

            $newAccount = true;
        }
        
        $user->setGoogleId($googleUser->getId());
        $this->em->persist($user);
        $this->em->flush();

        if($newAccount){
            $url = $this->router->generate('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
            
            $body = $this->templating->render('emails/forgot.html.twig', [
                    'user'  => $user,
                    'url'   => $url,
                    'label' => "Créer mot de passe",
                ]
            );
            
            $message = MessageHelper::getMessage($this->optionService, 'Nouvelle inscription', $body, 'text/html')
                ->setTo($user->getEmail());
            
            $this->mailer->send($message);
            
            // creates the UserCreatedEvent and dispatches it
            $event = new UserCreatedEvent($user);
            $this->dispatcher->dispatch($event, UserCreatedEvent::NAME);
        }

        return $user;
    }

    /**
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2Client
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry
            ->getClient('google');
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * This is called when an anonymous request accesses a resource that
     * requires authentication. The job of this method is to return some
     * response that "helps" the user start into the authentication process.
     *
     * Examples:
     *  A) For a form login, you might redirect to the login page
     *      return new RedirectResponse('/login');
     *  B) For an API token authentication system, you return a 401 response
     *      return new Response('Auth header required', 401);
     *
     * @param Request $request The request that resulted in an AuthenticationException
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $authException The exception that started the authentication process
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function start(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @param Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationFailure(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception)
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
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param Request $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, $providerKey)
    {
        // creates the UserLoggedInEvent and dispatches it
        $user = $token->getUser();
        $event = new UserLoggedInEvent($user);
        $this->dispatcher->dispatch($event, UserLoggedInEvent::NAME);
        
        $targetUrl = $this->router->generate('app_index');

        return new RedirectResponse($targetUrl);
    }
}