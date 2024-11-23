<?php

namespace App\Entity\Mail;

use App\Repository\MailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MailRepository::class)]
#[ORM\Table(name: "mails")]
class Mail
{
    public const STATUS_NEW = 'new';
    public const STATUS_READY = 'ready';
    public const STATUS_PROCESS = 'process';
    public const STATUS_SKIP = 'skip';
    public const STATUS_ERROR = 'error';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    public const AVAILABLE_STATUSES = [
        self::STATUS_NEW          => self::STATUS_NEW,
        self::STATUS_READY        => self::STATUS_READY,
        self::STATUS_PROCESS      => self::STATUS_PROCESS,
        self::STATUS_SKIP         => self::STATUS_SKIP,
        self::STATUS_ERROR        => self::STATUS_ERROR,
        self::STATUS_SUCCESS      => self::STATUS_SUCCESS,
        self::STATUS_UNSUBSCRIBED => self::STATUS_UNSUBSCRIBED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private string $status;

    #[ORM\Column(length: 255)]
    private string $emailTo;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $template = null;

    #[ORM\Column]
    private array $context = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $error = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->status = self::STATUS_NEW;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isSent(): bool
    {
        return $this->status == self::STATUS_SUCCESS;
    }

    public function getEmailTo(): string
    {
        return $this->emailTo;
    }

    public function setEmailTo(string $emailTo): self
    {
        $this->emailTo = $emailTo;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getContextJson(): string
    {
        return json_encode($this->context);
    }

    public function setContextJson(string $contextString): static
    {
        $this->setContext(json_decode($contextString, true));

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isImportant(): bool
    {
        return in_array($this->getType(), MailType::IMPORTANT_MAILS);
    }

    public function needRepeat(): bool
    {
        if ($this->type != MailType::CONFIRMATION_REGISTRATION) {
            return false;
        }

        $mailDomains = [
            '@outlook.com',
            '@hotmail.com',
            //'@yahoo.com',
        ];

        foreach ($mailDomains as $mailDomain) {
            if (str_contains($this->emailTo, $mailDomain)) {
                return true;
            }
        }

        return false;
    }
}
