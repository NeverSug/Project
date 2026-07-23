<?php

namespace App;

function saveCategory(array $newCategory): int
{
    $db = getDB();

    $sql = 'INSERT INTO categories (name, slug, description)
            VALUES (:name, :slug, :description)';

    $stmt = $db->prepare($sql);

    $params = [
        ':name' => $newCategory['name'] ?? '',
        ':slug'      => $newCategory['slug'] ?? '',
        ':description'    => $newCategory['description'] ?? '',
    ];

    $stmt->execute($params);


    return (int)$db->lastInsertId();
}
function getPostsCategoriesBySlug(string $slug): array
{
    $category = getCategoryBySlug($slug);

    return getPostsCategoriesById($category['id']);
}

function getPostsCategoriesById(int $id): array
{

    $db = getDB();

    $sql = 'SELECT * FROM posts WHERE category_id = :category_id';
    $stmt = $db->prepare($sql);
    $stmt->execute([':category_id' => $id]);

    return $stmt->fetchAll();
}


function getCategoryBySlug(string $slug): array
{
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM categories WHERE slug = :slug');
    $stmt->execute([':slug' => $slug]);
    $category = $stmt->fetch();


    if (!$category) {
        throw new \OutOfBoundsException("Категория с slug '{$slug}' не найдена");
    }

    return $category;
}

function getCategoryById(int $id): ?array
{
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $category = $stmt->fetch();

    if (isset($category)) {
        throw new \OutOfBoundsException("Категория не найдена");
    }

    return $category;
}

function getCategories()
{
    $db = getDB();
    $stmt = $db->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
    if (!$categories) {
        throw new \RuntimeException("Ошибка сервера");
    }
    return $categories;
}
