<?php

namespace App\Controllers;

use function App\createUser;
use function App\nicknameExists;
use function App\emailExists;
use function App\render;
use function App\getCurrentUser;
use function App\authenticateUser;

function registriredControllers(): void
{
    $email = '';
    $nickname = '';
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = htmlspecialchars($_POST['email'] ?? '');
        $nickname = htmlspecialchars($_POST['nickname'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordReplay = $_POST['passwordReplay'] ?? '';
        if ($nickname === '') {
            $errors['nickname'] = 'Введите никнейм';
        }
        if (nicknameExists($nickname)) {
            $errors['nickname'] = 'Такой никнейм уже занят';
        }

        if ($email === '') {
            $errors['email'] = 'Введите email';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный email';
        }
        if (emailExists($email)) {
            $errors['email'] = 'Этот email уже зарегистрирован';
        }

        if ($password === '') {
            $errors['password'] = 'Введите пароль';
        }
        if (strlen($password) < 6) {
            $errors['password'] = 'Пароль должен быть не короче 6 символов';
        }
        if ($password !== $passwordReplay) {
            $errors['passwordReplay'] = 'Пароли не совпадают';
        }

        if (empty($errors)) {
            createUser($email, $nickname,  $password);
            header('Location: /?page=login&success=info');
            exit();
        }
    }
    echo render('registrired', [
        'nickname' => $nickname,
        'email' => $email,
        'errors' => $errors,
    ]);
}
function loginControllers(): void
{
    $errors = [];
    $success = '';
    $currentUser = getCurrentUser();
    if ($currentUser !== null) {
        header('Location: /');
        exit();
    }
    if (isset($_GET['success']) && $_GET['success'] === 'info') {
        $_SESSION['message'] = 'Регистрация успешна! Теперь вы можете войти.';
        $success = $_SESSION['message'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';


        $authenticated = authenticateUser($login, $password);
        if ($authenticated !== null) {
            $_SESSION['user'] = $authenticated;
            header('Location: /?page=posts&success=info');
            exit();
        }

        $errors[] = 'Неверный логин или пароль';
    }

    echo render('login', [
        'errors' => $errors,
        'success' => $success,
    ]);
}
function logoutControllers(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['user']);
    header('Location: /');
    exit();
}
