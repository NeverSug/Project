<?php
require __DIR__ . '/vendor/autoload.php';

const STATUSES = [
    'ok' => 'Пост создан',
    'info' => 'Успешно',
    'delete' => 'Пост удален',
    'like' => 'Лайк присвоен'
];
$success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;


try {

    $posts = getPosts();
    $post = [];
    $categorySlug = $_GET['category'] ?? null;

    if (isset($_GET['action']) && $_GET['action'] === 'like') {
        $id = (int)$_GET['id'] ?? null;
        $posts[$id]['like'] = ($posts[$id]['like'] ?? 0) + 1;;
        $data = __DIR__ . '/data/posts.json';
        upDate($data, $posts);
        if (isset($_GET['ajax'])) {
            if (empty($error)) {
                $result = [
                    'status' => 'success',
                    'count' => $posts[$id]['like'],
                    'result' => 'Лайк присвоен'
                ];
            } else {
                $result = [
                    'status' => 'error'
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }
        header("Location: /posts.php?success=like");
        die();
    }


    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = (int)$_GET['id'] ?? null;
        deletePost($id);

        if (isset($_GET['ajax'])) {
            if (empty($error)) {
                $result = [
                    'status' => 'success',
                    'result' => 'Пост успешно удален'
                ];
            } else {
                $result = [
                    'status' => 'error'
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

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
    <div id="messages"></div>
    <?php if (!empty($success)): ?>
        <p class="result"><?= $success ?></p>
    <?php endif; ?>

    <?php if (!isset($error)): ?>
        <?php if (empty($filtered)): ?>
            <p>Нет постов.</p>
        <?php else: ?>
            <?php foreach ($filtered as $post): ?>
                <div class="post-preview" id="<?= $post['id'] ?>">
                    <div class="post-title">
                        <h2><a href="post.php?id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                        <div>
                            <a href="/post-edit.php?action=edit&id=<?= $post['id'] ?>">Редактировать</a> |
                            <a href="/posts.php?action=delete&id=<?= $post['id'] ?>">Удалить</a>
                            <button type="button" data-id="<?= $post['id'] ?>" class="btn deleteBtn">Удалить</button>
                        </div>
                    </div>
                    <p><strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?> | <?= htmlspecialchars($post['date']) ?></p>
                    <div class="post-title">
                        <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                        <button data-id="<?= htmlspecialchars($post['id'] ?? 0) ?>" class="like" type="button" value="<?= htmlspecialchars($post['like'] ?? 0) ?>">💗<?= htmlspecialchars($post['like'] ?? 0) ?></button>
                        <!-- <a href="/posts.php?action=like&id=<?= $post['id'] ?>">💗<?= htmlspecialchars($post['like'] ?? 0) ?></a> -->
                    </div>
                    <hr>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>
        <?= htmlspecialchars($error) ?>
    <?php endif; ?>

    <p><a href="index.php">← На главную</a></p>

    <?php include __DIR__ . '/components/footer.php'; ?>
    <script>
        window.onload = function() {
            document.querySelectorAll('.deleteBtn').forEach(button => {
                button.onclick = function() {
                    const id = this.getAttribute('data-id');
                    const messagesBox = document.getElementById('messages');
                    (
                        async () => {
                            try {
                                const response = await fetch(`?action=delete&id=${id}&ajax`);
                                const result = await response.json();
                                switch (result.status) {
                                    case 'success':
                                        document.getElementById(id).remove();
                                        messagesBox.innerHTML = '';
                                        const resultText = document.createElement('p');
                                        resultText.innerText = result.result;
                                        resultText.classList.add('result');
                                        messagesBox.appendChild(resultText);
                                        break;
                                    case 'error':
                                        console.error('Ошибка: Невозможно удалить этот пост');
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
            document.querySelectorAll('.like').forEach(button => {
                button.onclick = function() {
                    const id = this.getAttribute('data-id');
                    const messagesBox = document.getElementById('messages');
                    (
                        async () => {
                            try {
                                const response = await fetch(`?action=like&id=${id}&ajax`);
                                const result = await response.json();
                                switch (result.status) {
                                    case 'success':
                                        const count = button.value;
                                        button.textContent = `💗 ${result.count}`;
                                        button.classList.add('like-button');
                                        setTimeout(() => button.classList.remove('like-button'), 1500);
                                        messagesBox.innerHTML = '';
                                        const resultText = document.createElement('p');
                                        resultText.innerText = result.result;
                                        resultText.classList.add('result');
                                        messagesBox.appendChild(resultText);
                                        break;
                                    case 'error':
                                        console.error('Ошибка: Невозможно оценить этот пост');
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
        }
    </script>
</body>

</html>