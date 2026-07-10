<?php
$errorConfig = [
    404 => [
        'title' => 'Страница не найдена',
        'message' => 'Запрашиваемая страница не существует или была перемещена.',
        'suggestions' => [
            'Проверьте правильность URL адреса',
            'Вернитесь на главную страницу',
            'Воспользуйтесь поиском по сайту'
        ]
    ],
    500 => [
        'title' => 'Внутренняя ошибка сервера',
        'message' => 'На сервере произошла техническая ошибка.',
        'suggestions' => [
            'Попробуйте обновить страницу через несколько минут',
            'Очистите кэш браузера',
            'Сообщите об ошибке администратору',
            'Попробуйте зайти позже'
        ]
    ]
];

$errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;
$errorMessage = isset($_GET['message']) ? urldecode($_GET['message']) : null;
$errorId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if (!array_key_exists($errorCode, $errorConfig)) {
    $errorCode = 404;
}

$config = $errorConfig[$errorCode] ?? $errorConfig[404];
if ($errorMessage) {
    $config['message'] = htmlspecialchars($errorMessage);
}

http_response_code($errorCode);

header('X-Robots-Tag: noindex, nofollow');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Ошибка <?= $errorCode ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <h1 class="error"><?= htmlspecialchars($errorCode . ' ' . $config['title']) ?></h1>
    <?php if (isset($errorId)): ?>
        <div class="error-container">
            <h1>Код ошибки:</h1> <?= htmlspecialchars($errorId) ?>
            <br>
            <p>Пожалуйста, сообщите этот код в службу поддержки</p>
        </div>
    <?php endif; ?>

    <div class="error-container">
        <h3>Что можно сделать:</h3>
        <ul>
            <?php foreach ($config['suggestions'] as $suggestion): ?>
                <li><?= $suggestion ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <p><a href="index.php">← На главную</a></p>
</body>

</html>