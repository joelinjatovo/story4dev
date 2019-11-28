<?php

namespace App\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use App\Entity\User;
use App\Events\UserCreatedEvent;

class UserSubscriber implements EventSubscriberInterface
{
    
    protected $twig;
    
    protected $mailer;
    
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['onKernelResponsePre', 10],
                ['onKernelResponsePost', -10],
            ],
            UserCreatedEvent::NAME => 'onUserCreated',
        ];
    }

    public function onKernelResponsePre(ResponseEvent $event)
    {
        // ...
    }

    public function onKernelResponsePost(ResponseEvent $event)
    {
        // ...
    }

    public function onUserCreated(UserCreatedEvent $event)
    {
        $user = $event->getUser();
        
        $body = $this->renderTemplate($user);
        
        $message = (new \Swift_Message('Utilisateur ' . $user->getId() . ' created'))
            ->setFrom('admin@story4dev.com')
            ->setTo('joelinjatovo@gmail.com')
            ->setBody($body, 'text/html')
        ;
        $this->mailer->send($message);
    }
    
    private function renderTemplate($user)
    {
        return $this->twig->render(
            'emails/user_created.html.twig',
            array(
                'user' => $user
            )
        );
    }
}
