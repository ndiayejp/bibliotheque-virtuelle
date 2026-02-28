<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * ADAPTER — JWTUserProvider
 *
 * Appelé par LexikJWTAuthenticationBundle à chaque requête authentifiée.
 * Charge l'utilisateur depuis la BDD en utilisant notre UserRepositoryInterface.
 */
final class JWTUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $email = new Email($identifier);
        } catch (\InvalidArgumentException) {
            throw new UserNotFoundException(sprintf('Email invalide : %s', $identifier));
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new UserNotFoundException(sprintf('Utilisateur "%s" introuvable.', $identifier));
        }

        return new SymfonyUserAdapter(
            email: $user->getEmail()->getValue(),
            roles: [$user->getRole()],
            password: $user->getPasswordHash(),
            id: $user->getId()->getValue(),
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === SymfonyUserAdapter::class;
    }
}
