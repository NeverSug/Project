<?php

function saveCategory(string $name, string $slug, string $description): int
{
    $category = getCategories();
    $category[] = [
        'name' => $name,
        'slug' => $slug,
        'description' => $description,
    ];
    $lastKey = array_key_last($category);
    $category[$lastKey]['id'] = $lastKey;
    $category[$lastKey] = array_merge(['id' => $lastKey], $category[$lastKey]);
    $data = dirname(__DIR__) . '/data/categories.json';
    putContent($category, $data);

    return $lastKey;
}
function getPostsCategoriesBySlug(string $slug): array
{
    $category = getCategoryBySlug($slug);

    return getPostsCategoriesById($category['id']);
}

function getPostsCategoriesById(int $id): array
{
    $posts = getPosts();

    $filteredPosts = array_filter($posts, function ($post) use ($id) {
        return isset($post['category_id']) && $post['category_id'] === $id;
    });

    return array_values($filteredPosts);
}


function getCategoryBySlug(string $slug): array
{
    $categories = getCategories();

    $filtered = array_filter($categories, fn($cat) => $cat['slug'] === $slug);


    if (empty($filtered)) {
        throw new OutOfBoundsException("Категория с slug '{$slug}' не найдена");
    }

    return array_values($filtered)[0];
}

function getCategoryById(int $id): array
{
    $category = getCategories();

    if (!isset($category[$id])) {
        throw new OutOfBoundsException("Категория не найдена");
    }

    return $category[$id];
}

function getCategories()
{
    $categoriesData = readFileData('/data/categories.json');
    if (!$categoriesData) {
        throw new RuntimeException("Ошибка сервера");
    }
    return decodeData($categoriesData);
}
