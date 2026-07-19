<?php

namespace App\Controllers;

use function App\render;

function calculatorControllers(): void
{
    $titleSite = 'Калькулятор';
    $error = [];

    $x = 0;
    $y = 0;
    $result = 0;
    $operation = '';
    $textResult = '';
    $hasCalculationAttempt = false;

    if (!empty($_GET)) {
        $x = trim($_GET['x'] ?? '');
        $y = trim($_GET['y'] ?? '');
        $operation = trim($_GET['operation'] ?? '');


        if ($x === '') {
            $error[] = 'Первое значение не может быть пустым';
        }
        if ($y === '') {
            $error[] = 'Второе значение не может быть пустым';
        }

        if ($x !== '' && !is_numeric($x)) {
            $error[] = 'Первое значение должно быть числом';
        }
        if ($y !== '' && !is_numeric($y)) {
            $error[] = 'Второе значение должно быть числом';
        }
        if (!in_array($operation, ["+", "-", "*", "/"], true)) {
            $error[] = "Неверная операция";
        }

        if (empty($error)) {

            $x = (float)$x;
            $y = (float)$y;
            $result = match ($operation) {
                '+' =>
                $x + $y,
                '-' =>
                $x - $y,
                '*' =>
                $x * $y,
                '/' => ($y !== 0.0) ? $x / $y :  'Деление на ноль невозможно',
                default => throw new \Exception('Ошибка'),
            };
            if (is_numeric($result)) {
                $result = round($result, 2);
            }
            $textResult = "$x $operation $y = $result";
        }

        if (isset($_GET['ajax'])) {
            if (empty($error)) {
                $result = [
                    'status' => 'success',
                    'data' => [
                        'result' => $result,
                        'textResult' => $textResult,
                    ]

                ];
            } else {
                $result = [
                    'status' => 'error',
                    'error' => $error,

                ];
            }

            header('Content-Type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }
    }
    echo render('calculator', [
        'titleSite' => $titleSite,
        'x' => $x,
        'y' => $y,
        'result' => $result,
        'textResult' => $textResult,
        'hasCalculationAttempt' => $hasCalculationAttempt,
        'error' => $error ?? null,
    ]);
}
