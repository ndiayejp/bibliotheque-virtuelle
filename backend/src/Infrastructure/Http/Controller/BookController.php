<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\Book\DTO\CreateBookDTO;
use App\Application\Book\UseCase\BorrowBookUseCase;
use App\Application\Book\UseCase\CreateBookUseCase;
use App\Application\Book\UseCase\GetAllBooksUseCase;
use App\Application\Book\UseCase\GetBookByIdUseCase;
use App\Application\Book\UseCase\ReturnBookUseCase;
use App\Domain\Book\Exception\BookAlreadyBorrowedException;
use App\Domain\Book\Exception\BookNotFoundException;
use App\Domain\Book\Exception\BookNotBorrowedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/api/books', name: 'book_')]
final class BookController extends AbstractController
{
    public function __construct(
        private readonly GetAllBooksUseCase  $getAllBooksUseCase,
        private readonly GetBookByIdUseCase  $getBookByIdUseCase,
        private readonly CreateBookUseCase   $createBookUseCase,
        private readonly BorrowBookUseCase   $borrowBookUseCase,
        private readonly ReturnBookUseCase   $returnBookUseCase,
    ) {}

    // GET /api/books
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page   = max(1, (int) $request->query->get('page', 1));
        $limit  = min(50, max(1, (int) $request->query->get('limit', 20)));
        $filter = $request->query->get('filter', 'all');

        $result = $this->getAllBooksUseCase->execute($page, $limit, $filter);

        return $this->json([
            'data' => array_map(fn($dto) => $dto->toArray(), $result['books']),
            'meta' => [
                'total' => $result['total'],
                'page'  => $result['page'],
                'limit' => $result['limit'],
                'pages' => (int) ceil($result['total'] / $result['limit']),
            ],
        ]);
    }

    // GET /api/books/{id}
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        try {
            return $this->json(['data' => $this->getBookByIdUseCase->execute($id)->toArray()]);
        } catch (BookNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => 'ID invalide.'], Response::HTTP_BAD_REQUEST);
        }
    }

    // POST /api/books
    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();

        foreach (['title', 'author', 'isbn'] as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => sprintf('Le champ "%s" est requis.', $field)], 422);
            }
        }

        try {
            $dto = new CreateBookDTO(
                title: $data['title'],
                author: $data['author'],
                isbn: $data['isbn'],
                description: $data['description'] ?? '',
                coverUrl: $data['coverUrl'] ?? '',
                totalCopies: (int) ($data['totalCopies'] ?? 1),
            );

            return $this->json(['data' => $this->createBookUseCase->execute($dto)->toArray()], 201);

        } catch (\DomainException|\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 422);
        }
    }

    // POST /api/books/{id}/borrow
    #[Route('/{id}/borrow', name: 'borrow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function borrow(string $id): JsonResponse
    {
        try {
           /** @var SymfonyUserAdapter $symfonyUser */
            $symfonyUser = $this->getUser();
            $userId = $symfonyUser->getId(); // ← UUID, pas l'email
            $dto    = $this->borrowBookUseCase->execute($id, $userId);

            return $this->json(['data' => $dto->toArray(), 'message' => 'Livre emprunté avec succès.']);

        } catch (BookNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (BookAlreadyBorrowedException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }
    }

    // POST /api/books/{id}/return
    #[Route('/{id}/return', name: 'return', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function return(string $id): JsonResponse
    {
        try {
            $dto = $this->returnBookUseCase->execute($id);

            return $this->json(['data' => $dto->toArray(), 'message' => 'Livre retourné avec succès.']);

        } catch (BookNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (BookNotBorrowedException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }
    }
}
