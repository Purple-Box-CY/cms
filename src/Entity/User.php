<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\NotAcceptableValueException;
use App\Service\Utility\DomainHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use App\Repository\UserRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Factory\UlidFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
class User implements PasswordAuthenticatedUserInterface
{

    private const string CONFIG_NOTIFICATIONS_UNSUBSCRIBED = 'notifications_unsubscribed';

    private const SUPPORT_USERNAME = 'support';

    public const STATUS_ACTIVE  = 'active';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_DELETED = 'deleted';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_BLOCKED,
        self::STATUS_DELETED,
    ];

    public const APPROVE_STATUS_NEED_APPROVE = 'need_approve';
    public const APPROVE_STATUS_WAITING_FOR_APPROVE = 'waiting_for_approve';
    public const APPROVE_STATUS_NOT_APPROVED = 'not_approved';
    public const APPROVE_STATUS_APPROVED = 'approved';
    public const APPROVE_STATUSES = [
        self::APPROVE_STATUS_NEED_APPROVE,
        self::APPROVE_STATUS_WAITING_FOR_APPROVE,
        self::APPROVE_STATUS_NOT_APPROVED,
        self::APPROVE_STATUS_APPROVED,
    ];
    public const APPROVE_STATUSES_NAMES = [
        self::APPROVE_STATUS_NEED_APPROVE => 'Need approve',
        self::APPROVE_STATUS_WAITING_FOR_APPROVE => 'Waiting for approve',
        self::APPROVE_STATUS_NOT_APPROVED => 'Not approved',
        self::APPROVE_STATUS_APPROVED => 'Approved',
    ];

    public const AVATAR_STATUS_NEW             = 'new';
    public const AVATAR_STATUS_WAITING_APPROVE = 'waiting_approve';
    public const AVATAR_STATUS_ACTIVE          = 'active';
    public const AVATAR_STATUS_BLOCKED         = 'blocked';

    public const AVAILABLE_AVATAR_STATUSES = [
        self::AVATAR_STATUS_NEW,
        self::AVATAR_STATUS_WAITING_APPROVE,
        self::AVATAR_STATUS_ACTIVE,
        self::AVATAR_STATUS_BLOCKED,
    ];

    public const SOURCE_GOOGLE   = 'google';
    public const SOURCE_FACEBOOK = 'facebook';
    public const SOURCE_INSTAGRAM = 'instagram';
    public const SOURCE_TWITTER = 'twitter';
    public const SOURCE_REDDIT = 'reddit';
    public const SOURCE_YOUTUBE = 'youtube';
    public const SOURCE_TWITCH = 'twitch';
    public const SOURCE_TIKTOK = 'tiktok';
    public const SOURCE_X_COM = 'x.com';

    public const SOURCES = [
        self::SOURCE_GOOGLE,
        self::SOURCE_FACEBOOK,
        self::SOURCE_INSTAGRAM,
        self::SOURCE_TWITTER,
        self::SOURCE_REDDIT,
        self::SOURCE_YOUTUBE,
        self::SOURCE_TWITCH,
        self::SOURCE_TIKTOK,
        self::SOURCE_X_COM,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 1024, unique: true, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $fullName;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $avatar;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $source;

    #[ORM\Column(length: 16, nullable: true)]
    private string $avatarStatus;

    private ?string $imageFile = null;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $bioLink;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isVerified = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isApproved = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isBlocked = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isDeleted = false;

    #[ORM\Column(nullable: true)]
    private ?string $approveStatus;

    /** @var PersistentCollection<int, ResetPasswordRequest> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ResetPasswordRequest::class, cascade: ['remove'])]
    private PersistentCollection $resetPasswordRequests;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0])]
    private float $balance = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['default' => 0])]
    private float $balanceRu = 0;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserAvatar::class, cascade: ['persist', 'remove'])]
    private UserAvatar|null $avatarData = null;

    private ?string $photoProfileFile = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserBlock::class, cascade: ['persist', 'remove'])]
    private UserBlock|null $block = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserDecline::class, cascade: ['persist', 'remove'])]
    private UserDecline|null $decline = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    private ?string $newPassword = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $facebookId = null;

    private bool $needUpdateContentsStatuses = false;

    private bool $needDeactivateContents = false;


    #[ORM\Column(nullable: true)]
    private ?string $anonymUserId = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastActivityAt = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');

        $this->avatarStatus = self::AVATAR_STATUS_NEW;

        if (!$this->avatarData) {
            $this->avatarData = new UserAvatar();
            $this->avatarData->setUser($this);
        }


        if (!$this->block) {
            $this->block = new UserBlock();
            $this->block->setUser($this);
        }

        if (!$this->decline) {
            $this->decline = new UserDecline();
            $this->decline->setUser($this);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    #[ORM\Column(type: UlidType::NAME)]
    private Ulid $ulid;


    #[ORM\Column(nullable: true)]
    private ?array $data = [];

    public function getEmail(): string
    {
        return $this->email;
    }


    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }


    public function getData(): array
    {
        return $this->data ?? [];
    }

    public function setData(?array $data): self
    {
        $this->data = $data ?? [];

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setHashedPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string|null $newPassword
     */
    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUlid(): string
    {
        return $this->ulid->toBase32();
    }

    public function getUid(): string
    {
        return (string)$this->ulid;
    }

    public function generateUlid(): self
    {
        $this->ulid = (new UlidFactory())->create();

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getPrintName(): ?string
    {
        return $this->getFullName() ?: $this->getUsername();
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     */
    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getAvatarUrl(): ?string
    {
        if (!$this->avatar) {
            return null;
        }
        if (str_starts_with($this->avatar, 'http')) { // google avatar
            return $this->avatar;
        }
        return sprintf('%s/%s', DomainHelper::getCdnDomain(), $this->avatar);
    }

    public function getAvatarCropUrl(): ?string
    {
        return $this->getAvatarData()->getCropUrl();
    }

    public function getAvatarOriginalUrl(): ?string
    {
        return $this->getAvatarData()->getOriginalUrl();
    }


    public function imageProfileCover(): string
    {
        return sprintf('%s/profile-image-covers/%s.png', DomainHelper::getCdnDomain(), $this->id%5);
    }


    /**
     * @param string|null $avatar
     */
    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getBioLink(): ?string
    {
        return $this->bioLink;
    }

    public function setBioLink(?string $bioLink): static
    {
        $this->bioLink = $bioLink;

        return $this;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        if ($this->isApproved !== $isApproved) {
            $this->needUpdateContentsStatuses = true;
            if (!$isApproved) {
                $this->needDeactivateContents = true;
            }
        }
        $this->isApproved = $isApproved;

        return $this;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        if ($this->isBlocked !== $isBlocked) {
            $this->needUpdateContentsStatuses = true;
        }
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function __toString(): string
    {
        $name = $this->getUsername() ?? '';

        return $name;
    }

    public function getAvatarData(): ?UserAvatar
    {
        if (!$this->avatarData) {
            $this->avatarData = new UserAvatar();
            $this->avatarData->setUser($this);
        }

        return $this->avatarData;
    }

    public function setAvatarData(?UserAvatar $avatarData): void
    {
        $this->avatarData = $avatarData;
    }

    public function getAvatarStatus(): ?string
    {
        return $this->avatarStatus;
    }


    /**
     * @return \DateTimeImmutable|null
     */
    public function getLastActivityAt(): ?\DateTimeImmutable
    {
        return $this->lastActivityAt;
    }

    /**
     * @param \DateTimeImmutable|null $lastActivityAt
     */
    public function setLastActivityAt(?\DateTimeImmutable $lastActivityAt): void
    {
        $this->lastActivityAt = $lastActivityAt;
    }

    public function setAvatarStatus(string $status): self
    {
        if (!in_array($status, self::AVAILABLE_AVATAR_STATUSES)) {
            throw new NotAcceptableValueException();
        }

        $this->avatarStatus = $status;

        return $this;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getBalanceRu(): float
    {
        return $this->balanceRu;
    }

    /**
     * @param float $balanceRu
     */
    public function setBalanceRu(float $balanceRu): void
    {
        $this->balanceRu = $balanceRu;
    }

    public function addSum(float|int $sum): self
    {
        $this->balance += (float)$sum;

        return $this;
    }

    public function addSumRu(float|int $sumRu): self
    {
        $this->balanceRu += (float)$sumRu;

        return $this;
    }

    public function needUpdateContentsStatuses(): bool
    {
        return $this->needUpdateContentsStatuses;
    }

    public function needDeactivateContents(): bool
    {
        return $this->needDeactivateContents;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        if ($this->isDeleted !== $isDeleted) {
            $this->needUpdateContentsStatuses = true;
        }
        $this->isDeleted = $isDeleted;
    }

    public function getStatus(): string
    {
        if ($this->isDeleted) {
            return self::STATUS_DELETED;
        }

        if ($this->isBlocked) {
            return self::STATUS_BLOCKED;
        }

        return self::STATUS_ACTIVE;
    }

    public function getApproveStatus(): ?string
    {
        if (!$this->approveStatus) {
            return $this->isApproved() ? self::APPROVE_STATUS_APPROVED : self::APPROVE_STATUS_NEED_APPROVE;
        }

        return $this->approveStatus;
    }

    public function setApproveStatus(?string $approveStatus): self
    {
        $this->approveStatus = $approveStatus;
        $this->setIsApproved($approveStatus === User::APPROVE_STATUS_APPROVED);

        return $this;
    }

    public function getBlockReason(): ?UserBlock
    {
        if (!$this->block) {
            $this->block = new UserBlock();
            $this->block->setUser($this);
        }

        return $this->block;
    }

    public function getBlockReasonName(): string
    {
        $name = $this->block ? UserBlock::REASONS_NAMES[$this->block->getBlockReason()] : null;

        if (!$name) {
            $name = UserBlock::REASONS_DEFAULT_NAME;
        }

        return $name;
    }

    public function setBlockReason(?UserBlock $block): void
    {
        $this->block = $block;
    }

    public function getBlockReasonStr(): string
    {
        if (!$this->block) {
            $this->block = new UserBlock();
            $this->block->setUser($this);
        }

        return $this->block->getBlockReason() ?? '';
    }

    public function setBlockReasonStr(string $reason): void
    {
        $this->getBlockReason()->setBlockReason($reason);
    }

    public function getDeclineReason(): ?UserDecline
    {
        if (!$this->decline) {
            $this->decline = new UserDecline();
            $this->decline->setUser($this);
        }

        return $this->decline;
    }

    public function setDeclineReason(?UserDecline $decline): self
    {
        $this->decline = $decline;

        return $this;
    }

    public function getDeclineReasonStr(): string
    {
        if (!$this->decline) {
            $this->decline = new UserDecline();
            $this->decline->setUser($this);
        }

        if (!$this->decline->getDeclineReason()) {
            return '';
            //return $this->decline->getDeclineReason();
        }

        return UserDecline::REASONS_NAMES[$this->decline->getDeclineReason()] ?? '';
    }

    public function getDeclineDescriptionStr(): string
    {
        if (!$this->decline) {
            $this->decline = new UserDecline();
            $this->decline->setUser($this);
        }

        return $this->decline->getDeclineDescription() ?? '';
    }

    public function setDeclineReasonStr(?string $reason): self
    {
        $this->getDeclineReason()->setDeclineReason($reason);

        return $this;
    }

    public function setDeclineDescriptionStr(?string $declineDescription): self
    {
        $this->getDeclineReason()->setDeclineDescription($declineDescription);

        return $this;
    }

    public function getDeclineReasonName(): string
    {
        return $this->getDeclineReasonStr() ?? UserDecline::REASON_DEFAULT;
    }

    public function getAvatarDeclineReasonName(): string
    {
        return UserBlock::REASONS_NAMES[$this->getAvatarData()->getDeclineReason()] ?? UserBlock::REASONS_DEFAULT_NAME;
    }

    public function getAvatarDeclineReason(): ?string
    {
        return $this->getAvatarData()->getDeclineReason();
    }

    public function setAvatarDeclineReason(?string $reason): self
    {
        $this->getAvatarData()->setDeclineReason($reason);

        return $this;
    }

    public function getAvatarDeclineDescription(): ?string
    {
        return $this->getAvatarData()->getDeclineDescription();
    }

    public function setAvatarDeclineDescription(?string $reason): self
    {
        $this->getAvatarData()->setDeclineDescription($reason);

        return $this;
    }

    public function getUidStr(): string
    {
        return (string)$this->ulid;
    }

    public function getUserUrl(): string
    {
        return sprintf('%s/profile/%s', $_ENV['WEB_PROJECT_DOMAIN'], $this->getUidStr());
    }

    public function isUnsubscribed(): bool
    {
        return (bool)($this->getData()[self::CONFIG_NOTIFICATIONS_UNSUBSCRIBED] ?? false);
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }


    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedAtFormat(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->createdAt?->format($format);
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isFan(): bool
    {
        return !$this->isApproved();
    }

    public function isSupport(): bool
    {
        return $this->username === self::SUPPORT_USERNAME;
    }

    public function isModel(): bool
    {
        return $this->isApproved();
    }

    public function getPhotoProfileFile(): ?string
    {
        return $this->photoProfileFile;
    }

    public function setPhotoProfileFile(?string $photoProfileFile): void
    {
        $this->photoProfileFile = $photoProfileFile;
    }
}
