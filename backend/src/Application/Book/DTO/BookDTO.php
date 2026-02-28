<?php

declare(strict_types=1);

namespace App\Application\Book\DTO;

use App\Domain\Book\Entity\Book;

/**
 * DTO — BookDTO (Data Transfer Object)
 *
 * Traduit une entité Domain riche → tableau simple pour l'API JSON.
 *
 * Pourquoi ne pas exposer l'entité directement ?
 * 1. Éviter la sérialisation de Value Objects complexes
 * 2. Découpler la structure interne de la représentation publique
 * 3. Contrôler précisément ce qui est exposé
 * 4. Éviter les problèmes de lazy-loading Doctrine
 */
final class BookDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $author,
        public readonly string $isbn,
        public readonly string $description,
        public readonly string $coverUrl,
        public readonly int $totalCopies,
        public readonly int $availableCopies,
        public readonly bool $isAvailable,
        public readonly ?string $borrowedByUserId,
        public readonly ?string $borrowedAt,
        public readonly ?string $dueDate,
        public readonly bool $isOverdue,
        public readonly string $createdAt,
    ) {}

    public static function fromEntity(Book $book): self
    {
        return new self(
            id: $book->getId()->getValue(),
            title: $book->getTitle()->getValue(),
            author: $book->getAuthor(),
            isbn: $book->getIsbn()->getValue(),
            description: $book->getDescription(),
            coverUrl: $book->getCoverUrl(),
            totalCopies: $book->getTotalCopies(),
            availableCopies: $book->getAvailableCopies(),
            isAvailable: $book->isAvailable(),
            borrowedByUserId: $book->getBorrowedByUserId()?->getValue(),
            borrowedAt: $book->getBorrowedAt()?->format(\DateTimeInterface::RFC3339),
            dueDate: $book->getDueDate()?->format(\DateTimeInterface::RFC3339),
            isOverdue: $book->isOverdue(),
            createdAt: $book->getCreatedAt()->format(\DateTimeInterface::RFC3339),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'author'            => $this->author,
            'isbn'              => $this->isbn,
            'description'       => $this->description,
            'coverUrl'          => $this->coverUrl,
            'totalCopies'       => $this->totalCopies,
            'availableCopies'   => $this->availableCopies,
            'isAvailable'       => $this->isAvailable,
            'borrowedByUserId'  => $this->borrowedByUserId,
            'borrowedAt'        => $this->borrowedAt,
            'dueDate'           => $this->dueDate,
            'isOverdue'         => $this->isOverdue,
            'createdAt'         => $this->createdAt,
        ];
    }
}
