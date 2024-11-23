<?php

namespace App\Service\Infrastructure\Mail;

use App\Entity\Mail\Mail;
use App\Entity\Mail\MailType;
use App\Entity\User;
use App\Repository\MailRepository;
use App\Repository\UserRepository;
use App\Service\Exception\RedisQueueNotFoundException;
use App\Service\Infrastructure\LogService;
use App\Service\Infrastructure\RedisKeys;
use App\Service\Infrastructure\RedisService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{
    public function __construct(
        private MailerInterface             $mailer,
        private RedisService                $redisService,
        private ProjectEmailAddressProvider $emailAddressProvider,
        private MailRepository              $mailRepository,
        private UserRepository              $userRepository,
        private LogService                  $logger,
        private string                      $apiDomain,
    ) {
    }

    public function sendTemplateMail(
        string $email,
        string $subject,
        string $template, //'mail/test.html.twig'
        array  $context = [],
    ): void {
        $from = $this->emailAddressProvider->provide();

        $unsubscribeHash = $this->getUnsubscribeHash($email);
        $context['unsubscribe_hash'] = $unsubscribeHash;
        $unsubscribeLink = $this->apiDomain.'/api/user/settings/notifications/unsubscribe?id='.$unsubscribeHash;

        $mail = (new TemplatedEmail())
            ->from($from)
            ->to($email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->context($context)
            ->htmlTemplate($template);

        $headers = $mail->getHeaders();
        $headers->addHeader('List-Unsubscribe', $unsubscribeLink);
        $mail->setHeaders($headers);

        try {
            $this->mailer->send($mail);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send message',
                [
                    'error'            => $e->getMessage(),
                    'from'             => $from,
                    'to'               => $email,
                    'subject'          => $subject,
                    'template'         => $template,
                    'template_context' => $context,
                ]);
        }
    }

    public function getMailById(int $id): ?Mail
    {
        return $this->mailRepository->findOneBy(['id' => $id]);
    }

    private function saveMailError(Mail $mail, string $error): void
    {
        $mail->setError($error);
        $mail->setStatus(Mail::STATUS_ERROR);
        $this->saveMail($mail);
        $this->logger->error($error,
            [
                'mail_id' => $mail->getId(),
            ]);
        throw new \Exception($error);
    }

    public function getLastReadyMail(): ?Mail
    {
        return $this->mailRepository->findOneBy([
            'status' => Mail::STATUS_READY,
        ]);
    }

    public function skipMail(Mail $mail): Mail
    {
        $mail->setStatus(Mail::STATUS_SKIP);
        $this->saveMail($mail);

        return $mail;
    }

    /**
     * @throws \Throwable
     */
    public function sendMail(
        Mail $mail,
    ): Mail {
        $mail->setStatus(Mail::STATUS_PROCESS);
        $this->saveMail($mail);

        $subject = $this->getSubject($mail->getType());
        $template = $this->getTemplate($mail->getType());

        if (!$template) {
            $this->saveMailError($mail, sprintf('Template by type %s not found', $mail->getType()));
        }

        $emailTo = $this->getEmailTo($mail);
        if (!$emailTo) {
            $this->saveMailError($mail, sprintf('EmailTo by type %s not found', $mail->getType()));
        }

        if ($emailTo instanceof Address) {
            $emailTo = $emailTo->toString();
        }

        $context = $this->getContext($mail);

        $mail
            ->setSubject($subject)
            ->setTemplate($template)
            ->setEmailTo($emailTo)
            ->setContext($context);

        if (!$mail->isImportant()) {
            $user = $this->userRepository->getUserByEmail($emailTo);
            if ($user && $user->isUnsubscribed()) {
                $mail
                    ->setSentAt(new \DateTimeImmutable('now'))
                    ->setStatus(Mail::STATUS_UNSUBSCRIBED);

                return $this->saveMail($mail);
            }
        }

        try {
            $this->sendTemplateMail(
                email: $emailTo,
                subject: $subject,
                template: $template,
                context: $context,
            );
        } catch (\Throwable $e) {
            $this->saveMailError($mail, $e->getMessage());
        }

        $mail
            ->setSentAt(new \DateTimeImmutable('now'))
            ->setStatus(Mail::STATUS_SUCCESS);

        return $this->saveMail($mail);
    }

    /**
     * @throws \Throwable
     * @throws RedisQueueNotFoundException
     */
    public function addMailToQueue(Mail $mail): Mail
    {
        try {
            $this->redisService->pushToQueue(RedisKeys::QUEUE_MAIL,
                serialize([
                    'id' => $mail->getId(),
                ]));
        } catch (\Throwable $e) {
            $this->logger->error('Failed to add mail to queue',
                [
                    'mail_id' => $mail->getId(),
                    'error'   => $e->getMessage(),
                ]);

            $mail
                ->setStatus(Mail::STATUS_ERROR)
                ->setError($e->getMessage());
            $this->saveMail($mail);

            throw $e;
        }

        $mail->setStatus(Mail::STATUS_READY);

        return $this->saveMail($mail);
    }

    public function saveMail(Mail $mail): Mail
    {
        return $this->mailRepository->save($mail);
    }

    private function getSubject(string $mailType): string
    {
        return MailSubject::SUBJECTS_BY_TYPES[$mailType] ?? 'New message';
    }

    public function getTemplate(string $mailType): ?string
    {
        return MailTemplate::TEMPLATES_BY_TYPES[$mailType] ?? null;
    }

    public function getEmailTo(Mail $mail): ?string
    {
        if ($mail->getEmailTo()) {
            return $mail->getEmailTo();
        }

        $context = $mail->getContext();
        $userId = $context['user_id'] ?? null;
        switch ($mail->getType()) {
            case MailType::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX:
            case MailType::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX:
                $userId = $context['creator_id'] ?? null;
                break;
        }

        if (!$userId) {
            return null;
        }

        $user = $this->userRepository->getUserById($userId);
        if (!$user) {
            return null;
        }

        return $user->getEmail();
    }

    private function getUnsubscribeHash(string $emailTo): string
    {
        return base64_encode($emailTo);
    }

    private function getContext(Mail $mail): array
    {
        $context = $mail->getContext();
        $context['user_name'] = $context['user_name'] ?? '';

        if (isset($context['user_id'])) {
            $user = $this->userRepository->getUserById($context['user_id']);
            if ($user) {
                $context['user_name'] = $user->getFullName();
            }
        }

        return $context;
    }

    private function createMail(
        string $email,
        string $type,
        array  $context = [],
    ): Mail {
        $mail = new Mail();
        $mail
            ->setEmailTo($email)
            ->setType($type)
            ->setContext($context);

        return $this->mailRepository->save($mail);
    }

    private function createAndAddToQueue(
        string $email,
        string $type,
        array  $context,
    ): ?Mail {
        try {
            $mail = $this->createMail(
                email: $email,
                type: $type,
                context: $context,
            );
        } catch (\Throwable $e) {
            $this->logger->error('Failed to create mail',
                [
                    'email'   => $email,
                    'type'    => $type,
                    'context' => $context,
                    'error'   => $e->getMessage(),
                ]);

            return null;
        }
        try {
            $mail = $this->addMailToQueue($mail);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to add mail to queue',
                [
                    'email'   => $email,
                    'type'    => $type,
                    'context' => $context,
                    'mail_id' => $mail->getId(),
                    'error'   => $e->getMessage(),
                ]);

            $mail
                ->setStatus(Mail::STATUS_ERROR)
                ->setError($e->getMessage());
            $this->saveMail($mail);

            return null;
        }

        return $mail;
    }

    public function sendMailUserApproved(
        User $user,
    ): ?Mail {
        $context = [
            'user_name' => $user->getFullName(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_APPROVED,
            context: $context,
        );
    }

    public function sendMailUserAvatarApproved(
        User $user,
    ): ?Mail {
        $context = [
            'user_name' => $user->getFullName(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_AVATAR_APPROVED,
            context: $context,
        );
    }

    public function sendMailUserAvatarDeclined(
        User $user,
    ): ?Mail {
        $context = [
            'user_name'           => $user->getFullName(),
            'decline_reason'      => $user->getAvatarDeclineReasonName(),
            'decline_description' => $user->getAvatarDeclineDescription(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_AVATAR_DECLINED,
            context: $context,
        );
    }

    public function sendMailUserImageProfileApproved(
        User $user,
    ): ?Mail {
        $context = [
            'user_name' => $user->getFullName(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_IMAGE_PROFILE_APPROVED,
            context: $context,
        );
    }

    public function sendMailUserImageProfileDeclined(
        User $user,
    ): ?Mail {
        $context = [
            'user_name'           => $user->getFullName(),
            'decline_reason'      => $user->getImageProfileDeclineReasonName(),
            'decline_description' => $user->getImageProfileDeclineDescription(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_IMAGE_PROFILE_DECLINED,
            context: $context,
        );
    }

    public function sendMailUserAudioProfileApproved(
        User $user,
    ): ?Mail {
        $context = [
            'user_name' => $user->getFullName(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_AUDIO_PROFILE_APPROVED,
            context: $context,
        );
    }

    public function sendMailUserAudioProfileDeclined(
        User $user,
    ): ?Mail {
        $context = [
            'user_name'           => $user->getFullName(),
            'decline_reason'      => $user->getAudioProfileDeclineReasonName(),
            'decline_description' => $user->getAudioProfileDeclineDescription(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_AUDIO_PROFILE_DECLINED,
            context: $context,
        );
    }

    public function sendMailUserDeclined(
        User $user,
    ): ?Mail {
        $context = [
            'user_name'           => $user->getFullName(),
            'decline_reason'      => $user->getDeclineReasonName(),
            'decline_description' => $user->getDeclineDescriptionStr(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_DECLINED,
            context: $context,
        );
    }

    public function sendMailUserBlocked(
        User $user,
    ): ?Mail {
        $context = [
            'user_name' => $user->getFullName(),
            'reason'    => $user->getBlockReasonName(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_BLOCKED,
            context: $context,
        );
    }

    public function sendMailUserUnblocked(
        User $user,
    ): ?Mail {
        $context = [
            'user_name' => $user->getFullName(),
        ];

        return $this->createAndAddToQueue(
            email: $user->getEmail(),
            type: MailType::USER_UNBLOCKED,
            context: $context,
        );
    }

}
