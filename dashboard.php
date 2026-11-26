<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Делаем роли более человечными для отображения
$role_display = 'Сотрудник';
if ($_SESSION['user_role'] == 'админ') $role_display = 'Администратор';
if ($_SESSION['user_role'] == 'исполнитель') $role_display = 'Механик';
if ($_SESSION['user_role'] == 'заявитель') $role_display = 'Сотрудник';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная - Лифты ГБУЗ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Система управления лифтами ГБУЗ</h1>
        <div>
            <span>Вы вошли как: <strong><?= $_SESSION['user_name'] ?></strong> (<?= $role_display ?>)</span>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="logout.php">Выйти</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h2>Добро пожаловать в систему!</h2>
            <p>Здесь вы можете управлять заявками на обслуживание и ремонт лифтового оборудования.</p>
        </div>

        <!-- Блок экстренной помощи (для заявителя и админа) -->
        <?php if ($_SESSION['user_role'] == 'заявитель' || $_SESSION['user_role'] == 'админ'): ?>
        <div style="background-color: #f8d7da; border: 2px solid #e74c3c; color: #721c24; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 30px; margin-right: 20px;">🚨</div>
            <div>
                <h3 style="margin: 0 0 5px 0; color: #c0392b;">Внимание! Экстренная ситуация!</h3>
                <p style="margin: 0; font-size: 15px;">
                    Если в лифте застряли люди, не заполняйте заявку — <strong>немедленно звоните!</strong><br>
                    Единый номер: <strong style="font-size: 18px;">112</strong> 
                    | Приемное отделение: <strong>8 (35352) 2-47-37</strong>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Блок техники безопасности (для механика) -->
        <?php if ($_SESSION['user_role'] == 'исполнитель'): ?>
        <div style="background-color: #fff3cd; border: 2px solid #ffc107; color: #856404; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="font-size: 30px; margin-right: 20px;">⚠️</div>
            <div>
                <h3 style="margin: 0 0 5px 0; color: #856404;">Техника безопасности</h3>
                <p style="margin: 0; font-size: 15px;">
                    Перед началом ремонта обязательно <strong>обесточьте лифт</strong> и убедитесь, что в кабине нет пассажиров.
                </p>
            </div>
        </div>
        <?php endif; ?>

        <div class="menu-grid">
            <!-- ИНТЕРФЕЙС ЗАЯВИТЕЛЯ -->
            <?php if ($_SESSION['user_role'] == 'заявитель'): ?>
                <div class="menu-card">
                    <h3>Новая заявка</h3>
                    <p>Сообщить о неисправности лифта или запросить ТО</p>
                    <a href="create_request.php" class="btn btn-main">Создать заявку</a>
                </div>
                <div class="menu-card">
                    <h3>Мои заявки</h3>
                    <p>Посмотреть статус ваших обращений</p>
                    <a href="my_requests.php" class="btn btn-main">Мои заявки</a>
                </div>
            <?php endif; ?>

            <!-- ИНТЕРФЕЙС МЕХАНИКА -->
            <?php if ($_SESSION['user_role'] == 'исполнитель'): ?>
                <div class="menu-card">
                    <h3>Новые заявки</h3>
                    <p>Заявки, которые еще никто не взял в работу</p>
                    <a href="pool.php" class="btn btn-main">Перейти к заявкам</a>
                </div>
                <div class="menu-card">
                    <h3>Мои задачи</h3>
                    <p>Заявки, которые вы взяли в работу</p>
                    <a href="my_tasks.php" class="btn btn-main">Мои задачи</a>
                </div>
            <?php endif; ?>

            <!-- ИНТЕРФЕЙС АДМИНА -->
            <?php if ($_SESSION['user_role'] == 'админ'): ?>
                <div class="menu-card" style="grid-column: 1 / -1; max-width: 500px; margin: 0 auto;">
                    <h3>Панель управления</h3>
                    <p>Полный доступ ко всем заявкам, пользователям и справочникам системы</p>
                    <a href="admin_panel.php" class="btn btn-main" style="font-size: 18px; padding: 15px 40px;">Войти в панель админа</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>