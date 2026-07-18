<h2>Категории</h2>
<?php if (isset($categories)): ?>
    <ul>
        <?php foreach ($categories as $cat): ?>
            <li title="<?= htmlspecialchars($cat['description']) ?>">
                <a href="/?page=posts&category=<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Категории пока не добавлены.</p>
<?php endif; ?>

<p><a href="/?page=posts">Все посты</a></p>