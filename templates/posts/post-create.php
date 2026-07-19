<p><a href='/?page=posts'>Вернуться к постам</a></p>

<h1>Создание поста</h1>
<?php if (!empty($success)): ?>
    <p class="result"><?= $success ?></p>
<?php endif; ?>
<form action="" method="post" class="post-create" enctype="multipart/form-data">
    <h3>Категория</h3>

    <select name="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= htmlspecialchars(($category['id'] === $category_id) ? 'selected' : '') ?>value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <p><a href="/?page=categorycreate">+ Создать категорию</a></p>
    <label for="title">
        Заголовок поста:
        <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>">
    </label>
    <?php if (!empty($errors['title'])): ?>
        <p class="error"><?= htmlspecialchars($errors['title']) ?></p>
    <?php endif; ?>

    <label for="content">
        Текст поста:
        <textarea name="content"><?= htmlspecialchars($content ?? '') ?></textarea>
    </label>
    <?php if (!empty($errors['content'])): ?>
        <p class="error"><?= htmlspecialchars($errors['content']) ?></p>
    <?php endif; ?>
    <label for="image">
        <input type="file" name="image">
    </label>
    <?php if (!empty($errors['image'])): ?>
        <p class="error"><?= htmlspecialchars($errors['image']) ?></p>
    <?php endif; ?>
    <label for="author">
        Автор поста:
        <input type="text" name="author" value="<?= htmlspecialchars($author ?? '') ?>">
    </label>
    <?php if (!empty($errors['author'])): ?>
        <p class="error"><?= htmlspecialchars($errors['author']) ?></p>
    <?php endif; ?>
    <label for="date">
        Дата публикации:
        <input type="text" name="date" value="<?= htmlspecialchars($date ?? date('Y-m-d H:i:s')) ?>">
    </label>
    <?php if (!empty($errors['date'])): ?>
        <p class="error"><?= htmlspecialchars($errors['date']) ?></p>
    <?php endif; ?>
    <button class="btn" type="submit">Создать</button>
</form>
<p><a href="/">← На главную</a></p>