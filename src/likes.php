<?php

namespace App;

function enrichPostWithLikes(array $post): array
{
    $post['like_count'] = getLikeCount($post['id']);
    $post['liked']      = isLiked($post['id']);

    return $post;
}

function getCurrentUserIdOrNull(): ?int
{
    return !empty($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
}

function getGuestSessionId(): ?string
{
    return session_status() === PHP_SESSION_ACTIVE ? session_id() : null;
}

function toggleLike(int|string $postId): array
{
    $uid = getCurrentUserIdOrNull();
    $guestSession = $uid === null ? getGuestSessionId() : null;
    $pid = (int)$postId;

    if ($uid === null && $guestSession === null) {
        return [
            'status' => 'error',
            'message' => 'Не удалось определить пользователя или сессию',
            'liked' => false,
            'count' => getLikeCount($pid),
        ];
    }

    $db = getDb();

    if ($uid !== null) {
        $check = $db->prepare('SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?');
        $check->execute([$pid, $uid]);
    } else {
        $check = $db->prepare('SELECT 1 FROM likes WHERE post_id = ? AND guest_session_id = ?');
        $check->execute([$pid, $guestSession]);
    }
    $exists = (bool)$check->fetch();

    if ($exists) {
        if ($uid !== null) {
            $del = $db->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
            $del->execute([$pid, $uid]);
        } else {
            $del = $db->prepare('DELETE FROM likes WHERE post_id = ? AND guest_session_id = ?');
            $del->execute([$pid, $guestSession]);
        }
        $liked = false;
    } else {
        if ($uid !== null) {
            $ins = $db->prepare('INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (?, ?)');
            $ins->execute([$pid, $uid]);
        } else {
            $ins = $db->prepare(
                'INSERT OR IGNORE INTO likes (post_id, guest_session_id) VALUES (?, ?)'
            );
            $ins->execute([$pid, $guestSession]);
        }
        $liked = true;
    }

    return [
        'status' => 'success',
        'liked' => $liked,
        'count' => getLikeCount($pid),
    ];
}

function isLiked(int|string $postId): bool
{
    $uid = getCurrentUserIdOrNull();
    $guestSession = $uid === null ? getGuestSessionId() : null;
    $pid = (int)$postId;

    if ($uid === null && $guestSession === null) {
        return false;
    }

    $db = getDb();
    if ($uid !== null) {
        $stmt = $db->prepare('SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$pid, $uid]);
    } else {
        $stmt = $db->prepare('SELECT 1 FROM likes WHERE post_id = ? AND guest_session_id = ?');
        $stmt->execute([$pid, $guestSession]);
    }

    return (bool)$stmt->fetch();
}

function getLikeCount(int|string $postId): int
{
    $db = getDb();
    $stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM likes WHERE post_id = ?');
    $stmt->execute([(int)$postId]);
    $row = $stmt->fetch();
    return (int)($row['cnt'] ?? 0);
}
