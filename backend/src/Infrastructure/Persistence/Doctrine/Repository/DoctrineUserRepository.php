<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use Doctrine\DBAL\Connection;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    // =========================================================
    // ÉCRITURE
    // =========================================================

    public function save(User $user): void
    {
        $exists = $this->connection->fetchOne(
            'SELECT id FROM users WHERE id = ?',
            [$user->getId()->getValue()]
        );

        $data = [
            'email'         => $user->getEmail()->getValue(),
            'first_name'    => $user->getFirstName(),
            'last_name'     => $user->getLastName(),
            'password_hash' => $user->getPasswordHash(),
            'role'          => $user->getRole(),
            'is_active'     => $user->isActive() ? 1 : 0,
        ];

        if ($exists) {
            $this->connection->update('users', $data, ['id' => $user->getId()->getValue()]);
        } else {
            $data['id']         = $user->getId()->getValue();
            $data['created_at'] = $user->getCreatedAt()->format('Y-m-d H:i:s');
            $this->connection->insert('users', $data);
        }
    }

    public function delete(UserId $id): void
    {
        $this->connection->delete('users', ['id' => $id->getValue()]);
    }

    // =========================================================
    // LECTURE
    // =========================================================

    public function findById(UserId $id): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = ?',
            [$id->getValue()]
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE email = ?',
            [$email->getValue()]
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
       $rows = $this->connection->fetchAllAssociative(
        sprintf('SELECT * FROM users ORDER BY created_at DESC LIMIT %d OFFSET %d', $limit, $offset)
       );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function emailExists(Email $email): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT COUNT(id) FROM users WHERE email = ?',
            [$email->getValue()]
        );
    }

    // =========================================================
    // HYDRATATION — string BDD → Value Objects
    // =========================================================

    private function hydrate(array $row): User
    {
        $ref  = new \ReflectionClass(User::class);
        $user = $ref->newInstanceWithoutConstructor();

        $set = static function (string $prop, mixed $value) use ($ref, $user): void {
            $p = $ref->getProperty($prop);
            $p->setAccessible(true);
            $p->setValue($user, $value);
        };

        $set('id',           UserId::fromString($row['id']));
        $set('email',        new Email($row['email']));
        $set('firstName',    $row['first_name']);
        $set('lastName',     $row['last_name']);
        $set('passwordHash', $row['password_hash']);
        $set('role',         $row['role']);
        $set('isActive',     (bool) $row['is_active']);
        $set('createdAt',    new \DateTimeImmutable($row['created_at']));

        return $user;
    }
}
