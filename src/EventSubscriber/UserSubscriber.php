<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\Infrastructure\RedisService;
use App\Service\ModerationUserService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ModerationUserService  $moderationUserService,
        private UserService            $userService,
        private RedisService           $redisService,
        private EntityManagerInterface $em,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => [
                ['updateAvatar'],
                ['changeApprovedStatus'],
            ],
            AfterEntityUpdatedEvent::class  => [
                ['newPassword'],
                ['invalidateUserCache'],
            ],
        ];
    }

    public function invalidateUserCache(AbstractLifecycleEvent $event)
    {
        /** @var User $user */
        $user = $event->getEntityInstance();

        if (!($user instanceof User)) {
            return;
        }

        $this->redisService->invalidateUserCache($user);
    }

    public function updateAvatar(AbstractLifecycleEvent $event)
    {
        $user = $event->getEntityInstance();

        if (!($user instanceof User)) {
            return;
        }

        if (!$user->getImageFile()) {
            return;
        }

        $this->userService->uploadAvatarImageToCDN($user);
    }

    public function changeApprovedStatus(AbstractLifecycleEvent $event)
    {
        $user = $event->getEntityInstance();

        if (!($user instanceof User)) {
            return;
        }

        $uow = $this->em->getUnitOfWork();
        $uow->computeChangeSets();
        $changeset = $uow->getEntityChangeSet($user);
        if (!isset($changeset['approveStatus'])) {
            return;
        }

        $statusApproved = $changeset['approveStatus'][1];

        //прошёл проверку
        if ($statusApproved === User::APPROVE_STATUS_APPROVED) {
            $this->moderationUserService->approveUser($user);
        }

        //не прошёл проверку
        if ($statusApproved === User::APPROVE_STATUS_NOT_APPROVED) {
            $this->moderationUserService->declineUser($user);
        }

    }

    public function newPassword(AbstractLifecycleEvent $event)
    {
        $user = $event->getEntityInstance();

        if (!($user instanceof User)) {
            return;
        }

        if (!$user->getNewPassword()) {
            return;
        }

        $user = $this->userService->changePassword($user, $user->getNewPassword());
    }

}
