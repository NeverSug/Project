<?php
require __DIR__ . '/vendor/autoload.php';

const STATUSES = [
    'ok' => 'Пост создан',
    'info' => 'Успешно',
    'delete' => 'Пост удален',
];
$success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;

try {

    $posts = getPosts();
    $categorySlug = $_GET['category'] ?? null;

    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = (int)$_GET['id'] ?? null;
        deletePost($posts, $id);
        header("Location: /posts.php?success=delete");
        die();
    }

    if (!isset($posts)) {
        header('Location: /error-tmp.php?code=404');
        exit;
    }


    if ($categorySlug) {
        $filtered = getPostsCategoriesBySlug($categorySlug);
        $categoryInfo = getCategoryBySlug($categorySlug);
        $title = "Категория: " . $categoryInfo['name'];
    } else {
        $filtered = $posts;
        $title = "Все посты";
    }
} catch (Exception $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    redirectToError(404, $e->getMessage(), $errorId);
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

    <?php include __DIR__ . '/components/menu.php';
    ?>

    <p><a href="post-create.php">+ Создать пост</a></p>

    <?php if (!empty($success)): ?>
        <p class="result"><?= $success ?></p>
    <?php endif; ?>

    <?php if (!isset($error)): ?>
        <?php if (empty($filtered)): ?>
            <p>Нет постов.</p>
        <?php else: ?>
            <?php foreach ($filtered as $post): ?>
                <div class="post-preview">
                    <div class="post-title">
                        <h2><a href="post.php?id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                        <div>
                            <a href="/post-edit.php?action=edit&id=<?= $post['id'] ?>">Редактировать</a> |
                            <a href="/posts.php?action=delete&id=<?= $post['id'] ?>">Удалить</a>
                        </div>
                    </div>
                    <p><strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?> | <?= htmlspecialchars($post['date']) ?></p>
                    <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>
        <?= htmlspecialchars($error) ?>
    <?php endif; ?>

    <p><a href="index.php">← На главную</a></p>

    <?php include __DIR__ . '/components/footer.php'; ?>
</body>

</html>