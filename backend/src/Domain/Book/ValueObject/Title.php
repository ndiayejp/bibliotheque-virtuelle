<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

final class Title
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new \InvalidArgumentException('Le titre du livre ne peut pas être vide.');
        }

        if (strlen($trimmed) > 255) {
            throw new \InvalidArgumentException(
                sprintf('Le titre ne peut pas dépasser 255 caractères (actuel : %d).', strlen($trimmed))
            );
        }

        $this->value = $trimmed;
    }

    public function getValue(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
    public function equals(self $other): bool { return strtolower($this->value) === strtolower($other->value); }
}
