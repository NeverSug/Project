<!DOCTYPE html>
<p><a href='/?page=posts'>Вернуться к постам</a></p>

<?php if (!empty($success)): ?>
    <p class="result"><?= $success ?></p>
<?php endif; ?>

<?php if (!$error): ?>
    <article class="single-post">
        <div class="show">
            <div class="contentText">
                <div>

                    <h1><?= htmlspecialchars($post['title']) ?></h1>
                    <p><strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?></p>
                    <p><strong>Дата:</strong> <?= htmlspecialchars($post['date']) ?></p>
                    <p><strong>Категория:</strong> <?= htmlspecialchars($categories[$post['category_id']]['name']) ?></p>
                </div>
                <div>
                    <div class="content">

                        <div class="img">
                            <?php if (!empty($post['image'])): ?>
                                <img src="/upload/<?= htmlspecialchars($post['image'] ?? '') ?>" alt="<?= htmlspecialchars($post['image'] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                        <p><?= htmlspecialchars($post['content']) ?></p>
                    </div>
                    <div class="contentLike">

                        <button data-id="<?= htmlspecialchars($post['id'] ?? 0) ?>" class="like" type="button" value="<?= htmlspecialchars($post['like'] ?? 0) ?>">💗<?= htmlspecialchars($post['like'] ?? 0) ?></button>
                        <div id="messages"></div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <p><a href="/?page=posts">← Все посты</a></p>
<?php else: ?>
    <?php include dirname(__DIR__) . '../components/menu.php'; ?>
    <?= htmlspecialchars($error) ?>
    <p><a href="/?page=posts">← Все посты</a></p>
<?php endif; ?>
<p><a href="/">← На главную</a></p>
<script>
    document.querySelectorAll('.like').forEach(button => {
        button.onclick = function() {
            const id = this.getAttribute('data-id');
            const messagesBox = document.getElementById('messages');
            (
                async () => {
                    try {
                        const response = await fetch(`?page=posts&action=like&id=${id}&ajax`);
                        const result = await response.json();
                        switch (result.status) {
                            case 'success':
                                const count = button.value;
                                button.textContent = `💗 ${result.count}`;
                                button.classList.add('like-button');
                                setTimeout(() => button.classList.remove('like-button'), 1500);
                                messagesBox.innerHTML = '';
                                const resultText = document.createElement('p');
                                resultText.innerText = result.result;
                                resultText.classList.add('result');
                                messagesBox.appendChild(resultText);
                                break;
                            case 'error':
                                console.error('Ошибка: Невозможно оценить этот пост');
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
</script>