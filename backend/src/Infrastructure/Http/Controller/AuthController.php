<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\User\DTO\RegisterUserDTO;
use App\Application\User\UseCase\RegisterUserUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', name: 'auth_')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly RegisterUserUseCase $registerUserUseCase,
    ) {}

    /**
     * POST /api/auth/register
     * La route /api/auth/login est gérée automatiquement par LexikJWTAuthenticationBundle
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = $request->toArray();

        foreach (['email', 'firstName', 'lastName', 'password'] as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => sprintf('Le champ "%s" est requis.', $field)], 422);
            }
        }

        if (strlen($data['password']) < 8) {
            return $this->json(['error' => 'Le mot de passe doit contenir au moins 8 caractères.'], 422);
        }

        try {
            $dto = new RegisterUserDTO(
                email: $data['email'],
                firstName: $data['firstName'],
                lastName: $data['lastName'],
                plainPassword: $data['password'],
            );

            $userDTO = $this->registerUserUseCase->execute($dto);

            return $this->json(['message' => 'Inscription réussie.', 'data' => $userDTO->toArray()], 201);

        } catch (\DomainException|\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/auth/me — Infos de l'utilisateur connecté
     */
    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Non authentifié.'], 401);
        }

        return $this->json([
            'data' => [
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }
}
