<?php
declare(strict_types=1);
namespace App\Application\Book\DTO;

final class CreateBookDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $author,
        public readonly string $isbn,
        public readonly string $description = '',
        public readonly string $coverUrl = '',
        public readonly int $totalCopies = 1,
    ) {}
}
