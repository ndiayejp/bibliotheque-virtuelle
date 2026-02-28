<?php
declare(strict_types=1);
namespace App\Domain\User\ValueObject;
use Symfony\Component\Uid\Uuid;

final class UserId
{
    private string $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(sprintf('"%s" n\'est pas un UUID valide pour UserId.', $value));
        }
        $this->value = $value;
    }

    public static function generate(): self { return new self(Uuid::v4()->toRfc4122()); }
    public static function fromString(string $v): self { return new self($v); }
    public function getValue(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }
    public function __toString(): string { return $this->value; }
}
