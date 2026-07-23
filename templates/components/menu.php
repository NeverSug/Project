    <h1><a href="/" style="color: inherit; text-decoration: none;">Блог о PHP</a></h1>
    <nav>
        <div class="nav">
            <a href="/" style="color: inherit; text-decoration: none;">Главная</a>
            <a href="/?page=posts" style="color: inherit; text-decoration: none;">Посты</a>
            <a href="/?page=calculator" style="color: inherit; text-decoration: none;">Калькулятор</a>
        </div>
        <div class="nav">
            <?php if (!empty($_SESSION['user']['nickname'])): ?>
                <span class="stat"><?= htmlspecialchars($_SESSION['user']['nickname']) ?></span>
                <a href="/?page=logout" class="auth-link" title="Выйти из аккаунта">Выйти</a>
            <?php else: ?>
                <a href="/?page=login" class="auth-link" title="Войти в аккаунт">Вход</a>
                <a href="/?page=registrired" style="color: inherit; text-decoration: none;">Регистрация</a>
            <?php endif; ?>
        </div>
    </nav>
    <hr>