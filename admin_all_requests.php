<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'админ') {
    die('Доступ запрещен.');
}

// Проверяем фильтр (по умолчанию - все заявки)
$filter_sql = "";
if (isset($_GET['filter']) && $_GET['filter'] == 'done') {
    $filter_sql = "WHERE z.Статус = 'выполнено'";
}

// Получаем заявки с учетом фильтра
$query = "SELECT z.*, m.Название AS Место, u1.ФИО AS Заявитель, u2.ФИО AS Механик 
          FROM `Заявки` z 
          JOIN `Места` m ON z.ID_места = m.ID 
          JOIN `Пользователи` u1 ON z.ID_заявителя = u1.ID 
          LEFT JOIN `Пользователи` u2 ON z.ID_исполнителя = u2.ID
          $filter_sql
          ORDER BY z.Дата_создания DESC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Все заявки - Админ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Мониторинг заявок</h1>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="admin_panel.php">← В панель админа</a>
        </div>
    </div>

    <div class="container">
        <!-- Вкладки: Все / Архив -->
        <div class="tabs">
            <a href="admin_all_requests.php" class="tab <?= !isset($_GET['filter']) ? 'active' : '' ?>">Все заявки</a>
            <a href="admin_all_requests.php?filter=done" class="tab <?= (isset($_GET['filter']) && $_GET['filter'] == 'done') ? 'active' : '' ?>">Выполненные (Архив)</a>
        </div>

        <div class="card">
            <?php if (mysqli_num_rows($result) == 0): ?>
                <p>Заявок не найдено.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <!-- Колонка ID убрана -->
                            <th>Лифт</th>
                            <th>Описание</th>
                            <th>Заявитель</th>
                            <th>Исполнитель</th>
                            <th>Дата</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <!-- Вывод ID убран -->
                            <td><?= htmlspecialchars($row['Место']) ?></td>
                            <td><?= htmlspecialchars($row['Описание']) ?></td>
                            <td><?= htmlspecialchars($row['Заявитель']) ?></td>
                            <td><?= $row['Механик'] ? htmlspecialchars($row['Механик']) : '<i>Не назначен</i>' ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($row['Дата_создания'])) ?></td>
                            <td>
                                <?php if($row['Статус'] == 'новая'): ?>
                                    <span class="status-new">Новая</span>
                                <?php elseif($row['Статус'] == 'в работе'): ?>
                                    <span class="status-progress">В работе</span>
                                <?php else: ?>
                                    <span class="status-done">Выполнено</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>