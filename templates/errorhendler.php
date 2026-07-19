<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titleSite ?></title>
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