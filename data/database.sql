--удаление таблиц
DROP TABLE IF EXISTS 'posts';
DROP TABLE IF EXISTS 'categories';
DROP TABLE IF EXISTS 'users';
DROP TABLE IF EXISTS 'likes';

--создание таблиц
CREATE TABLE IF NOT EXISTS "categories" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "name" VARCHAR NOT NULL UNIQUE,
    "slug" VARCHAR NOT NULL UNIQUE,
    "description" TEXT
);

CREATE TABLE IF NOT EXISTS "posts"
(
    "id"          INTEGER NOT NULL,
    "title"       VARCHAR NOT NULL,
    "category_id" INTEGER,
    "content"     TEXT,
    "date"        VARCHAR,
    "author"      VARCHAR,
    "image"       VARCHAR,
    "user_id"     INTEGER,
    PRIMARY KEY ("id"),
    FOREIGN KEY ("category_id") REFERENCES "categories" ("id"),
    FOREIGN KEY ("user_id") REFERENCES "users" ("id")
);

CREATE TABLE IF NOT EXISTS "likes"
(
    "post_id" INTEGER NOT NULL,
    "user_id" VARCHAR NOT NULL,
    PRIMARY KEY ("post_id", "user_id")
);

CREATE TABLE IF NOT EXISTS "users"
(
    "id"            INTEGER NOT NULL,
    "nickname"      VARCHAR NOT NULL UNIQUE,
    "email"         VARCHAR NOT NULL UNIQUE,
    "password_hash" TEXT NOT NULL,
    "created_at"    DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

--заполнение данными таблицы  categories
INSERT INTO categories
(id, name, slug, description)
VALUES(1, 'PHP разработка', 'php', 'Статьи о PHP, фреймворках и бэкенд разработке');
INSERT INTO categories
(id, name, slug, description)
VALUES(2, 'Frontend', 'frontend', 'HTML, CSS, JavaScript и современные фронтенд технологии');
INSERT INTO categories
(id, name, slug, description)
VALUES(3, 'Базы данных', 'database', 'SQL, MySQL, PostgreSQL и работа с данными');
INSERT INTO categories
(id, name, slug, description)
VALUES(4, 'Безопасность', 'security', 'Защита веб-приложений и лучшие практики безопасности');

--заполнение данными таблицы  posts
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(2, 'Безопасность веб-приложений', 4, 'Защита от XSS, CSRF и SQL-инъекций критически важна. Всегда экранируйте вывод, используйте подготовленные запросы и валидируйте пользовательский ввод.55', '2024-01-22', 'Ольга', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(3, 'Современный CSS: Flexbox и Grid', 2, 'Flexbox и Grid - мощные инструменты для создания адаптивных макетов. Flexbox хорош для одномерной верстки, а Grid для двухмерной. Освойте их для профессиональной верстки.', '2024-01-21', 'Игорь', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(4, 'JavaScript: оживление веб-страниц', 3, 'JavaScript делает сайты интерактивными. С его помощью можно обрабатывать клики пользователей, отправлять запросы на сервер без перезагрузки страницы и создавать анимации.', '2024-01-20', 'Анна', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(5, 'Базы данных и SQL: первые шаги', 3, 'Базы данных необходимы для хранения информации в веб-приложениях. SQL позволяет создавать, читать, обновлять и удалять данные. Начните с простых SELECT запросов.', '2024-01-19', 'Дмитрий', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(6, 'Основы HTML и CSS для начинающих', 2, 'HTML и CSS - это фундамент веб-разработки. HTML отвечает за структуру страницы, а CSS за ее внешний вид. Вместе они создают красивые и функциональные сайты.', '2024-01-18', 'Елена', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(7, 'Обработка ошибок в PHP', 2, 'Правильная обработка ошибок критически важна для создания надежных приложений. Используйте try-catch блоки и проверяйте результаты функций.', '2024-01-17', 'Петя', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(8, 'Работа с JSON в PHP', 1, 'JSON - отличный формат для хранения и обмена данными. PHP предоставляет функции json_encode и json_decode для работы с ним.', '2024-01-16', 'Мария', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(9, 'Введение в PHP', 1, 'PHP - это язык программирования, который широко используется для веб-разработки. Он прост в изучении и мощный в использовании.', '2024-01-15', 'Алексей', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(10, 'Обработка ошибок в PHP', 4, 'Правильная обработка ошибок критически важна для создания надежных приложений. Используйте try-catch блоки и проверяйте результаты функций.', '2024-01-17', 'Иван', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(11, 'Работа с JSON в PHP', 3, 'JSON - отличный формат для хранения и обмена данными. PHP предоставляет функции json_encode и json_decode для работы с ним.', '2024-01-16', 'Мария', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(12, 'Введение в PHP', 2, 'PHP - это язык программирования, который широко используется для веб-разработки. Он прост в изучении и мощный в использовании.', '2024-01-15', 'Алексей', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(13, 'Обработка ошибок в PHP', 1, 'Правильная обработка ошибок критически важна для создания надежных приложений. Используйте try-catch блоки и проверяйте результаты функций.', '2024-01-17', 'Иван', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(14, 'Работа с JSON в PHP', 4, 'JSON - отличный формат для хранения и обмена данными. PHP предоставляет функции json_encode и json_decode для работы с ним.', '2024-01-16', 'Мария', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(15, 'Введение в PHP', 3, 'PHP - это язык программирования, который широко используется для веб-разработки. Он прост в изучении и мощный в использовании.', '2024-01-15', 'Алексей', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(16, 'Обработка ошибок в PHP', 2, 'Правильная обработка ошибок критически важна для создания надежных приложений. Используйте try-catch блоки и проверяйте результаты функций.', '2024-01-17', 'Иван', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(17, 'Работа с JSON в PHP', 1, 'JSON - отличный формат для хранения и обмена данными. PHP предоставляет функции json_encode и json_decode для работы с ним.', '2024-01-16', 'Мария', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(18, 'Введение в PHP', 3, 'PHP - это язык программирования, который широко используется для веб-разработки. Он прост в изучении и мощный в использовании.', '2024-01-15', 'Алексей', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(19, 'Работа с JSON', 1, 'JSON - отличный формат для хранения и обмена данными. PHP предоставляет функции json_encode и json_decode для работы с ним.', '2024-01-16', 'Мария', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(20, 'Обработка ошибок в PHP', 2, 'Правильная обработка ошибок критически важна для создания надежных приложений. Используйте try-catch блоки и проверяйте результаты функций.', '2024-01-17', 'Иван', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(21, 'Работа с JSON в PHP', 1, 'JSON - отличный формат для хранения и обмена данными. PHP предоставляет функции json_encode и json_decode для работы с ним.', '2024-01-16', 'Мария', NULL);
INSERT INTO posts
(id, title, category_id, content, date, author, image)
VALUES(22, 'Обработка ошибок в PHP', 3, 'Правильная обработка ошибок критически важна для создания надежных приложений. Используйте try-catch блоки и проверяйте результаты функций.', '2024-01-17', 'Иван', NULL);
