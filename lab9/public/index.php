<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Domain\{Club, ClubCode, Curator, Student, User};

function printUserCard(User $user): void
{
    printf(
        "#%d %s — %s <%s>\n",
        $user->id,
        $user->fullName(),
        $user->roleLabel(),
        $user->email,
    );
}

$curator = new Curator(1, 'Анна Ким', 'a.kim@atu.edu.kz', 'ИТ');
$aliya = new Student(10, 'Алия Нур', 'aliya@example.kz', 'ИС');
$daniyar = new Student(11, 'Данияр Серик', 'daniyar@example.kz', 'ИС');
$club = new Club(new ClubCode('CHESS-01'), 'Шахматный клуб', $curator, 3);

echo "== Карточки пользователей ==\n";
foreach ([$curator, $aliya, $daniyar] as $user) {
    printUserCard($user);
}

echo "\n== Вступление в клуб «{$club->title}» (код {$club->code->value}) ==\n";
foreach ([$aliya, $daniyar] as $student) {
    $m = $club->join($student);
    echo "{$student->fullName()} вступил(а) {$m->joinedAt->format('Y-m-d H:i:s')}\n";
}
echo 'Свободных мест: ' . $club->freePlaces() . PHP_EOL;

echo "\n== Обработка ошибок ==\n";
try {
    $club->join($aliya);
} catch (\DomainException $e) {
    echo 'Ошибка: ' . $e->getMessage() . PHP_EOL;
}
$third = new Student(12, 'Мадина Бек', 'madina@example.kz', 'ИС');
$club->join($third);
echo "{$third->fullName()} вступил(а), свободных мест: {$club->freePlaces()}\n";
$fourth = new Student(13, 'Ерлан Тлеу', 'erlan@example.kz', 'ИС');
try {
    $club->join($fourth);
} catch (\DomainException $e) {
    echo 'Ошибка: ' . $e->getMessage() . PHP_EOL;
}
