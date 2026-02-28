<?php

declare(strict_types=1);

namespace App\Application\Book\UseCase;

use App\Application\Book\DTO\BookDTO;
use App\Domain\Book\Repository\BookRepositoryInterface;

/**
 * USE CASE — GetAllBooksUseCase
 *
 * Un Use Case = une action métier unique.
 * Il orchestre : récupérer → transformer → retourner.
 * Il ne contient AUCUNE logique métier propre.
 */
final class GetAllBooksUseCase
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function execute(int $page = 1, int $limit = 20, string $filter = 'all'): array
    {
        $books = match ($filter) {
            'available' => $this->bookRepository->findAvailable($page, $limit),
            default     => $this->bookRepository->findAll($page, $limit),
        };

        return [
            'books' => array_map(fn($b) => BookDTO::fromEntity($b), $books),
            'total' => $this->bookRepository->countAll(),
            'page'  => $page,
            'limit' => $limit,
        ];
    }
}
