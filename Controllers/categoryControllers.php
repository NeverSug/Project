<?php

namespace App\Controllers;

use function App\render;
use function App\saveCategory;

function categorycreateControllers(): void
{
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

            saveCategory([
                'name' => $name,
                'slug' => $slug,
                'description' => $description
            ]);

            header("Location: /?page=postcreate&success=okcat");
            die();
        }
    }
    echo render('category-create', [
        'titleSite' => $titleSite,
        'errors' => $errors ?? null,
        'name' => $name ?? '',
        'slug' => $slug ?? '',
        'description' => $description ?? '',
    ]);
}
