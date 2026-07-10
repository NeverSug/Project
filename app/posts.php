<?php
function deletePost(array $posts, int $id)
{
    unset($posts[$id]);
    $data = dirname(__DIR__) . '/data/posts.json';
    putContent($posts, $data);
}

function savePost(int $category_id, string $title, string $content): int
{
    $posts = getPosts();
    $posts[] = [
        'category_id' => $category_id,
        'title' => $title,
        'content' => $content,
        'date' => date('Y-m-d H:i:s'),
        'author' => '',
    ];
    $lastKey = array_key_last($posts);
    $posts[$lastKey]['id'] = $lastKey;
    $posts[$lastKey] = array_merge(['id' => $lastKey], $posts[$lastKey]);
    uasort($posts, function ($a, $b) {
        return $b['id'] <=> $a['id'];
    });
    $firstPost = reset($posts);
    $newId = $firstPost['id'];
    $data = dirname(__DIR__) . '/data/posts.json';
    putContent($posts, $data);

    return $newId;
}

function getPost(int $id): array
{
    $posts = getPosts();
    if (!isset($posts[$id])) {
        throw new OutOfBoundsException("Пост не найден");
    }

    return $posts[$id];
}
function getPosts(): array
{

    $postsData = readFileData('/data/posts.json');

    if (!$postsData) {
        throw new RuntimeException("Ошибка сервера");
    }

    return decodeData($postsData);
}
