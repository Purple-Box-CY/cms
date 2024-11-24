<?php

namespace App\EventSubscriber;

use App\Entity\Marker;
use App\Service\Infrastructure\RedisService;
use App\Service\MarkerService;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MarkerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MarkerService $markerService,
        private RedisService $redisService,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityDeletedEvent::class  => [
                ['invalidateCache'],
            ],
            AfterEntityPersistedEvent::class => [
                ['uploadImage'],
                ['invalidateCache'],
            ],
            AfterEntityUpdatedEvent::class   => [
                ['uploadImage'],
                ['invalidateCache'],
            ],
            AfterEntityDeletedEvent::class   => ['invalidateCache'],
        ];
    }

    public function invalidateCache(AbstractLifecycleEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Marker)) {
            return;
        }

        $this->redisService->invalidateCacheMarker($entity);
    }


    public function uploadImage(AbstractLifecycleEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Marker)) {
            return;
        }

        //if (!$entity->getImage()) {
        //    return;
        //}

        if (!$entity->getImageFile()) {
            return;
        }

        $this->markerService->uploadMarkerImageToCDN($entity);
    }
}