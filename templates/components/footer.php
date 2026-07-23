<div id="cookie-banner" class="cookie-banner">
    <span class="cookie-text">
        Мы используем файлы cookie, чтобы сделать сайт удобнее. Продолжая пользоваться сайтом, вы соглашаетесь с нашей Политикой конфиденциальности.
    </span>
    <button id="cookie-accept" class="cookie-btn">Понятно</button>
</div>
<hr>
<footer>&copy; <?= date('Y') ?> Блог о PHP</footer>
<script>
    (function() {
        const banner = document.getElementById('cookie-banner');
        const btn = document.getElementById('cookie-accept');

        if (localStorage.getItem('cookies-accepted') === '1') {
            banner.style.display = 'none';
            return;
        }

        btn.addEventListener('click', () => {
            localStorage.setItem('cookies-accepted', '1');
            banner.style.opacity = '0';
            setTimeout(() => {
                banner.style.display = 'none';
            }, 300);
        });
    })();
</script>