<?php

declare(strict_types=1);

namespace App\Domain;

final class Club
{
    /** @var list<Membership> */
    private array $memberships = [];

    public function __construct(
        public readonly ClubCode $code,
        public readonly string $title,
        public readonly Curator $curator,
        private int $capacity,
    ) {
        if (trim($title) === '') {
            throw new \InvalidArgumentException('Название клуба обязательно.');
        }
        if ($capacity < 1) {
            throw new \InvalidArgumentException('Лимит участников должен быть положительным.');
        }
    }

    public function join(Student $student): Membership
    {
        if ($this->isFull()) {
            throw new \DomainException('В клубе нет свободных мест.');
        }
        if ($this->hasMember($student)) {
            throw new \DomainException('Студент уже состоит в этом клубе.');
        }

        $membership = new Membership($student->id, $this->code, new \DateTimeImmutable());
        $this->memberships[] = $membership;
        return $membership;
    }

    public function leave(Student $student): void
    {
        foreach ($this->memberships as $index => $item) {
            if ($item->studentId === $student->id) {
                unset($this->memberships[$index]);
                $this->memberships = array_values($this->memberships);
                return;
            }
        }
        throw new \DomainException('Студент не состоит в этом клубе.');
    }

    public function changeCapacity(int $newCapacity): void
    {
        if ($newCapacity < 1) {
            throw new \InvalidArgumentException('Лимит участников должен быть положительным.');
        }
        if ($newCapacity < count($this->memberships)) {
            throw new \DomainException('Нельзя установить лимит меньше текущего числа участников.');
        }
        $this->capacity = $newCapacity;
    }

    public function hasMember(Student $student): bool
    {
        foreach ($this->memberships as $item) {
            if ($item->studentId === $student->id) {
                return true;
            }
        }
        return false;
    }

    public function isFull(): bool
    {
        return count($this->memberships) >= $this->capacity;
    }

    public function capacity(): int
    {
        return $this->capacity;
    }

    public function freePlaces(): int
    {
        return $this->capacity - count($this->memberships);
    }

    /** @return list<Membership> */
    public function memberships(): array
    {
        return $this->memberships;
    }
}
