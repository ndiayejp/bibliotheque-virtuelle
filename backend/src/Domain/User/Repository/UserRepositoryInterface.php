<?php
declare(strict_types=1);
namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(UserId $id): ?User;
    public function findByEmail(Email $email): ?User;
    /** @return User[] */
    public function findAll(int $page = 1, int $limit = 20): array;
    public function delete(UserId $id): void;
    public function emailExists(Email $email): bool;
}
