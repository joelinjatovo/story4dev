<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Security;

use App\Entity\User;

class LastActivitySubscriber implements EventSubscriberInterface {

    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security) {
        $this->em = $em;
        $this->security = $security;
    }

    public function onCoreController(FilterControllerEvent $event){
        // Check that the current request is a "MASTER_REQUEST"
        // Ignore any sub-request
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }
        
        // Check token authentication availability
        if ($this->security->getToken()) {
            $user = $this->security->getToken()->getUser();

            if ( $user instanceof User ) {
                $user->setLastActivityAt(new \DateTime());
                $this->em->persist($user);
                $this->em->flush();
            }
        }
    }

    public static function getSubscribedEvents() {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            //KernelEvents::TERMINATE => [['onTerminate', 20]],
            KernelEvents::CONTROLLER => 'onCoreController',
        ];
    }

}