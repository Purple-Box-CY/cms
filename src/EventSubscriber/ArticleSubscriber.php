<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use App\Service\Infrastructure\RedisService;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleSubscriber implements EventSubscriberInterface
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

        if (!($entity instanceof Article)) {
            return;
        }

        $this->redisService->invalidateCacheArticle($entity);
    }
}