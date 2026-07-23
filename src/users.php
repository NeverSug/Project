<?php

namespace App;


function requireLogin(?string $redirectTo = null): array
{
    $user = getCurrentUser();

    if ($user === null) {
        $redirect = $redirectTo ?? '/';
        header('Location: /login?redirect=' . urlencode($redirect));
        exit();
    }

    return $user;
}

function canManagePost(array $post): bool
{
    $user = getCurrentUser();

    return $user !== null
        && isset($post['user_id'])
        && $post['user_id'] !== null
        && (int)$post['user_id'] === (int)$user['id'];
}

function requirePostOwner(array $post): void
{
    if (!canManagePost($post)) {
        redirectToError(403);
    }
}

function authenticateUser(string $identifier, string $plainPassword): ?array
{
    $identifier = trim($identifier);

    $user = getUserByNickname($identifier);
    if ($user === null) {
        $user = getUserByEmail($identifier);
    }

    if ($user === null) {
        return null;
    }

    if (!password_verify($plainPassword, $user['password_hash'])) {
        return null;
    }

    return [
        'id' => (int)$user['id'],
        'nickname' => $user['nickname'],
        'email' => $user['email'],
        'created_at' => $user['created_at'] ?? null,
        'is_admin' => (bool)$user['is_admin'],
    ];
}

function getCurrentUser(): ?array
{
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    return null;
}

function emailExists(string $email): bool
{
    return getUserByEmail($email) !== null;
}

function nicknameExists(string $nickname): bool
{
    return getUserByNickname($nickname) !== null;
}

function getUserByEmail(string $email): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    return $user !== false ? $user : null;
}

function getUserByNickname(string $nickname): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM users WHERE nickname = ? LIMIT 1');
    $stmt->execute([$nickname]);
    $user = $stmt->fetch();

    return $user !== false ? $user : null;
}

function createUser(string $email, string $nickname,  string $password): int
{
    $db = getDb();
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare(
        'INSERT INTO users (email, nickname, password_hash) VALUES (?, ?, ?)'
    );
    $success = $stmt->execute([$email, $nickname,  $hash]);

    if (!$success) {
        throw new \Exception('Не удалось создать пользователя');
    }

    return (int)$db->lastInsertId();
}
