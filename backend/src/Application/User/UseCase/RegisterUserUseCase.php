<?php
declare(strict_types=1);
namespace App\Application\User\UseCase;

use App\Application\User\DTO\RegisterUserDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Security\SymfonyUserAdapter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function execute(RegisterUserDTO $dto): UserDTO
    {
        $email = new Email($dto->email);

        if ($this->userRepository->emailExists($email)) {
            throw new \DomainException(sprintf('L\'email "%s" est déjà utilisé.', $dto->email));
        }

        // Proxy minimal pour hasher le mot de passe avec Symfony
        $proxy = new SymfonyUserAdapter($email->getValue(), [User::ROLE_USER], '');
        $hashedPassword = $this->passwordHasher->hashPassword($proxy, $dto->plainPassword);

        $user = new User(
            id: UserId::generate(),
            email: $email,
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            passwordHash: $hashedPassword,
        );

        $this->userRepository->save($user);

        return UserDTO::fromEntity($user);
    }
}
