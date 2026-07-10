<?php
require __DIR__ . '/vendor/autoload.php';

const STATUSES = [
    'ok' => 'Пост создан',
    'info' => 'Успешно',
    'delete' => 'Пост удален',
    'edit' => 'Пост успешно изменен',
];
$success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;

try {

    $id = $_GET['id'] ?? null;

    if (is_null($id)) {
        throw new OutOfBoundsException('ID поста не передан');
    }

    if (!is_numeric($id)) {
        throw new OutOfBoundsException('ID поста должен быть числом');
    }

    $post = getPost($id);
    // dd($post);
    $categories = getCategories();
    // dd($categories[$post['category_id']]['name']);
} catch (OutOfBoundsException $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    redirectToError(404, $e->getMessage(), $errorId);
} catch (Exception $e) {
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <?php include __DIR__ . '/components/menu.php'; ?>

    <p><a href='posts.php'>Вернуться к постам</a></p>

    <?php if (!empty($success)): ?>
        <p class="result"><?= $success ?></p>
    <?php endif; ?>

    <?php if (!isset($error)): ?>
        <article class="single-post">
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <p><strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?></p>
            <p><strong>Дата:</strong> <?= htmlspecialchars($post['date']) ?></p>
            <p><strong>Категория:</strong> <?= htmlspecialchars($categories[$post['category_id']]['name']) ?></p>
            <div class="content">
                <p><?= htmlspecialchars($post['content']) ?></p>
            </div>
        </article>

        <p><a href="posts.php">← Все посты</a></p>
    <?php else: ?>
        <?php include __DIR__ . '/components/menu.php'; ?>
        <?= htmlspecialchars($error) ?>
        <p><a href="posts.php">← Все посты</a></p>
    <?php endif; ?>

    <?php include __DIR__ . '/components/footer.php'; ?>
</body>

</html>