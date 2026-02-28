<?php

declare(strict_types=1);

namespace App\Domain\Book\Entity;

use App\Domain\Book\Exception\BookAlreadyBorrowedException;
use App\Domain\Book\Exception\BookNotBorrowedException;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\ISBN;
use App\Domain\Book\ValueObject\Title;
use App\Domain\User\ValueObject\UserId;

/**
 * ENTITÉ DOMAIN — Book
 *
 * ✅ PHP pur : aucun import Symfony, Doctrine, ou framework quelconque.
 *
 * Toutes les RÈGLES MÉTIER vivent ici :
 * - "On ne peut emprunter qu'un livre disponible"
 * - "Un livre emprunté peut être retourné"
 * - "Un emprunt dure N jours"
 *
 * Cette classe ne sait pas qu'elle est persistée en MySQL.
 * Elle ne sait pas qu'elle est exposée via HTTP.
 * Elle fait UNE chose : représenter un livre avec ses règles.
 */
final class Book
{
    private BookId $id;
    private Title $title;
    private string $author;
    private ISBN $isbn;
    private string $description;
    private string $coverUrl;
    private int $totalCopies;
    private int $availableCopies;
    private ?UserId $borrowedByUserId;
    private ?\DateTimeImmutable $borrowedAt;
    private ?\DateTimeImmutable $dueDate;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        BookId $id,
        Title $title,
        string $author,
        ISBN $isbn,
        string $description = '',
        string $coverUrl = '',
        int $totalCopies = 1,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
        $this->description = $description;
        $this->coverUrl = $coverUrl;
        $this->totalCopies = $totalCopies;
        $this->availableCopies = $totalCopies;
        $this->borrowedByUserId = null;
        $this->borrowedAt = null;
        $this->dueDate = null;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // =========================================================
    // COMPORTEMENTS MÉTIER
    // La logique vit ici, PAS dans les Use Cases ou Controllers
    // =========================================================

    public function borrow(UserId $userId, int $durationDays = 14): void
    {
        if ($this->availableCopies <= 0) {
            throw new BookAlreadyBorrowedException(
                sprintf('Le livre "%s" n\'a plus d\'exemplaires disponibles.', $this->title->getValue())
            );
        }

        $this->borrowedByUserId = $userId;
        $this->borrowedAt = new \DateTimeImmutable();
        $this->dueDate = new \DateTimeImmutable("+{$durationDays} days");
        $this->availableCopies--;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function return(): void
    {
        if ($this->borrowedByUserId === null) {
            throw new BookNotBorrowedException(
                sprintf('Le livre "%s" n\'est pas actuellement emprunté.', $this->title->getValue())
            );
        }

        $this->borrowedByUserId = null;
        $this->borrowedAt = null;
        $this->dueDate = null;
        $this->availableCopies++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isOverdue(): bool
    {
        if ($this->dueDate === null) {
            return false;
        }
        return $this->dueDate < new \DateTimeImmutable();
    }

    public function isAvailable(): bool
    {
        return $this->availableCopies > 0;
    }

    // =========================================================
    // GETTERS (pas de setters publics = encapsulation forte)
    // =========================================================

    public function getId(): BookId { return $this->id; }
    public function getTitle(): Title { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getIsbn(): ISBN { return $this->isbn; }
    public function getDescription(): string { return $this->description; }
    public function getCoverUrl(): string { return $this->coverUrl; }
    public function getTotalCopies(): int { return $this->totalCopies; }
    public function getAvailableCopies(): int { return $this->availableCopies; }
    public function getBorrowedByUserId(): ?UserId { return $this->borrowedByUserId; }
    public function getBorrowedAt(): ?\DateTimeImmutable { return $this->borrowedAt; }
    public function getDueDate(): ?\DateTimeImmutable { return $this->dueDate; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    // Setters limités — utilisés uniquement par Doctrine pour reconstruire l'objet
    public function setAvailableCopies(int $count): void { $this->availableCopies = $count; }
    public function setBorrowedByUserId(?UserId $userId): void { $this->borrowedByUserId = $userId; }
    public function setBorrowedAt(?\DateTimeImmutable $date): void { $this->borrowedAt = $date; }
    public function setDueDate(?\DateTimeImmutable $date): void { $this->dueDate = $date; }
    public function setCreatedAt(\DateTimeImmutable $date): void { $this->createdAt = $date; }
    public function setUpdatedAt(\DateTimeImmutable $date): void { $this->updatedAt = $date; }
}
