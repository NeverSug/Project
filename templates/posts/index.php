<p><a href="?page=post-create">+ Создать пост</a></p>
<div id="messages"></div>
<?php if (!empty($success)): ?>
    <p class="result"><?= $success ?></p>
<?php endif; ?>

<?php if ($error === ''): ?>
    <?php if (empty($filtered)): ?>
        <p>Нет постов.</p>
    <?php else: ?>
        <?php foreach ($filtered as $post): ?>
            <div class="post-preview" id="<?= $post['id'] ?>">
                <div class="post-title">
                    <h2><a href="/?page=post&id=<?= htmlspecialchars($post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <div>
                        <a href="/?page=post-edit&action=edit&id=<?= $post['id'] ?>">Редактировать</a> |
                        <a href="/?page=posts&action=delete&id=<?= $post['id'] ?>">Удалить</a>
                        <button type="button" data-id="<?= $post['id'] ?>" class="btn deleteBtn">Удалить</button>
                    </div>
                </div>
                <p><strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?> | <?= htmlspecialchars($post['date']) ?></p>
                <div class="post-title">
                    <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                    <button data-id="<?= htmlspecialchars($post['id'] ?? 0) ?>" class="like" type="button" value="<?= htmlspecialchars($post['like'] ?? 0) ?>">💗<?= htmlspecialchars($post['like'] ?? 0) ?></button>
                    <!-- <a href="/posts.php?action=like&id=<?= $post['id'] ?>">💗<?= htmlspecialchars($post['like'] ?? 0) ?></a> -->
                </div>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php else: ?>
    <?= htmlspecialchars($error) ?>
<?php endif; ?>

<p><a href="/">← На главную</a></p>

<script>
    window.onload = function() {
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.onclick = function() {
                const id = this.getAttribute('data-id');
                const messagesBox = document.getElementById('messages');
                (
                    async () => {
                        try {
                            const response = await fetch(`?page=posts&action=delete&id=${id}&ajax`);
                            const result = await response.json();
                            switch (result.status) {
                                case 'success':
                                    document.getElementById(id).remove();
                                    messagesBox.innerHTML = '';
                                    const resultText = document.createElement('p');
                                    resultText.innerText = result.result;
                                    resultText.classList.add('result');
                                    messagesBox.appendChild(resultText);
                                    break;
                                case 'error':
                                    console.error('Ошибка: Невозможно удалить этот пост');
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
    }
</script>