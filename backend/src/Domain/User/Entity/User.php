<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;

/**
 * ENTITÉ DOMAIN — User
 * PHP pur, aucune dépendance Symfony Security ou Doctrine.
 */
final class User
{
    public const ROLE_USER       = 'ROLE_USER';
    public const ROLE_LIBRARIAN  = 'ROLE_LIBRARIAN';
    public const ROLE_ADMIN      = 'ROLE_ADMIN';

    private UserId $id;
    private Email $email;
    private string $firstName;
    private string $lastName;
    private string $passwordHash;
    private string $role;
    private bool $isActive;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        UserId $id,
        Email $email,
        string $firstName,
        string $lastName,
        string $passwordHash,
        string $role = self::ROLE_USER,
    ) {
        $this->id           = $id;
        $this->email        = $email;
        $this->firstName    = trim($firstName);
        $this->lastName     = trim($lastName);
        $this->passwordHash = $passwordHash;
        $this->role         = $role;
        $this->isActive     = true;
        $this->createdAt    = new \DateTimeImmutable();
    }

    // Comportements métier
    public function getFullName(): string { return $this->firstName . ' ' . $this->lastName; }
    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }
    public function isLibrarian(): bool { return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_LIBRARIAN], true); }
    public function deactivate(): void { $this->isActive = false; }
    public function changePassword(string $hash): void { $this->passwordHash = $hash; }

    // Getters
    public function getId(): UserId { return $this->id; }
    public function getEmail(): Email { return $this->email; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getRole(): string { return $this->role; }
    public function isActive(): bool { return $this->isActive; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    // Pour Doctrine
    public function setCreatedAt(\DateTimeImmutable $d): void { $this->createdAt = $d; }
    public function setIsActive(bool $v): void { $this->isActive = $v; }
}
