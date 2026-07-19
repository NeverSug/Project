<?php

namespace App\Controllers;

use function App\getCategories;
use function App\getPost;
use function App\getPosts;
use function App\render;
use function App\writeFileData;
use function App\deletePost;
use function App\updatePost;
use function App\getCategoryBySlug;
use function App\getPostsCategoriesBySlug;
use function App\savePost;

function postsControllers(): void
{
    $success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;
    $titleSite = 'Все посты';
    $posts = getPosts();
    $post = [];
    $categorySlug = $_GET['category'] ?? null;
    $error = $_GET['error'] ?? '';

    if (isset($_GET['action']) && $_GET['action'] === 'like') {
        $id = (int)$_GET['id'] ?? null;
        $posts[$id]['like'] = ($posts[$id]['like'] ?? 0) + 1;
        $data = __DIR__ . '/../data/posts.json';
        writeFileData($data, $posts);
        if (isset($_GET['ajax'])) {
            if ($error === '') {
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
        header("Location: /?page=posts&success=like");
        die();
    }


    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = (int)$_GET['id'] ?? null;
        deletePost($id);

        if (isset($_GET['ajax'])) {
            if ($error === '') {
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

        header("Location: /?page=posts&success=delete");
        die();
    }

    if (!isset($posts)) {
        header('Location: /?page=errorhendler&code=404');
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

    echo render('posts/index', [
        'titleSite' => $titleSite,
        'success' => $success,
        'filtered' => $filtered,
        'post' => $post,
        'error' => $error,
    ]);
}

function postControllers(): void
{
    $success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;
    $id = $_GET['id'] ?? null;
    $error = '';


    if (is_null($id)) {
        throw new \OutOfBoundsException('ID поста не передан');
    }

    if (!is_numeric($id)) {
        throw new \OutOfBoundsException('ID поста должен быть числом');
    }

    $post = getPost($id);

    if (isset($_GET['action']) && $_GET['action'] === 'like') {
        $id = (int)$_GET['id'] ?? null;
        $posts[$id]['like'] = ($posts[$id]['like'] ?? 0) + 1;
        $data = __DIR__ . '/../data/posts.json';
        writeFileData($data, $posts);
        if (isset($_GET['ajax'])) {
            if ($error === '') {
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
        header("Location: /?page=posts&success=like");
        die();
    }

    $categories = getCategories();
    $titleSite = $post['title'];
    echo render('posts/show', [
        'titleSite' => $titleSite,
        'success' => $success,
        'categories' => $categories,
        'post' => $post,
        'error' => $error,
    ]);
}

function posteditControllers(): void
{
    $category_id = null;
    $post = [];
    $titleSite = 'Редактирование поста';

    if (isset($_GET['action']) && $_GET['action'] === 'delete_image') {
        $id = htmlspecialchars($_POST['id'] ?? '');
        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? null);
        updatePost([
            'id' => $id,
            'category_id' => $category_id,
            'title' => $title,
            'content' => $content,
            'image' => '',
        ]);
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

    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
        $id = (int)$_GET['id'];
        $post = getPost($id);
    }
    if (isset($_GET['action']) && $_GET['action'] === 'save') {
        $id = htmlspecialchars($_POST['id'] ?? '');
        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $image = $post['image'] ?? '';
        $category_id = (int)($_POST['category_id'] ?? null);
        $errors = [];
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

        $lenghtContent = mb_strlen($content);
        if ($lenghtContent <= 5 && 0 < $lenghtContent) {
            $errors['content'] = 'Раскройте тему. Нужно больше информации';
        }
        if (empty($errors)) {
            updatePost([
                'id' => $id,
                'category_id' => $category_id,
                'title' => $title,
                'content' => $content,
                'image' => $newImage,
            ]);

            header("Location: /?page=post&id=$id&success=edit");
            die();
        }
    }
    if ($_GET['action'] !== 'edit' && $_GET['action'] !== 'save') {
        throw new \OutOfBoundsException(("Неверный action"));
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
        $author = htmlspecialchars($_POST['author'] ?? '');
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
                'author' => $author,
                'like' => 0,
                'image' => $fileName
            ]);

            header("Location: /?page=post&id=$result&success=ok");
            die();
        }
    }

    echo render('posts/post-create', [
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
