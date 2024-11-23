<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Utility\FormatHelper;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Uid\Ulid;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, User::class);
    }

    public function cacheClear(): void
    {
        $this->_em->clear();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setHashedPassword($newHashedPassword);
        $this->save($user);
    }

    public function save(User $user): User
    {
        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    public function getUserById(int $userId): ?User
    {
        return $this->findOneBy(['id' => $userId]);
    }

    public function getUserByUid(?string $uid): ?User
    {
        if (!$uid) {
            return null;
        }

        if (!FormatHelper::isValidUid($uid)) {
            return null;
        }

        return $this->findOneBy(['ulid' => new Ulid($uid)]);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function getUsernamesByIds(array $userIds): array
    {
        $sql = "SELECT username, id FROM users WHERE id IN (?)";
        $em = $this->getEntityManager();
        $con = $em->getConnection();
        $resultSet = $con->executeQuery(
            $sql,
            [$userIds],
            [Connection::PARAM_INT_ARRAY],
        );

        return $resultSet->fetchAllKeyValue();
    }

    public function getPayingUsers(): array
    {
        return $this->findBy([
            'isPaying' => true,
        ]);
    }
}
