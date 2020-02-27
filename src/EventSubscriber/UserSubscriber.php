<?php

namespace App\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use App\Entity\User;
use App\Events\UserCreatedEvent;
use App\Events\UserChangedEvent;

class UserSubscriber implements EventSubscriberInterface
{
    
    protected $twig;
    protected $mailer;
    
    public function __construct(\Twig\Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onUserLoggedIn',
            UserCreatedEvent::NAME => 'onUserCreated',
            UserChangedEvent::ACTIVATED => 'onUserActivated',
        ];
    }

    public function onUserLoggedIn(InteractiveLoginEvent $event)
    {
        /** Do not notify on authenticated by API-TOKEN */
        $request = $event->getRequest();
        $uri = $request->getUri();
        if( ( strpos($uri, '/api/v1/') !== false ) && ( strpos($uri, '/api/v1/token') !== false ) ) {
            return;
        }
        
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        
        // Check if user is logged in
        if (!$user instanceof User) {
            return;
        }
        
        $body = $this->twig->render(
            'emails/user_logged_in.html.twig',
            array(
                'user' => $user,
                'uri' => $uri,
            )
        );
        
        $message = (new \Swift_Message('Nouvelle connexion sur Story4Dev'))
            ->setFrom(['admin@story4dev.com' => 'EventListener - Story4Dev'])
            ->setTo('joelinjatovo@gmail.com')
            ->setBody($body, 'text/html')
        ;
        $this->mailer->send($message);
    }

    public function onUserCreated(UserCreatedEvent $event)
    {
        $user = $event->getUser();
        
        $body = $this->twig->render(
            'emails/user_created.html.twig',
            array(
                'user' => $user
            )
        );
        
        $message = (new \Swift_Message('Nouvelle inscription sur Story4Dev'))
            ->setFrom(['admin@story4dev.com' => 'EventListener - Story4Dev'])
            ->setTo('joelinjatovo@gmail.com')
            ->setBody($body, 'text/html')
        ;
        $this->mailer->send($message);
    }

    public function onUserActivated(UserChangedEvent $event)
    {
        $user = $event->getUser();
        
        /** Send email to admin */
        $body = $this->twig->render('emails/user_activated-admin.html.twig',array('user' => $user));
        $message = (new \Swift_Message('Nouveau compte activé'))
            ->setFrom(['admin@story4dev.com' => 'EventListener - Story4Dev'])
            ->setTo('joelinjatovo@gmail.com')
            ->setBody($body, 'text/html')
        ;
        $this->mailer->send($message);
        
        /** Send email to client */
        $body = $this->twig->render('emails/user_activated-user.html.twig',array('user' => $user));
        $message = (new \Swift_Message('Inscription validée - Story4Dev'))
            ->setFrom(['admin@story4dev.com' => 'Admin - Story4Dev'])
            ->setTo([$user->getEmail() => $user->getFullName()])
            ->setBody($body, 'text/html')
        ;
        $this->mailer->send($message);
    }
}
