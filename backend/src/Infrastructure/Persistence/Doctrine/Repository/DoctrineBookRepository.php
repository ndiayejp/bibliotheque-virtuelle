<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Book\Entity\Book;
use App\Domain\Book\Repository\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\ISBN;
use App\Domain\Book\ValueObject\Title;
use App\Domain\User\ValueObject\UserId;
use Doctrine\DBAL\Connection;

/**
 * ADAPTER — DoctrineBookRepository (SQL direct + ReflectionClass)
 *
 * Pas de Custom Types Doctrine.
 * Toutes les conversions string ↔ Value Object sont faites ici.
 */
final class DoctrineBookRepository implements BookRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    // =========================================================
    // ÉCRITURE
    // =========================================================

    public function save(Book $book): void
    {
        $data = [
            'title'               => $book->getTitle()->getValue(),
            'author'              => $book->getAuthor(),
            'isbn'                => $book->getIsbn()->getValue(),
            'description'         => $book->getDescription(),
            'cover_url'           => $book->getCoverUrl(),
            'total_copies'        => $book->getTotalCopies(),
            'available_copies'    => $book->getAvailableCopies(),
            'borrowed_by_user_id' => $book->getBorrowedByUserId()?->getValue(),
            'borrowed_at'         => $book->getBorrowedAt()?->format('Y-m-d H:i:s'),
            'due_date'            => $book->getDueDate()?->format('Y-m-d H:i:s'),
            'updated_at'          => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $exists = $this->connection->fetchOne(
            'SELECT id FROM books WHERE id = ?',
            [$book->getId()->getValue()]
        );

        if ($exists) {
            $this->connection->update('books', $data, ['id' => $book->getId()->getValue()]);
        } else {
            $data['id']         = $book->getId()->getValue();
            $data['created_at'] = $book->getCreatedAt()->format('Y-m-d H:i:s');
            $this->connection->insert('books', $data);
        }
    }

    public function delete(BookId $id): void
    {
        $this->connection->delete('books', ['id' => $id->getValue()]);
    }

    // =========================================================
    // LECTURE
    // =========================================================

    public function findById(BookId $id): ?Book
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM books WHERE id = ?',
            [$id->getValue()]
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByIsbn(ISBN $isbn): ?Book
    {
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM books WHERE isbn = ?',
            [$isbn->getValue()]
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
       $rows = $this->connection->fetchAllAssociative(
        sprintf('SELECT * FROM books ORDER BY created_at DESC LIMIT %d OFFSET %d', $limit, $offset)
    );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function search(string $query, int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
       $rows = $this->connection->fetchAllAssociative(
        sprintf('SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY title ASC LIMIT %d OFFSET %d', $limit, $offset),
        ["%$query%", "%$query%"]
    );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findAvailable(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;
       $rows = $this->connection->fetchAllAssociative(
        sprintf('SELECT * FROM books WHERE available_copies > 0 ORDER BY created_at DESC LIMIT %d OFFSET %d', $limit, $offset)
    );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findBorrowedByUser(string $userId): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT * FROM books WHERE borrowed_by_user_id = ?',
            [$userId]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function countAll(): int
    {
        return (int) $this->connection->fetchOne('SELECT COUNT(id) FROM books');
    }

    // =========================================================
    // HYDRATATION — string BDD → Value Objects
    // =========================================================

    private function hydrate(array $row): Book
    {
        $ref  = new \ReflectionClass(Book::class);
        $book = $ref->newInstanceWithoutConstructor();

        $set = static function (string $prop, mixed $value) use ($ref, $book): void {
            $p = $ref->getProperty($prop);
            $p->setAccessible(true);
            $p->setValue($book, $value);
        };

        $set('id',              BookId::fromString($row['id']));
        $set('title',           new Title($row['title']));
        $set('author',          $row['author']);
        $set('isbn',            new ISBN($row['isbn']));
        $set('description',     $row['description'] ?? '');
        $set('coverUrl',        $row['cover_url'] ?? '');
        $set('totalCopies',     (int) $row['total_copies']);
        $set('availableCopies', (int) $row['available_copies']);
        $set('borrowedByUserId',
            $row['borrowed_by_user_id']
                ? UserId::fromString($row['borrowed_by_user_id'])
                : null
        );
        $set('borrowedAt',
            $row['borrowed_at']
                ? new \DateTimeImmutable($row['borrowed_at'])
                : null
        );
        $set('dueDate',
            $row['due_date']
                ? new \DateTimeImmutable($row['due_date'])
                : null
        );
        $set('createdAt', new \DateTimeImmutable($row['created_at']));
        $set('updatedAt', new \DateTimeImmutable($row['updated_at']));

        return $book;
    }
}
