<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM `Пользователи` WHERE `Логин` = '$login' AND `Активен` = 1";
    $result = mysqli_query($connect, $query);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if ($password == $user['Пароль']) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['user_login'] = $user['Логин'];
            $_SESSION['user_role'] = $user['Роль'];
            $_SESSION['user_name'] = $user['ФИО'];
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Неверный пароль';
        }
    } else {
        $error = 'Пользователь не найден или заблокирован';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему - Лифты ГБУЗ</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { width: 320px; text-align: center; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Вход в систему</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" class="btn btn-main" style="width: 100%;">Войти</button>
        </form>
        <button class="theme-toggle" onclick="toggleTheme()" style="margin-top: 20px; border-color: var(--text-muted); color: var(--text-muted);">🌓 Сменить тему</button>
    </div>
    <script src="theme.js"></script>
</body>
</html>