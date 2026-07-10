<?php
require __DIR__ . '/vendor/autoload.php';
const STATUSES = [
    'ok' => 'Категория создана',
];
$success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;

try {
    $categories = getCategories();
    $category_id = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? null);

        $errors = [];

        if (empty($title)) {
            $errors['title'] = 'Укажите заголовок. Без него рейтинг поста будет ниже';
        };
        if (empty($content)) {
            $errors['content'] = 'Раскройте тему. Иначе пользователи не поймут, о чем Вы пишите';
        };

        $lenghtContent = mb_strlen($content);
        if ($lenghtContent <= 5 && 0 < $lenghtContent) {
            $errors['content'] = 'Раскройте тему. Нужно больше информации';
        }

        if (empty($errors)) {

            $result = savePost($category_id, $title, $content);

            header("Location: post.php?id=$result&success=ok");
            die();
        }
    }
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
    <title>Создание нового поста</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/components/menu.php'; ?>

    <p><a href='posts.php'>Вернуться к постам</a></p>

    <h1>Создание поста</h1>

    <form action="" method="post" class="post-create">
        <h3>Категория</h3>
        <?php if (!empty($success)): ?>
            <p class="result"><?= $success ?></p>
        <?php endif; ?>
        <select name="category_id">
            <?php foreach ($categories as $category): ?>
                <option <?= htmlspecialchars(($category['id'] === $category_id) ? 'selected' : '') ?>value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <p><a href="category-create.php">+ Создать категорию</a></p>
        <label for="title">
            Заголовок поста:
            <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>">
        </label>
        <?php if (!empty($errors['title'])): ?>
            <p class="error"><?= htmlspecialchars($errors['title']) ?></p>
        <?php endif; ?>

        <label for="content">
            Текст поста:
            <textarea name="content"><?= htmlspecialchars($content ?? '') ?></textarea>
        </label>
        <?php if (!empty($errors['content'])): ?>
            <p class="error"><?= htmlspecialchars($errors['content']) ?></p>
        <?php endif; ?>
        <button class="btn" type="submit">Создать</button>
    </form>
</body>

</html>