<?php
declare(strict_types=1);
namespace App\Domain\Book\Exception;

final class BookNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Aucun livre trouvé avec l\'ID : %s', $id));
    }
}
