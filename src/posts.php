<?php

namespace App;

function deletePost(int $id): void
{
    $db = getDB();
    $stmt = $db->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        throw new \InvalidArgumentException('Некорректный ID поста');
    }
}

function updatePost(array $post, array $user): void
{
    if ($post['user_id'] !== $user['id']) {
        throw new \InvalidArgumentException('Вы не можете редактировать этот пост');
    }

    $db = getDB();

    if (empty($post['id']) || !is_numeric($post['id'])) {
        throw new \InvalidArgumentException('Некорректный ID поста');
    }

    $id = (int)$post['id'];

    $sql = 'UPDATE posts
            SET "title" = :title,
                "category_id" = :category_id,
                "content" = :content,
                "date" = :date,
                "author" = :author,
                "image" = :image
            WHERE id = :id';

    $stmt = $db->prepare($sql);

    $params = [
        ':title'      => $post['title'] ?? '',
        ':category_id' => $post['category_id'] ?? null,
        ':content'    => $post['content'] ?? '',
        ':date'       => $post['date'] ?? date('Y-m-d H:i:s'),
        ':author'     => $post['author'] ?? '',
        ':image'      => $post['image'] ?? null,
        ':id'         => $id,
    ];

    $stmt->execute($params);
    if ($stmt->rowCount() === 0) {
        throw new \RuntimeException('Пост с ID ' . $id . ' не найден');
    }
}

function savePost(array $newPost): int
{
    $db = getDB();

    $sql = 'INSERT INTO posts (category_id, title, content, date, author, image, user_id)
            VALUES (:category_id, :title, :content, :date, :author, :image, :user_id)';

    $stmt = $db->prepare($sql);
    $params = [
        ':category_id' => $newPost['category_id'] ?? null,
        ':title'      => $newPost['title'] ?? '',
        ':content'    => $newPost['content'] ?? '',
        ':date'       => $newPost['date'] ?? date('Y-m-d H:i:s'),
        ':author'     => $newPost['author'] ?? '',
        ':image'      => $newPost['image'] ?? null,
        ':user_id'      => $newPost['user_id'] ?? null,
    ];

    $stmt->execute($params);



    return (int)$db->lastInsertId();
}

function getPost(int $id, ?int $user_id = null): ?array
{
    $db = getDB();

    $stmt = $db->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt->execute([':id' => $id]);

    $post = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$post) {
        return null;
    }

    return $post;
}
function getPosts()
{
    $db = getDB();
    $stmt = $db->query("SELECT * FROM posts ORDER BY id DESC");
    $posts = $stmt->fetchAll();

    if (!$posts) {
        throw new \RuntimeException("Ошибка сервера");
    }

    return $posts;
}
