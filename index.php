<?php
$title = "Главная";
require __DIR__ . '/vendor/autoload.php';

try {
    $categories = getCategories();
    $posts = getPosts();
} catch (Exception $e) {

    // http_response_code(500);
    // $error = "Ошибка сервера: " . $e->getMessage();

    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    redirectToError(500, $e->getMessage(), $errorId);
}


?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Блог о PHP' ?></title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include __DIR__ . '/components/menu.php'; ?>

    <h2>Категории</h2>
    <?php if (isset($categories)): ?>
        <ul>
            <?php foreach ($categories as $cat): ?>
                <li title="<?= htmlspecialchars($cat['description']) ?>">
                    <a href="posts.php?category=<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Категории пока не добавлены.</p>
    <?php endif; ?>

    <p><a href="posts.php">Все посты</a></p>
    <?php include __DIR__ . '/components/footer.php'; ?>
</body>

</html>