<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

use Symfony\Component\Uid\Uuid;


final class BookId
{
    private string $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" n\'est pas un UUID valide pour BookId.', $value)
            );
        }
        $this->value = $value;
    }

    /**
     * Génère un nouvel identifiant unique.
     * Utilisé lors de la création d'un nouveau livre.
     */
    public static function generate(): self
    {
        return new self(Uuid::v4()->toRfc4122());
    }

    /**
     * Reconstruit depuis une valeur existante (venant de la BDD par exemple).
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string { return $this->value; }

    public function equals(self $other): bool { return $this->value === $other->value; }

    public function __toString(): string { return $this->value; }
}
