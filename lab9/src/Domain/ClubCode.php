<?php

declare(strict_types=1);

namespace App\Domain;

final readonly class ClubCode
{
    public function __construct(public string $value)
    {
        if (!preg_match('/^[A-Z]{3,5}-\d{2}$/', $value)) {
            throw new \InvalidArgumentException(
                'Формат кода клуба: три-пять заглавных латинских букв, дефис и две цифры (например, CHESS-01).'
            );
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
