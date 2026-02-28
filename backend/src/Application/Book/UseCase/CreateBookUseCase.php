<?php
declare(strict_types=1);
namespace App\Application\Book\UseCase;

use App\Application\Book\DTO\BookDTO;
use App\Application\Book\DTO\CreateBookDTO;
use App\Domain\Book\Entity\Book;
use App\Domain\Book\Repository\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookId;
use App\Domain\Book\ValueObject\ISBN;
use App\Domain\Book\ValueObject\Title;

final class CreateBookUseCase
{
    public function __construct(private readonly BookRepositoryInterface $bookRepository) {}

    public function execute(CreateBookDTO $dto): BookDTO
    {
        $isbn = new ISBN($dto->isbn);

        if ($this->bookRepository->findByIsbn($isbn) !== null) {
            throw new \DomainException(sprintf('Un livre avec l\'ISBN %s existe déjà.', $dto->isbn));
        }

        $book = new Book(
            id: BookId::generate(),
            title: new Title($dto->title),
            author: $dto->author,
            isbn: $isbn,
            description: $dto->description,
            coverUrl: $dto->coverUrl,
            totalCopies: $dto->totalCopies,
        );

        $this->bookRepository->save($book);

        return BookDTO::fromEntity($book);
    }
}
