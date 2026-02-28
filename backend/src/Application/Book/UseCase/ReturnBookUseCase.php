<?php
declare(strict_types=1);
namespace App\Application\Book\UseCase;

use App\Application\Book\DTO\BookDTO;
use App\Domain\Book\Exception\BookNotFoundException;
use App\Domain\Book\Repository\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookId;

final class ReturnBookUseCase
{
    public function __construct(private readonly BookRepositoryInterface $bookRepository) {}

    public function execute(string $bookId): BookDTO
    {
        $book = $this->bookRepository->findById(BookId::fromString($bookId));

        if ($book === null) {
            throw BookNotFoundException::withId($bookId);
        }

        $book->return();
        $this->bookRepository->save($book);

        return BookDTO::fromEntity($book);
    }
}
