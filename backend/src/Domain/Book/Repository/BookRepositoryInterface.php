<?php

declare(strict_types=1);

namespace App\Domain\Book\Repository;

use App\Domain\Book\Entity\Book;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\ISBN;

/**
 * PORT — BookRepositoryInterface
 *
 * ⭐ Concept central de l'architecture hexagonale.
 *
 * Cette INTERFACE est dans le Domain.
 * Elle dit CE QU'ON A BESOIN de faire (trouver, sauvegarder...).
 * Elle ne dit PAS COMMENT le faire (SQL ? Mongo ? Cache ?).
 *
 * Le Domain dépend de cette interface → dépendance vers l'intérieur ✅
 * DoctrineBookRepository IMPLÉMENTE cette interface → adapter sortant ✅
 *
 * Pour les tests unitaires : InMemoryBookRepository implements BookRepositoryInterface
 * Pour la prod : DoctrineBookRepository implements BookRepositoryInterface
 * Pour changer de BDD : créer MongoBookRepository, modifier services.yaml → DONE
 */
interface BookRepositoryInterface
{
    public function save(Book $book): void;

    public function findById(BookId $id): ?Book;

    public function findByIsbn(ISBN $isbn): ?Book;

    /** @return Book[] */
    public function findAll(int $page = 1, int $limit = 20): array;

    /** @return Book[] */
    public function search(string $query, int $page = 1, int $limit = 20): array;

    /** @return Book[] */
    public function findAvailable(int $page = 1, int $limit = 20): array;

    /** @return Book[] */
    public function findBorrowedByUser(string $userId): array;

    public function countAll(): int;

    public function delete(BookId $id): void;
}
