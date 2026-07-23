<?php

namespace App\Controllers;

use function App\getCurrentUser;
use function App\getCategories;
use function App\getPost;
use function App\getPosts;
use function App\render;
use function App\deletePost;
use function App\updatePost;
use function App\getPostsCategoriesBySlug;
use function App\savePost;
use function App\enrichPostWithLikes;
use function App\toggleLike;

function postsControllers(): void
{
    $success = '';
    $titleSite = 'Все посты';
    $posts = getPosts();
    $post = [];
    $categorySlug = $_GET['category'] ?? null;
    $errorMsg = '';

    if (isset($_GET['action']) && $_GET['action'] === 'like') {
        $id = (int)($_GET['id'] ?? null);

        if (!$id) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'result' => 'Неверный ID'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $result = toggleLike($id);

        if ($result['status'] === 'error') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'result' => $result['message'] ?? 'Не удалось поставить лайк'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'count' => $result['count'],
            'liked' => $result['liked'],
            'result' => $result['liked'] ? 'Лайк присвоен' : 'Лайк удалён'
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }


    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = (int)($_GET['id'] ?? null);

        if (!$id) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'result' => 'Неверный ID'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $user = getCurrentUser();

        if (!$user) {
            if (isset($_GET['ajax'])) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'status'  => 'error',
                    'result'  => 'Требуется авторизация для удаления поста'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            header("Location: /?page=posts");
            exit;
        }

        $post = getPost($id, $user['id']);

        if (!$post) {
            if (isset($_GET['ajax'])) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode([
                    'status'  => 'error',
                    'result'  => 'Пост не найден'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            header("Location: /?page=posts");
            exit;
        }

        if (!$user['is_admin'] && $post['user_id'] !== $user['id']) {
            if (isset($_GET['ajax'])) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'status'  => 'error',
                    'result'  => 'Вы не можете удалить пост, так как не являетесь его автором'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            header("Location: /?page=posts");
            exit;
        }

        deletePost($id);

        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'result' => 'Пост успешно удален'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        $_SESSION['message'] = 'Пост успешно удалён';
        header("Location: /?page=posts&success=1");
        exit;
    }

    if (!empty($_GET['success'])) {
        $success = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
    }

    if (empty($posts)) {
        header('Location: /?page=errorhendler&code=404');
        exit;
    }


    if ($categorySlug) {
        $filtered = getPostsCategoriesBySlug($categorySlug);
    } else {
        $filtered = $posts;
    }
    foreach ($filtered as &$postItem) {
        $postItem = enrichPostWithLikes($postItem);
    }
    unset($postItem);
    echo render('posts/index', [
        'titleSite' => $titleSite,
        'success' => $success,
        'filtered' => $filtered,
        'post' => $post,
        'error' => $errorMsg,
    ]);
}

function postControllers(): void
{
    $id = (int)(($_GET['id'] ?? null) ?: ($_POST['id'] ?? null));

    if (!$id) {
        if (isset($_GET['action']) && $_GET['action'] === 'like' && isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'result' => 'Не указан ID поста'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        throw new \InvalidArgumentException('Не указан ID поста');
    }

    $user = getCurrentUser();
    $user_id = $user ? ($user['id'] ?? null) : null;
    $post = getPost((int)$id, $user_id);

    // var_dump($user_id, $post);
    // die;
    if (!$post) {
        if (isset($_GET['action']) && $_GET['action'] === 'like' && isset($_GET['ajax'])) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'result' => 'Пост не найден'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        header('Location: /?page=errorhendler&code=404');
        exit;
    }

    $success = '';
    $error = '';

    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $canDelete = false;

        if ($user) {
            if ($user['is_admin'] === true || $post['user_id'] == $user['id']) {
                $canDelete = true;
            }
        }

        if (!$canDelete) {
            $_SESSION['message'] = 'У вас нет прав на удаление этого поста';
            header("Location: /?page=posts");
            exit;
        }

        deletePost($id);
        $_SESSION['message'] = 'Пост успешно удалён';
        header("Location: /?page=posts&success=1");
        exit;
    }
    if (!empty($_GET['success'])) {
        $success = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
    }

    if (isset($_GET['action']) && $_GET['action'] === 'like') {
        $result = toggleLike($id);

        if ($result['status'] === 'error') {
            if (isset($_GET['ajax'])) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'result' => $result['message'] ?? 'Ошибка при обработке лайка'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $error = $result['message'] ?? 'Не удалось поставить лайк';
        } else {
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'count' => $result['count'],
                    'liked' => $result['liked'],
                    'result' => $result['liked'] ? 'Лайк присвоен' : 'Лайк удалён'
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }

            $_SESSION['message'] = $result['liked'] ? 'Лайк поставлен' : 'Лайк снят';
            header("Location: /?page=posts&id={$id}&success=like");
            exit;
        }
    }
    $post = enrichPostWithLikes($post);
    $categories = getCategories();
    $titleSite = $post['title'];
    echo render('posts/show', [
        'titleSite' => $titleSite,
        'success' => $success,
        'categories' => $categories,
        'post' => $post,
        'error' => $error,
        'user' => $user,
    ]);
}

