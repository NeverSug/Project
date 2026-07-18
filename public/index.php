<?php
require __DIR__ . '/../vendor/autoload.php';

$success = isset($_GET['success']) ? STATUSES[($_GET['success'] ?? null)] : null;

$page = (string)($_GET['page'] ?? 'index');

try {
    switch ($page) {
        case 'index':
            $titleSite = "Главная";
            $categories = getCategories();
            $posts = getPosts();
            echo render('index', [
                'title' => $titleSite,
                'categories' => $categories,
                'posts' => $posts,
            ]);
            // include __DIR__ . '/../templates/index.php';
            break;
        case 'posts':
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
                header('Location: /?page=error-tmp&code=404');
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
            // include __DIR__ . '/../templates/posts.php';
            break;
        case 'post':
            $id = $_GET['id'] ?? null;
            $error = '';

            if (is_null($id)) {
                throw new OutOfBoundsException('ID поста не передан');
            }

            if (!is_numeric($id)) {
                throw new OutOfBoundsException('ID поста должен быть числом');
            }

            $post = getPost($id);

            $categories = getCategories();
            $titleSite = $post['title'];
            echo render('posts/show', [
                'titleSite' => $titleSite,
                'success' => $success,
                'categories' => $categories,
                'post' => $post,
                'error' => $error,
            ]);
            // include __DIR__ . '/../templates/posts/show.php';
            break;
        case 'post-edit':
            $category_id = null;
            $post = [];
            $titleSite = 'Редактирование поста';

            // dd($action);

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
                header("Location: /?page=post-edit&action=edit&id=$id");
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
                    // $uploadDir = 'upload/';

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
                throw new OutOfBoundsException(("Неверный action"));
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
            // include __DIR__ . '/../templates/post-edit.php';
            break;
        case 'post-create':
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
                    // $uploadDir = 'upload/';

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
            // include __DIR__ . '/../templates/posts/post-create.php';
            break;
        case 'error-tmp':
            $errorConfig = [
                404 => [
                    'title' => 'Страница не найдена',
                    'message' => 'Запрашиваемая страница не существует или была перемещена.',
                    'suggestions' => [
                        'Проверьте правильность URL адреса',
                        'Вернитесь на главную страницу',
                        'Воспользуйтесь поиском по сайту'
                    ]
                ],
                500 => [
                    'title' => 'Внутренняя ошибка сервера',
                    'message' => 'На сервере произошла техническая ошибка.',
                    'suggestions' => [
                        'Попробуйте обновить страницу через несколько минут',
                        'Очистите кэш браузера',
                        'Сообщите об ошибке администратору',
                        'Попробуйте зайти позже'
                    ]
                ]
            ];
            $errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;
            $errorMessage = isset($_GET['message']) ? urldecode($_GET['message']) : null;
            $errorId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

            if (!array_key_exists($errorCode, $errorConfig)) {
                $errorCode = 404;
            }

            $config = $errorConfig[$errorCode] ?? $errorConfig[404];
            if ($errorMessage) {
                $config['message'] = htmlspecialchars($errorMessage);
            }

            http_response_code($errorCode);

            header('X-Robots-Tag: noindex, nofollow');
            echo renderTemplate('error-tmp', [
                'errorCode' => $errorCode,
                'config' => $config,
                'errorId' => $errorId,
            ]);

            // include __DIR__ . '/../templates/error-tmp.php';
            break;
        case 'category-create':
            $titleSite = 'Создание категории';

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

                    header("Location: /?page=post-create&success=okcat");
                    die();
                }
            }
            echo render('category-create', [
                'titleSite' => $titleSite,
                'errors' => $errors ?? null,
                'name' => $name ?? '',
                'slug' => $slug ?? '',
                'description' => $description ?? ''
            ]);
            // include __DIR__ . '/../templates/category-create.php';
            break;
        case 'calculator':
            $titleSite = 'Калькулятор';
            $error = [];

            $x = 0;
            $y = 0;
            $result = 0;
            $operation = '';
            $textResult = '';
            $hasCalculationAttempt = false;


            if (!empty($_GET)) {
                $x = trim($_GET['x'] ?? '');
                $y = trim($_GET['y'] ?? '');
                $operation = trim($_GET['operation'] ?? '');


                if ($x === '') {
                    $error[] = 'Первое значение не может быть пустым';
                }
                if ($y === '') {
                    $error[] = 'Второе значение не может быть пустым';
                }

                if ($x !== '' && !is_numeric($x)) {
                    $error[] = 'Первое значение должно быть числом';
                }
                if ($y !== '' && !is_numeric($y)) {
                    $error[] = 'Второе значение должно быть числом';
                }
                if (!in_array($operation, ["+", "-", "*", "/"], true)) {
                    $error[] = "Неверная операция";
                }


                if (empty($error)) {

                    $x = (float)$x;
                    $y = (float)$y;
                    $result = match ($operation) {
                        '+' =>
                        $x + $y,
                        '-' =>
                        $x - $y,
                        '*' =>
                        $x * $y,
                        '/' => ($y !== 0.0) ? $x / $y :  'Деление на ноль невозможно',
                        default => throw new Exception('Ошибка'),
                    };
                    if (is_numeric($result)) {
                        $result = round($result, 2);
                    }
                    $textResult = "$x $operation $y = $result";
                }

                if (isset($_GET['ajax'])) {
                    if (empty($error)) {
                        $result = [
                            'status' => 'success',
                            'data' => [
                                'result' => $result,
                                'textResult' => $textResult,
                            ]

                        ];
                    } else {
                        $result = [
                            'status' => 'error',
                            'error' => $error,

                        ];
                    }

                    header('Content-Type: application/json');
                    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    exit;
                }
            }
            echo render('calculator', [
                'titleSite' => $titleSite,
                'x' => $x,
                'y' => $y,
                'result' => $result,
                'textResult' => $textResult,
                'hasCalculationAttempt' => $hasCalculationAttempt,
                'error' => $error ?? null,
            ]);
            // include __DIR__ . '/../templates/calculator.php';
            break;
        default:
            redirectToError('404');
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
