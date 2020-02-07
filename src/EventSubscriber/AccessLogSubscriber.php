<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Security;

use App\Entity\User;
use App\Entity\AccessLog;

class AccessLogSubscriber implements EventSubscriberInterface {

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
        $user = null;
        if ($this->security->getToken()) {
            $user = $this->security->getToken()->getUser();
            if ( ! $user instanceof User ) {
                $user = null;
            }
        }
        
        $request = $event->getRequest();
        
        $access = new AccessLog();
        $access->setUrl($request->getUri());
        $access->setIp($request->getClientIp());
        $access->setUserAgent($request->headers->get('User-Agent'));
        $access->setReferer($request->server->get('HTTP_REFERER'));
        $access->setUser($user);
        
        $this->em->persist($access);
        $this->em->flush();
        
    }

    public static function getSubscribedEvents() {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            //KernelEvents::TERMINATE => [['onTerminate', 20]],
            KernelEvents::CONTROLLER => 'onCoreController',
        ];
    }

}