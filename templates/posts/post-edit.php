<p><a href='/?page=posts'>Вернуться к постам</a></p>

<h1>Изменение поста</h1>

<form action="/?page=post-edit&action=save" method="post" class="post-create" enctype="multipart/form-data">
    <input type="text" name="id" readonly hidden value="<?= htmlspecialchars($post['id'] ?? $id ?? '') ?>">
    <h3>Категория</h3>
    <select name="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= htmlspecialchars(($category['id'] === ($post['category_id'] ?? $category_id)) ? 'selected' : '') ?> value="<?= htmlspecialchars($category['id']) ?>"> <?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="title">
        Заголовок поста:
        <input type="text" name="title" value="<?= htmlspecialchars($post['title'] ?? $title ?? '') ?>">
    </label>
    <?php if (!empty($errors['title'])): ?>
        <p class="error"><?= htmlspecialchars($errors['title']) ?></p>
    <?php endif; ?>

    <label for="content">
        Текст поста:
        <textarea name="content"><?= htmlspecialchars($post['content'] ?? $content ?? '') ?></textarea>
    </label>
    <?php if (!empty($errors['content'])): ?>
        <p class="error"><?= htmlspecialchars($errors['content']) ?></p>
    <?php endif; ?>
    <div>
        <input type="text" id="image-input-<?= $post['id'] ?? '' ?>" readonly value="<?= htmlspecialchars($post['image'] ?? '') ?>"> <button type="button" data-id="<?= $post['id'] ?? '' ?>" class="clearImg">Удалить</button>
    </div>
    <label for="image">
        Загрузить новую картинку:
        <input type="file" name="image">
    </label>
    <?php if (!empty($errors['image'])): ?>
        <p class="error"><?= htmlspecialchars($errors['image']) ?></p>
    <?php endif; ?>
    <button class="btn" type="submit">Изменить</button>
</form>
<p><a href="/">← На главную</a></p>
<script>
    window.onload = function() {
        document.querySelectorAll('.clearImg').forEach(button => {
            button.onclick = function() {
                const id = this.getAttribute('data-id');
                (
                    async () => {
                        try {
                            const response = await fetch(`/?page=post-edit&action=delete_image&id=${id}&ajax`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            const result = await response.json();
                            switch (result.status) {
                                case 'success':
                                    document.getElementById('image-input-' + id).value = '';
                                    break;
                                case 'error':
                                    console.error('Ошибка: Невозможно удалить этот файл');
                                    break;
                                default:
                                    console.error('Ошибка: неверный формат ответа');
                            }

                        } catch (error) {
                            console.log('Ошибка:', error)
                        }
                    }
                )()

            }

        })
    }
</script>