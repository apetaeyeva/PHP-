<?php

declare(strict_types=1);

// ==========================================
// Задание 2-3. Данные студента и определение типов
// ==========================================
$studentName = "Апетаева Талшын";
$course = 3;
$program = "Информационные системы";
$averageGrade = 85.75;
$hasDebt = false;
$debtText = $hasDebt ? "есть" : "нет";

// ==========================================
// Задание 4-5. Расчет итоговой оценки
// ==========================================
const PASSING_SCORE = 50;

$laboratoryGrade = 85;
$independentGrade = 78;
$examGrade = 92;

$result = ($laboratoryGrade * 0.30) + ($independentGrade * 0.20) + ($examGrade * 0.50);
$status = $result >= PASSING_SCORE ? "Дисциплина освоена" : "Дисциплина не освоена";

// ==========================================
// Задание 6. Явное преобразование типов
// ==========================================
$inputValue = "125";
$integerValue = (int) $inputValue;
$floatValue = (float) $inputValue;
$booleanValue = (bool) $inputValue;

// ==========================================
// Индивидуальный Вариант №6: Успеваемость
// ==========================================
const PASSING_GRADE = 60.0;

$g1 = 88;
$g2 = 75;
$g3 = 90;
$g4 = 82;

$gpa = ($g1 + $g2 + $g3 + $g4) / 4;
$studentStatus = ($gpa >= PASSING_GRADE) ? "Аттестован" : "Не аттестован";

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №2 - Апетаева Талшын</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        h2 { color: #333; }
    </style>
</head>
<body>

    <h1>Лабораторная работа №2. Типы данных и выражения в PHP</h1>

    <h2>Информация о студенте</h2>
    <p>ФИО: <?= htmlspecialchars($studentName) ?></p>
    <p>Курс: <?= $course ?></p>
    <p>Программа: <?= htmlspecialchars($program) ?></p>
    <p>Задолженность: <?= $debtText ?></p>

    <h2>Результаты базового расчета</h2>
    <table>
        <tr>
            <th>Студент</th>
            <th>Курс</th>
            <th>Итоговая оценка</th>
            <th>Статус</th>
        </tr>
        <?php
        printf(
            "<tr><td>%s</td><td>%d</td><td>%.2f</td><td>%s</td></tr>",
            htmlspecialchars($studentName),
            $course,
            $result,
            $status
        );
        ?>
    </table>

    <h2>Индивидуальный вариант №6 (Успеваемость)</h2>
    <table>
        <tr>
            <th>Оценка 1</th>
            <th>Оценка 2</th>
            <th>Оценка 3</th>
            <th>Оценка 4</th>
            <th>Средний балл</th>
            <th>Статус</th>
        </tr>
        <tr>
            <td><?= $g1 ?></td>
            <td><?= $g2 ?></td>
            <td><?= $g3 ?></td>
            <td><?= $g4 ?></td>
            <td><?= number_format($gpa, 2, '.', '') ?></td>
            <td><?= $studentStatus ?></td>
        </tr>
    </table>

</body>
</html>