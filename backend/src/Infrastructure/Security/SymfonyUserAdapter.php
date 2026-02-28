<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * ADAPTER — SymfonyUserAdapter
 *
 * Notre User Domain ne peut pas implémenter UserInterface de Symfony
 * car ce serait une dépendance Domain → Infrastructure.
 *
 * Ce petit adaptateur fait le pont :
 * - Il implémente les interfaces Symfony Security
 * - Il est construit depuis les données du User Domain
 * - Le Domain reste pur
 */
final class SymfonyUserAdapter implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private readonly string $email,
        private readonly array $roles,
        private readonly string $password,
        private readonly string $id = '',
    ) {}

    public function getUserIdentifier(): string { return $this->email; }

    public function getRoles(): array { return array_unique([...$this->roles, 'ROLE_USER']); }

    public function getPassword(): string { return $this->password; }

    public function eraseCredentials(): void {}

    public function getId(): string { return $this->id; }
}
