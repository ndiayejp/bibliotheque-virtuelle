<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

/**
 * VALUE OBJECT — ISBN
 *
 * Encapsule la règle métier "un ISBN doit être valide".
 * Cette règle est définie UNE SEULE FOIS ici.
 * Impossible de créer un livre avec un ISBN invalide.
 */
final class ISBN
{
    private string $value;

    public function __construct(string $value)
    {
        $cleaned = preg_replace('/[-\s]/', '', $value);

        if (!$this->isValidIsbn13($cleaned) && !$this->isValidIsbn10($cleaned)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" n\'est pas un ISBN valide (ISBN-10 ou ISBN-13 attendu).', $value)
            );
        }

        $this->value = $cleaned;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (strlen($isbn) !== 13 || !ctype_digit($isbn)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        return (10 - ($sum % 10)) % 10 === (int) $isbn[12];
    }

    private function isValidIsbn10(string $isbn): bool
    {
        if (strlen($isbn) !== 10) return false;
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            if (!ctype_digit($isbn[$i])) return false;
            $sum += (int) $isbn[$i] * (10 - $i);
        }
        $last = strtoupper($isbn[9]);
        $sum += $last === 'X' ? 10 : (ctype_digit($last) ? (int) $last : -1);
        return $sum % 11 === 0;
    }

    public function getValue(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }
}
