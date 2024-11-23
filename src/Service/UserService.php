<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Infrastructure\RedisKeys;
use App\Service\Infrastructure\RedisService;
use App\Service\Infrastructure\S3Service;
use App\Service\Utility\FormatHelper;
use App\Service\Utility\MomentHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as BaseUserPasswordHasherInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\HttpFoundation\File\File;

class UserService
{
    private const AVATAR_DIR_NAME = 'users';

    public function __construct(
        private string                                   $publicUploadsDir,
        private S3Service                                $s3Service,
        private UserRepository                           $userRepository,
        private string                                   $cdnDomain,
        private string                                   $projectDir,
        private readonly BaseUserPasswordHasherInterface $passwordHasher,
        private RedisService                             $redisService,
    ) {
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function getUserByUid(string $uid, bool $cacheClear = false): ?User
    {
        if (!FormatHelper::isValidUid($uid)) {
            return null;
        }

        if ($cacheClear) {
            $this->userRepository->cacheClear();
        }

        return $this->userRepository->findOneBy(['ulid' => new Ulid($uid)]);
    }

    public function getUserById(string $id): ?User
    {
        return $this->userRepository->findOneBy(['id' => $id]);
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }

    public function saveUser(User $user): void
    {
        $this->userRepository->save($user);
    }

    public function getUserAvatarUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::AVATAR_DIR_NAME);
    }

    public function uploadAvatarImageToCDN(User $user): void
    {
        $oldFile = $user->getAvatar();
        $newFile = $user->getImageFile();

        if (!$newFile) {
            return;
        }
        $newFilePath = trim(sprintf('%s/%s', $this->getUserAvatarUploadDir(), $newFile), '/');
        $newFileFullPath = sprintf('%s/%s', $this->projectDir, $newFilePath);
        $fileObject = new File($newFileFullPath);
        $this->s3Service->uploadFileToCDN(
            file: $fileObject,
            newFilePath: $newFilePath,
        );
        $this->s3Service->deleteFileFromCdn($oldFile);
        unlink($newFileFullPath);

        //todo save only path without domain
        $cdnFile = sprintf('%s/%s', $this->cdnDomain, $newFilePath);
        $user->setAvatar($cdnFile);
        $this->userRepository->save($user);
    }

    public function changePassword(User $user, string $newPassword): User
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $newPassword),
        );

        $this->saveUser($user);

        $this->redisService->set(
            sprintf(RedisKeys::KEY_USER_NEED_RELOGIN, $user->getUidStr()),
            1,
            MomentHelper::SECONDS_MONTH,
            false,
        );

        $this->redisService->set(
            key: sprintf(RedisKeys::KEY_USER_RELOGIN_SSE, $user->getUidStr()),
            value: 1,
            ttl: MomentHelper::SECONDS_5_SEC,
            withPrefix: false,
        );

        return $user;
    }

    /**
     * @return User[]
     */
    public function getApprovedUsers(): array
    {
        return $this->userRepository->findBy([
            'isApproved' => true
        ]);
    }

    public function getAuthorsChoices(): array
    {
        $authorsChoices = [];
        $authors = $this->getApprovedUsers();
        foreach ($authors as $author) {
            $authorsChoices[$author->getUsername()] = $author->getId();
        }

        return $authorsChoices;
    }

    /**
     * @return User[]
     */
    public function getPayingUsers(): array
    {
        return $this->userRepository->findBy([
            'isPaying' => true
        ]);
    }

    /**
     * @return User[]
     */
    public function getVerifiedUsers(): array
    {
        return $this->userRepository->findBy([
            'isVerified' => true
        ], [
            'username' => 'ASC'
        ]);
    }

    public function updateUserTotalSumByPayment(Payment $payment): void
    {
        $buyer = $payment->getUser();
        $buyer->setIsPaying(true);
        if ($payment->isStripe()) {
            $buyer->addSum($payment->getAmount());
        }

        if ($payment->isCloudPayments()) {
            $buyer->addSumRu($payment->getAmountRub());
        }

        $this->saveUser($buyer);
        $this->redisService->invalidateUserCache($buyer);
    }

    public function updateUserLastActivity(User $user, ?\DateTimeImmutable $dateTime = null): void
    {
        if (!$dateTime) {
            $dateTime = new \DateTimeImmutable('now');
        }
        $user->setLastActivityAt($dateTime);
        $this->userRepository->save($user);
    }
}
