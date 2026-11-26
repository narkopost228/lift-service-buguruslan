<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'админ') {
    die('Доступ только для администратора.');
}

if (isset($_GET['delete_location_id'])) {
    $del_id = intval($_GET['delete_location_id']);
    mysqli_query($connect, "DELETE FROM `Места` WHERE `ID` = $del_id");
    header('Location: admin_panel.php');
    exit;
}

$users = mysqli_query($connect, "SELECT * FROM `Пользователи` ORDER BY `Роль`, `ФИО`");
$locations = mysqli_query($connect, "SELECT * FROM `Места` ORDER BY `Название`");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель админа</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Панель администратора</h1>
        <div>
            <a href="admin_all_requests.php" class="btn-top">Все заявки</a>
            <a href="create_request.php" class="btn-top">Создать заявку</a>
            <a href="admin_add_location.php" class="btn-top">+ Добавить лифт</a>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="dashboard.php" style="margin-left: 15px;">← На главную</a>
        </div>
    </div>

    <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="card">
            <h2>Пользователи системы</h2>
            <table>
                <tr><th>ФИО</th><th>Логин</th><th>Роль</th></tr>
                <?php while($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= htmlspecialchars($u['ФИО']) ?></td>
                    <td><?= htmlspecialchars($u['Логин']) ?></td>
                    <td>
                        <?php 
                            $role_class = 'role-applicant';
                            if($u['Роль'] == 'админ') $role_class = 'role-admin';
                            if($u['Роль'] == 'исполнитель') $role_class = 'role-executor';
                        ?>
                        <span class="<?= $role_class ?>"><?= $u['Роль'] ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="card">
            <h2>Справочник лифтов (Мест)</h2>
            <table>
                <tr><th>Название</th><th>Описание</th><th>Действие</th></tr>
                <?php while($loc = mysqli_fetch_assoc($locations)): ?>
                <tr>
                    <td><?= htmlspecialchars($loc['Название']) ?></td>
                    <td><?= htmlspecialchars($loc['Описание']) ?></td>
                    <td>
                        <a href="admin_panel.php?delete_location_id=<?= $loc['ID'] ?>" class="btn-del" onclick="return confirm('Удалить этот лифт? Все связанные заявки также будут удалены!')">Удалить</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>