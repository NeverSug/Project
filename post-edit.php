<?php
require __DIR__ . '/vendor/autoload.php';

try {

    $categories = getCategories();
    $category_id = null;
    $post = [];
    $action = $_GET['action'] ?? '';
    // dd($action);

    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
        $id = (int)$_GET['id'];
        $post = getPost($id);
    }
    if (isset($_GET['action']) && $_GET['action'] === 'save') {
        $id = htmlspecialchars($_POST['id'] ?? '');
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
            updatePost([
                'id' => $id,
                'category_id' => $category_id,
                'title' => $title,
                'content' => $content
            ]);

            header("Location: post.php?id=$id&success=edit");
            die();
        }
    }
    if ($_GET['action'] !== 'edit' && $_GET['action'] !== 'save') {
        throw new OutOfBoundsException(("Неверный action"));
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
    <title>Изменение поста</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/components/menu.php'; ?>

    <p><a href='posts.php'>Вернуться к постам</a></p>

    <h1>Изменение поста</h1>

    <form action="/post-edit.php?action=save" method="post" class="post-create">
        <input type="text" name="id" readonly hidden value="<?= htmlspecialchars($post['id'] ?? $id ?? '') ?>">
        <h3>Категория</h3>
        <select name="category_id">
            <?php foreach ($categories as $category): ?>
                <option <?= htmlspecialchars(($category['id'] === ($post['category_id'] ?? $category_id)) ? 'selected' : '') ?> value="<?= htmlspecialchars($category['id']) ?>"> <?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="title">
            Заголовок поста:
            <input type="text" name="title" value="<?= htmlspecialchars($post['title'] ?? $title ?? '') ?>">
        </label>
        <?php if (!empty($errors['title'])): ?>
            <p class="error"><?= htmlspecialchars($errors['title']) ?></p>
        <?php endif; ?>

        <label for="content">
            Текст поста:
            <textarea name="content"><?= htmlspecialchars($post['content'] ?? $content ?? '') ?></textarea>
        </label>
        <?php if (!empty($errors['content'])): ?>
            <p class="error"><?= htmlspecialchars($errors['content']) ?></p>
        <?php endif; ?>
        <button class="btn" type="submit">Изменить</button>
    </form>
</body>

</html>