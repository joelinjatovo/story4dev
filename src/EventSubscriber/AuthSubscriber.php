<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use App\Entity\User;
use App\Entity\Session;

class AuthSubscriber implements EventSubscriberInterface {

    private $em;
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session) {
        $this->em = $em;
        $this->session = $session;
    }
    
    public function onLogin(InteractiveLoginEvent  $event) {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        
        // Check if user is logged in
        if (!$user instanceof User) {
            return;
        }
        
        $session = $this->em->getRepository(Session::class)->findOneBy(['sess_id' => $this->session->getId()]);
        
        if($session){
            $session->setUser($user);
            $session->setUpdateAt(new \DateTime());
            
            $this->em->persist($session);
            $this->em->flush();
        }
     }

    public static function getSubscribedEvents() {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [['onLogin', 20]],
        ];
    }

}