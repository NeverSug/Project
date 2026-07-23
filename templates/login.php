<h1>Вход</h1>
<?php if (!empty($success)): ?>
    <p class="result"><?= $success ?? '' ?></p>
<?php endif; ?>

<form action="" method="post" class="post-create">
    <label for="login">
        Введите email или никнейм:
        <input type="text" name="login" placeholder="admin@yandex.ru" value="<?= htmlspecialchars($email ?? $nickname ?? '') ?>">
    </label>
    <?php if (!empty($errors['email'])): ?>
        <p class="error"><?= htmlspecialchars($errors['email']) ?></p>
    <?php endif; ?>

    <label for="password">
        Введите пароль:
        <input name="password" placeholder="123456789"><?= $password ?? '' ?>
    </label>
    <?php if (!empty($errors['password'])): ?>
        <p class="error"><?= htmlspecialchars($errors['password'] ?? '') ?></p>
    <?php endif; ?>

    <button class="btn" type="submit">Войти</button>
</form>
<p><a href="/?page=registrired">Зарегистрироваться</a></p>