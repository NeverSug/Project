<?php
require __DIR__ . '/vendor/autoload.php';

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = htmlspecialchars($_POST['name'] ?? '');
        $slug = htmlspecialchars($_POST['slug'] ?? '');
        $description = htmlspecialchars($_POST['description'] ?? '');

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Введите название';
        };
        if (empty($slug)) {
            $errors['slug'] = 'Укажите тему, она будет указываться в тегах';
        };
        if (empty($description)) {
            $errors['description'] = 'Описание необходимо для понимания темы';
        };

        if (empty($errors)) {

            $result = saveCategory($name, $slug, $description);

            header("Location: post-create.php?success=ok");
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
    <title>Создание новой категории</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/components/menu.php'; ?>

    <p><a href='post-create.php'>Вернуться к созданию поста</a></p>

    <h1>Создание категории</h1>

    <form action="" method="post" class="post-create">
        <label for="title">
            Название категории:
            <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>">
        </label>
        <?php if (!empty($errors['name'])): ?>
            <p class="error"><?= htmlspecialchars($errors['name']) ?></p>
        <?php endif; ?>
        <label for="slug">
            Тема:
            <input type="text" name="slug" value="<?= htmlspecialchars($slug ?? '') ?>">
        </label>
        <?php if (!empty($errors['slug'])): ?>
            <p class="error"><?= htmlspecialchars($errors['slug']) ?></p>
        <?php endif; ?>

        <label for="description">
            Описание:
            <textarea name="description"><?= htmlspecialchars($description ?? '') ?></textarea>
        </label>
        <?php if (!empty($errors['description'])): ?>
            <p class="error"><?= htmlspecialchars($errors['description']) ?></p>
        <?php endif; ?>
        <button class="btn" type="submit">Создать</button>
    </form>
</body>

</html>