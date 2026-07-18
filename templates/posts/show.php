<!DOCTYPE html>
<p><a href='/?page=posts'>Вернуться к постам</a></p>

<?php if (!empty($success)): ?>
    <p class="result"><?= $success ?></p>
<?php endif; ?>

<?php if (!$error): ?>
    <article class="single-post">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p><strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?></p>
        <p><strong>Дата:</strong> <?= htmlspecialchars($post['date']) ?></p>
        <p><strong>Категория:</strong> <?= htmlspecialchars($categories[$post['category_id']]['name']) ?></p>
        <div class="content">
            <div class="img">
                <?php if (!empty($post['image'])): ?>
                    <img src="/upload/<?= htmlspecialchars($post['image'] ?? '') ?>" alt="<?= htmlspecialchars($post['image'] ?? '') ?>">
                <?php endif; ?>
            </div>
            <p><?= htmlspecialchars($post['content']) ?></p>
        </div>
    </article>

    <p><a href="/?page=posts">← Все посты</a></p>
<?php else: ?>
    <?php include dirname(__DIR__) . '../components/menu.php'; ?>
    <?= htmlspecialchars($error) ?>
    <p><a href="/?page=posts">← Все посты</a></p>
<?php endif; ?>
<p><a href="/">← На главную</a></p>