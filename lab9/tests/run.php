<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Domain\{Club, ClubCode, Curator, Student, User};

$passed = 0;
$failed = 0;

function check(string $name, bool $condition): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "OK:   $name\n";
    } else {
        $failed++;
        echo "FAIL: $name\n";
    }
}

/** Проверяет, что действие выбрасывает исключение ожидаемого класса. */
function expectException(string $class, callable $action, string $name): void
{
    try {
        $action();
        check($name . " (исключение не выброшено)", false);
    } catch (Throwable $e) {
        check($name . " — " . $e::class . ': ' . $e->getMessage(), $e instanceof $class);
    }
}

function makeCurator(): Curator
{
    return new Curator(1, 'Анна Ким', 'a.kim@atu.edu.kz', 'ИТ');
}

function makeStudent(int $id, string $name = 'Алия Нур'): Student
{
    return new Student($id, $name, "s{$id}@example.kz", 'ИС');
}

function makeClub(int $capacity): Club
{
    return new Club(new ClubCode('CHESS-01'), 'Шахматный клуб', makeCurator(), $capacity);
}

echo "== Сценарии проверки ==\n";

// 1. Корректное создание
$student = makeStudent(10);
$curator = makeCurator();
check('1. Корректные Student и Curator созданы', $student->id === 10 && $curator->department === 'ИТ');

// 2. Пустое имя
expectException(InvalidArgumentException::class, fn() => new Student(2, '  ', 'x@example.kz', 'ИС'), '2. Пустое имя');

// 3. Неверный email
expectException(InvalidArgumentException::class, fn() => new Student(3, 'Алия Нур', 'bad-email', 'ИС'), '3. Неверный email');

// 4. Лимит клуба = 0
expectException(InvalidArgumentException::class, fn() => makeClub(0), '4. Клуб с лимитом 0');

// 5. Вступление студента
$club = makeClub(3);
$m = $club->join(makeStudent(10));
check('5. Вступление: членство создано, свободных мест 2', $m->studentId === 10 && $club->freePlaces() === 2);

// 6. Повторное вступление (лимит 3, поэтому срабатывает именно проверка дубля)
expectException(DomainException::class, fn() => $club->join(makeStudent(10)), '6. Повторное вступление');

// 7. Сверх лимита (граничный случай: последнее место занято)
$small = makeClub(1);
$small->join(makeStudent(10));
check('7a. Граница: последнее место занято, клуб заполнен', $small->isFull() && $small->freePlaces() === 0);
expectException(DomainException::class, fn() => $small->join(makeStudent(11)), '7b. Вступление сверх лимита');

// 8. Полиморфизм: один контракт, разные результаты
$labels = array_map(fn(User $u): string => $u->roleLabel(), [makeCurator(), makeStudent(5)]);
check('8. Полиморфизм roleLabel(): ' . implode(' | ', $labels), $labels[0] !== $labels[1] && str_starts_with($labels[0], 'Куратор') && str_starts_with($labels[1], 'Студент'));

// 9. readonly ID
expectException(Error::class, function () use ($student) {
    /** @phpstan-ignore-next-line */
    $student->id = 99;
}, '9. Изменение readonly ID');

// 10. Выход из клуба освобождает место, повторный выход запрещён
$club2 = makeClub(2);
$s = makeStudent(20);
$club2->join($s);
$club2->leave($s);
check('10a. Выход из клуба освобождает место', $club2->freePlaces() === 2 && !$club2->hasMember($s));
expectException(DomainException::class, fn() => $club2->leave($s), '10b. Выход не состоящего студента');

// 11. Лимит нельзя уменьшить ниже числа участников
$club3 = makeClub(3);
$club3->join(makeStudent(30));
$club3->join(makeStudent(31));
expectException(DomainException::class, fn() => $club3->changeCapacity(1), '11a. Лимит меньше числа участников');
$club3->changeCapacity(2);
check('11b. Граница: лимит равен числу участников', $club3->capacity() === 2 && $club3->isFull());

// 12. Неверный код клуба
expectException(InvalidArgumentException::class, fn() => new ClubCode('chess1'), '12. Неверный формат кода клуба');

// 13. Внешний код не может изменить внутреннюю коллекцию
$club4 = makeClub(2);
$club4->join(makeStudent(40));
$copy = $club4->memberships();
$copy[] = $copy[0];
check('13. Изменение копии массива не влияет на клуб', count($club4->memberships()) === 1);

// 14. Состояние не нарушается после исключения
$club5 = makeClub(1);
$club5->join(makeStudent(50));
try {
    $club5->join(makeStudent(51));
} catch (DomainException) {
}
check('14. После отказа состояние клуба корректно', count($club5->memberships()) === 1);

echo "\nИтого: пройдено $passed, провалено $failed\n";
exit($failed === 0 ? 0 : 1);
