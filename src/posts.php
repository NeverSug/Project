<?php
function deletePost(int $id)
{
    $posts = getPosts();
    unset($posts[$id]);
    $data = dirname(__DIR__) . '/data/posts.json';
    writeFileData($data, $posts);
}

function updatePost(array $post): void
{
    $id = $post['id'];
    $posts = getPosts();
    $posts[$id] = [
        ...$post,
        ...[
            'date'      => $posts[$id]['date'] ?? date('Y-m-d H:i:s'),
            'author'    => $posts[$id]['author'] ?? 'Guest',
            'like' => $posts[$id]['like']
        ]
    ];

    $data = dirname(__DIR__) . '/data/posts.json';
    writeFileData($data, $posts);
}
function savePost(array $newPost): int
{
    $posts = getPosts();
    $postToSave = [
        'category_id' => $newPost['category_id'] ?? null,
        'title'     => $newPost['title'] ?? '',
        'content'   => $newPost['content'] ?? '',
        'date'      => $newPost['date'] ?? date('Y-m-d H:i:s'),
        'author'    => $newPost['author'] ?? '',
        'image' => $newPost['image'] ?? null
    ];
    $posts[] = $postToSave;
    $lastKey = array_key_last($posts);
    $posts[$lastKey]['id'] = $lastKey;
    $posts[$lastKey] = array_merge(['id' => $lastKey], $posts[$lastKey]);
    uasort($posts, function ($a, $b) {
        return $b['id'] <=> $a['id'];
    });
    $firstPost = reset($posts);
    $newId = $firstPost['id'];
    $data = dirname(__DIR__) . '/data/posts.json';
    writeFileData($data, $posts);

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