function posteditControllers(): void
{
    $id = (int)(($_GET['id'] ?? null) ?: ($_POST['id'] ?? null));
    if (!$id) {
        throw new \InvalidArgumentException('Не указан ID поста');
    }

    $errors = [];

    $category_id = null;
    $titleSite = 'Редактирование поста';
    $user = getCurrentUser();
    $user_id = $user ? ($user['id'] ?? null) : null;
    $post = getPost($id, $user_id);

    if (!$post) {
        throw new \OutOfBoundsException(("Пост не найден"));
    }

    $title = $post['title'] ?? '';
    $content = $post['content'] ?? '';
    $category_id = (int)($post['category_id'] ?? null);
    $image = $post['image'] ?? '';

    if (isset($_GET['action']) && $_GET['action'] !== 'edit' && $_GET['action'] !== 'save' && $_GET['action'] !== 'delete_image') {
        throw new \OutOfBoundsException(("Неверный action"));
    }

    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
        $post = getPost($id, $user_id);
        $canUpdare = false;

        if ($user) {
            if ($post['user_id'] == $user['id']) {
                $canUpdare = true;
            }
        }

        if (!$canUpdare) {
            $_SESSION['message'] = 'У вас нет прав редактировать этот пост';
            header("Location: /?page=posts&success=1");
            exit;
        }
    }
    if (!empty($post)) {
        if (isset($_GET['action']) && $_GET['action'] === 'delete_image') {

            updatePost([
                'id' => $id,
                'category_id' => $category_id,
                'title' => $title,
                'content' => $content,
                'author' => $user['nickname'],
                'image' => '',
                'user_id' => $user['id']
            ], $user);
            $error = '';
            if (isset($_GET['ajax'])) {
                if ($error === '') {
                    $result = [
                        'status' => 'success',
                        'result' => 'Картинка удалена'
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
            header("Location: /?page=postedit&action=edit&id=$id");
            die();
        }

        if (isset($_GET['action']) && $_GET['action'] === 'save') {

            $newImage = $image;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $extensionMimeMap = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'avif' => 'image/avif',
                ];
                $maxFileSize = 5 * 1024 * 1024;
                if ($_FILES['image']['size'] > $maxFileSize) {
                    $errors['image'] = 'Размер файла превышает допустимый';
                }

                $uploadDir = __DIR__ . '/../public/upload/';
                $newImage = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($newImage, PATHINFO_EXTENSION));

                if (!array_key_exists($fileExtension, $extensionMimeMap)) {
                    $errors['image'] = 'Недопустимый тип файла';
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedMimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
                $ext = $extensionMimeMap[$fileExtension] ?? '';
                if ($ext !== $detectedMimeType) {
                    $errors['image'] = 'Недопустимый тип файла';
                }

                if (empty($errors['image'])) {
                    $safeFileName = uniqid() . '_' . date('Y-m-d_H-i-s') . '_' . $fileExtension;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $safeFileName)) {
                        $newImage = $safeFileName;
                    } else {
                        $errors['image'] = 'Файл не загружен';
                    }
                }
            }

            if (empty($title)) {
                $errors['title'] = 'Укажите заголовок. Без него рейтинг поста будет ниже';
            };
            if (empty($content)) {
                $errors['content'] = 'Раскройте тему. Иначе пользователи не поймут, о чем Вы пишите';
            };

            $lengthContent = mb_strlen($content);
            if ($lengthContent <= 5 && 0 < $lengthContent) {
                $errors['content'] = 'Раскройте тему. Нужно больше информации';
            }
            if (empty($errors)) {
                updatePost([
                    'id' => $id,
                    'category_id' => $category_id,
                    'title' => $title,
                    'content' => $content,
                    'author' => $user['nickname'],
                    'image' => $newImage,
                    'user_id' => $user['id']
                ], $user);

                header("Location: /?page=post&id=$id&success=edit");
                die();
            }
        }
    }
    $categories = getCategories();

    echo render('posts/post-edit', [
        'titleSite' => $titleSite,
        'categories' => $categories,
        'category_id' => $category_id,
        'post' => $post,
        'id' => $id ?? '',
        'errors' => $errors ?? null,
        'title' => $title ?? '',
        'content' => $content ?? '',
        'image' => $image ?? '',

    ]);
}

