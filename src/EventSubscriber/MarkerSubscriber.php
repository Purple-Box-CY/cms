<?php

namespace App\EventSubscriber;

use App\Entity\Marker;
use App\Service\Infrastructure\RedisService;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MarkerSubscriber implements EventSubscriberInterface
{
    public function __construct(
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
                ['invalidateCache'],
            ],
            AfterEntityUpdatedEvent::class   => [
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
}