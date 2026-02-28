<?php
declare(strict_types=1);
namespace App\Application\User\DTO;
use App\Domain\User\Entity\User;

final class UserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $fullName,
        public readonly string $role,
        public readonly bool $isActive,
        public readonly string $createdAt,
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId()->getValue(),
            email: $user->getEmail()->getValue(),
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            fullName: $user->getFullName(),
            role: $user->getRole(),
            isActive: $user->isActive(),
            createdAt: $user->getCreatedAt()->format(\DateTimeInterface::RFC3339),
        );
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'firstName' => $this->firstName,
            'lastName'  => $this->lastName,
            'fullName'  => $this->fullName,
            'role'      => $this->role,
            'isActive'  => $this->isActive,
            'createdAt' => $this->createdAt,
        ];
    }
}