function postcreateControllers(): void
{
    $success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;
    $titleSite = 'Создание поста';
    $categories = getCategories();
    $category_id = null;
    $fileName = '';
    $user = $_SESSION['user'] ?? null;

    if (!$user || !isset($user['id'])) {
        $_SESSION['message'] = 'Для создания поста нужно войти в систему';
        header('Location: /?page=posts&success=1');
        exit;
    }
    $userId = (int)$user['id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $errors = [];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $extensionMimeMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'avif' => 'image/avif',
            ];
            $maxFileSize = 5 * 1024 * 1024;
            if ($_FILES['image']['size'] > $maxFileSize) {
                $errors['image'] = 'Размер файла превышает допустимый';
            }

            $uploadDir = __DIR__ . '/../public/upload/';
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!array_key_exists($fileExtension, $extensionMimeMap)) {
                $errors['image'] = 'Недопустимый тип файла';
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedMimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
            $ext = $extensionMimeMap[$fileExtension] ?? '';
            if ($ext !== $detectedMimeType) {
                $errors['image'] = 'Недопустимый тип файла';
            }
            $safeFileName = uniqid() . '_' . date('Y-m-d_H-i-s') . '_' . $fileExtension;

            if (!isset($errors['image'])) {
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $safeFileName)) {
                    $errors['image'] = 'Файл не загружен';
                }
                $fileName = $safeFileName;
            }
        }

        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? null);
        $author = $_SESSION['user']['nickname'] ?? 'guest';
        $date = htmlspecialchars($_POST['date'] ?? date('Y-m-d H:i:s'));

        if (empty($title)) {
            $errors['title'] = 'Укажите заголовок. Без него рейтинг поста будет ниже';
        }
        if (empty($content)) {
            $errors['content'] = 'Раскройте тему. Иначе пользователи не поймут, о чем Вы пишите';
        }

        $lengthContent = mb_strlen($content);
        if ($lengthContent <= 5 && 0 < $lengthContent) {
            $errors['content'] = 'Раскройте тему. Нужно больше информации';
        }

        if (empty($author)) {
            $errors['author'] = 'Назовите автора. Он не должен оставаться инкогнито';
        }
        if (empty($date)) {
            $errors['date'] = 'Укажите дату в формате "Y-m-d H:i:s". Пользователям важна актуальность поста';
        }

        if (empty($errors)) {

            $result = savePost([
                'category_id' => $category_id,
                'title' => $title,
                'content' => $content,
                'date' => $date,
                'author' =>  $author,
                'like' => 0,
                'image' => $fileName,
                'user_id' => $userId
            ]);

            header("Location: /?page=post&id=$result&success=ok");
            die();
        }
    }

    echo  render('posts/post-create', [
        'titleSite' => $titleSite,
        'categories' => $categories,
        'category_id' => $category_id,
        'title' => $title ?? '',
        'content' => $content ?? '',
        'author' => $author ?? '',
        'date' => $date ?? date('Y-m-d H:i:s'),
        'errors' => $errors ?? null,
        'success' => $success,
    ]);
}
