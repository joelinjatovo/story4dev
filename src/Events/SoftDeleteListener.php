<?php

namespace App\Events;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use App\Entity\Project;

class SoftDeleteListener
{
    public function preRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();

        if ($entity instanceof Project) {
            //$entity->setPosition(-1);

            $om = $event->getObjectManager();
            $om->persist($entity);
            $om->flush();
        }
    }
}
