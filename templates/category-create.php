<p><a href='/?page=post-create'>Вернуться к созданию поста</a></p>

<h1>Создание категории</h1>

<form action="" method="post" class="post-create">
    <label for="title">
        Название категории:
        <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>">
    </label>
    <?php if (!empty($errors['name'])): ?>
        <p class="error"><?= htmlspecialchars($errors['name']) ?></p>
    <?php endif; ?>
    <label for="slug">
        Тема:
        <input type="text" name="slug" value="<?= htmlspecialchars($slug ?? '') ?>">
    </label>
    <?php if (!empty($errors['slug'])): ?>
        <p class="error"><?= htmlspecialchars($errors['slug']) ?></p>
    <?php endif; ?>

    <label for="description">
        Описание:
        <textarea name="description"><?= htmlspecialchars($description ?? '') ?></textarea>
    </label>
    <?php if (!empty($errors['description'])): ?>
        <p class="error"><?= htmlspecialchars($errors['description']) ?></p>
    <?php endif; ?>
    <button class="btn" type="submit">Создать</button>
</form>
<p><a href="/">← На главную</a></p>