<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Infrastructure\Mail\MailService;
use App\Service\Infrastructure\RedisService;

readonly class ModerationUserService
{
    public function __construct(
        private MailService                $mailService,
        private RedisService               $redisService,
        private UserRepository             $userRepository,
    ) {
    }

    public function getCountUsersWaitModeration(): int
    {
        return $this->userRepository->count(['approveStatus' => User::APPROVE_STATUS_WAITING_FOR_APPROVE]);
    }

    public function getCountUsersAvatarsWaitModeration(): int
    {
        return $this->userRepository->count(['avatarStatus' => User::AVATAR_STATUS_WAITING_APPROVE]);
    }

    public function approveUser(User $user): void
    {
        $user
            ->setIsApproved(true)
            ->setApproveStatus(User::APPROVE_STATUS_APPROVED)
            ->setDeclineReasonStr(null)
            ->setDeclineDescriptionStr(null);

        $this->userRepository->save($user);

        $this->mailService->sendMailUserApproved($user);
        $this->redisService->invalidateUserCache($user);
    }

    public function declineUser(
        User    $user,
        ?string $declineReason = null,
        ?string $declineDescription = null,
    ): void {
        $user
            ->setIsApproved(false)
            ->setApproveStatus(User::APPROVE_STATUS_NOT_APPROVED)
            ->setDeclineReasonStr($declineReason)
            ->setDeclineDescriptionStr($declineDescription);

        $this->userRepository->save($user);

        $this->mailService->sendMailUserDeclined($user);

        $this->redisService->invalidateUserCache($user);
    }

    public function blockUser(User $user, ?string $blockReason = null): void
    {
        $user->setIsBlocked(true);
        if ($blockReason) {
            $user->setBlockReasonStr($blockReason);
        }

        $this->userRepository->save($user);

        $this->redisService->invalidateUserCache($user);

        $this->mailService->sendMailUserBlocked($user);
    }

    public function unblockUser(User $user): void
    {
        $user->setIsBlocked(false);
        $user->setBlockReasonStr('');

        $this->userRepository->save($user);

        $this->mailService->sendMailUserUnblocked($user);
    }

    public function approveUserAvatar(User $user): void
    {
        $user
            ->setAvatarStatus(User::AVATAR_STATUS_ACTIVE)
            ->setAvatar($user->getAvatarData()->getCrop());

        $this->userRepository->save($user);

        $this->mailService->sendMailUserAvatarApproved($user);

        $this->redisService->invalidateUserCache($user);
    }

    public function declineUserAvatar(
        User    $user,
        ?string $declineReason = null,
        ?string $declineDescription = null,
    ): void {
        $user
            ->setAvatarStatus(User::AVATAR_STATUS_BLOCKED)
            ->setAvatar($user->getAvatarData()->getCropBlur())
            ->setAvatarDeclineReason($declineReason)
            ->setAvatarDeclineDescription($declineDescription);

        $this->userRepository->save($user);

        $this->mailService->sendMailUserAvatarDeclined($user);

        $this->redisService->invalidateUserCache($user);
    }

}
