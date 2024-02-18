<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Cocur\Slugify\Slugify;
use App\Entity\Category;
use App\Entity\Product;
use DateTime;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class EntityChangedNotifier
{
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Category || $entity instanceof Product) {
            $entity->setSlug((new Slugify())->slugify($entity->getName()));
        }

        if ($entity instanceof Product) {
            $entity->setDateAdd(new DateTime('now'));
        }
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Category || $entity instanceof Product) {
            $entity->setSlug((new Slugify())->slugify($entity->getName()));
        }
    }
}
