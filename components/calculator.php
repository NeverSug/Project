<?php
require dirname(__DIR__) . '/vendor/autoload.php';

$error = [];

$x = 0;
$y = 0;
$result = 0;
$operation = '';
$textResult = '';


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
            default => throw new Exception('Ошибка'),
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include __DIR__ . '/menu.php'; ?>
    <form action="" method="get">
        <input id="x" type="text" name="x" placeholder="0" value="<?= htmlspecialchars($x ?? '') ?>">
        <button data-operation="+" class="calcButton" type="button">+</button>
        <button data-operation="-" class="calcButton" type="button">-</button>
        <button data-operation="*" class="calcButton" type="button">*</button>
        <button data-operation="/" class="calcButton" type="button">/</button>

        <!-- <select name="operation"> -->
        <!-- <input type="submit" name="operation" value="+" <?= $operation === '+' ? 'selected' : '' ?>>
        <input type="submit" name="operation" value="-" <?= $operation === '-' ? 'selected' : '' ?>>
        <input type="submit" name="operation" value="*" <?= $operation === '*' ? 'selected' : '' ?>>
        <input type="submit" name="operation" value="/" <?= $operation === '/' ? 'selected' : '' ?>> -->
        <!-- </select> -->

        <input id="y" type="text" name="y" placeholder="0" value="<?= htmlspecialchars($y ?? '') ?>">

        <button class="clear" type="button">=</button>
        <input class="result" type="text" readonly id="result" value="<?= htmlspecialchars($result ?? '') ?>">
        <br>

        <button class="clear" type="button">Очистить историю</button>

        <div class="error" id="error"></div>

        <?php if ($error): ?>
            <?php foreach ($error as $err): ?>
                <div class="error"><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="result" id="textResult"><?= htmlspecialchars($textResult) ?></div>
        <?php endif; ?>


    </form>

    <p><a href="../index.php">← На главную</a></p>

    <?php include __DIR__ . '/footer.php'; ?>
    <script>
        window.onload = function() {
            document.querySelectorAll('.calcButton').forEach(button => {
                button.onclick = function() {
                    const x = document.getElementById('x').value;
                    const y = document.getElementById('y').value;
                    const operation = this.getAttribute('data-operation');
                    (
                        async () => {
                            try {
                                const response = await fetch(`?x=${x}&operation=${encodeURIComponent(operation)}&y=${y}&ajax`);
                                const result = await response.json();
                                switch (result.status) {
                                    case 'success':
                                        document.getElementById('result').value = result.data.result;
                                        document.getElementById('error').innerText = '';
                                        const textResult = document.getElementById('textResult')
                                        const infoResult = document.createElement('p')
                                        infoResult.innerText = result.data.textResult;
                                        textResult.insertBefore(infoResult, textResult.firstChild);
                                        break;
                                    case 'error':
                                        document.getElementById('result').value = 'Ошибка';
                                        const error = document.getElementById('error')
                                        const infoError = document.createElement('p')
                                        infoError.innerText = result.error;
                                        error.insertBefore(infoError, error.firstChild);
                                        break;
                                    default:
                                        console.error('Ошибка: неверный формат ответа');
                                }

                            } catch (error) {
                                console.log('Ошибка:', error)
                            }
                        }
                    )()
                }

            })
            document.querySelector('.clear').addEventListener('click', function() {
                const textResultContainer = document.getElementById('textResult');
                const errorContainer = document.getElementById('error');
                textResultContainer.innerHTML = '';
                errorContainer.innerHTML = '';

                document.getElementById('result').value = '';
            });
        }
    </script>
</body>

</html>