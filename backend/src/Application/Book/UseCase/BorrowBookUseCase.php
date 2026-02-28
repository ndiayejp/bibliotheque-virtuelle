<?php
declare(strict_types=1);
namespace App\Application\Book\UseCase;

use App\Application\Book\DTO\BookDTO;
use App\Domain\Book\Exception\BookNotFoundException;
use App\Domain\Book\Repository\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\User\ValueObject\UserId;

final class BorrowBookUseCase
{
    public function __construct(private readonly BookRepositoryInterface $bookRepository) {}

    public function execute(string $bookId, string $userId, int $durationDays = 14): BookDTO
    {
        $book = $this->bookRepository->findById(BookId::fromString($bookId));

        if ($book === null) {
            throw BookNotFoundException::withId($bookId);
        }

        // La règle métier "livre doit être disponible" est dans Book::borrow()
        // Le UseCase ne contient pas cette règle — il délègue à l'entité
        $book->borrow(UserId::fromString($userId), $durationDays);

        $this->bookRepository->save($book);

        return BookDTO::fromEntity($book);
    }
}
