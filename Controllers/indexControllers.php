<?php

namespace App\Controllers;

use function App\getCategories;
use function App\getPosts;
use function App\render;
use function App\renderTemplate;

function indexControllers(): void
{
    $titleSite = "Главная";
    $categories = getCategories();
    $posts = getPosts();
    echo render('index', [
        'title' => $titleSite,
        'categories' => $categories,
        'posts' => $posts,
    ]);
}
function errorHendlerControllers(): void
{
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
    echo renderTemplate('errorhendler', [
        'errorCode' => $errorCode,
        'config' => $config,
        'errorId' => $errorId,
    ]);
}
