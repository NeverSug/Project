<h1>Регистрация</h1>
<?php if (!empty($success)): ?>
    <p class="result"><?= $success ?></p>
<?php endif; ?>

<form action="" method="post" class="post-create">
    <label for="email">
        Введите email:
        <input type="text" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
    </label>
    <?php if (!empty($errors['email'])): ?>
        <p class="error"><?= htmlspecialchars($errors['email']) ?></p>
    <?php endif; ?>
    <label for="nickname">
        Введите никнейм:
        <input type="text" name="nickname" value="<?= htmlspecialchars($nickname ?? '') ?>">
    </label>
    <?php if (!empty($errors['nickname'])): ?>
        <p class="error"><?= htmlspecialchars($errors['nickname'] ?? '') ?></p>
    <?php endif; ?>

    <label for="password">
        Введите пароль:
        <input name="password"><?= $password ?? '' ?>
    </label>
    <?php if (!empty($errors['password'])): ?>
        <p class="error"><?= htmlspecialchars($errors['password'] ?? '') ?></p>
    <?php endif; ?>
    <label for="passwordReplay">
        Повторите пароль:
        <input name="passwordReplay"><?= $passwordReplay ?? '' ?>
    </label>
    <?php if (!empty($errors['passwordReplay'])): ?>
        <p class="error"><?= htmlspecialchars($errors['passwordReplay'] ?? '') ?></p>
    <?php endif; ?>
    <button class="btn" type="submit">Зарегистрироваться</button>
</form>
<p><a href="/">← На главную</a></p>