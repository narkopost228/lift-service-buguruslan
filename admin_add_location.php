<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'админ') {
    die('Доступ запрещен.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (!empty($name)) {
        $stmt_check = $connect->prepare("SELECT ID FROM `Места` WHERE `Название` = ?");
        $stmt_check->bind_param("s", $name);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $message = '<div style="color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin-bottom: 15px;">Ошибка: Лифт с таким названием уже существует в системе!</div>';
        } else {
            $stmt = $connect->prepare("INSERT INTO `Места` (`Название`, `Описание`) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            
            if ($stmt->execute()) {
                $message = '<div style="color: green; padding: 10px; background: #d4edda; border-radius: 5px; margin-bottom: 15px;">Лифт успешно добавлен!</div>';
            } else {
                $message = '<div style="color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin-bottom: 15px;">Ошибка базы данных: ' . $connect->error . '</div>';
            }
        }
    } else {
        $message = '<div style="color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin-bottom: 15px;">Заполните название!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить лифт</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Добавление нового лифта</h1>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="admin_panel.php">← В панель админа</a>
        </div>
    </div>

    <div class="container" style="max-width: 600px;">
        <div class="card">
            <?= $message ?>
            <form method="POST">
                <label>Название лифта (Место)</label>
                <input type="text" name="name" required placeholder="Например: Главный корпус, Лифт №6">

                <label>Описание (какие этажи/отделения обслуживает)</label>
                <textarea name="description" rows="3" placeholder="Например: Для персонала, 1-5 этажи"></textarea>

                <button type="submit" class="btn btn-main" style="width: 100%;">Добавить в справочник</button>
            </form>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>