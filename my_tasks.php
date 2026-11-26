<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'исполнитель' && $_SESSION['user_role'] != 'админ')) {
    die('Доступ запрещен.');
}

$executor_id = $_SESSION['user_id'];

if (isset($_GET['done_id'])) {
    $done_id = intval($_GET['done_id']);
    $update = "UPDATE `Заявки` SET `Статус` = 'выполнено' WHERE `ID` = $done_id AND `ID_исполнителя` = $executor_id";
    mysqli_query($connect, $update);
    header('Location: my_tasks.php'); 
    exit;
}

$show_done = isset($_GET['show']) && $_GET['show'] == 'done';
$status_filter = $show_done ? 'выполнено' : 'в работе';

$query = "SELECT z.*, m.Название AS Место, u.ФИО AS Заявитель, u.Телефон 
          FROM `Заявки` z 
          JOIN `Места` m ON z.ID_места = m.ID 
          JOIN `Пользователи` u ON z.ID_заявителя = u.ID
          WHERE z.ID_исполнителя = $executor_id AND z.Статус = '$status_filter' 
          ORDER BY z.Дата_обновления DESC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои задачи - Лифты ГБУЗ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Мои задачи</h1>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="dashboard.php">← На главную</a>
        </div>
    </div>

    <div class="container">
        <div class="tabs">
            <a href="my_tasks.php" class="tab <?= !$show_done ? 'active' : '' ?>">В работе</a>
            <a href="my_tasks.php?show=done" class="tab <?= $show_done ? 'active' : '' ?>">Выполненные</a>
        </div>

        <div class="card">
            <?php if (mysqli_num_rows($result) == 0): ?>
                <div style="text-align: center; padding: 30px; color: var(--text-muted);">
                    <h3><?= $show_done ? 'У вас пока нет выполненных задач.' : 'У вас нет активных задач. Возьмите заявку из общего пула.' ?></h3>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Лифт (Место)</th>
                            <th>Описание</th>
                            <th>Заявитель / Телефон</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <?php if (!$show_done): ?><th>Действие</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Место']) ?></td>
                            <td><?= htmlspecialchars($row['Описание']) ?></td>
                            <td>
                                <div><?= htmlspecialchars($row['Заявитель']) ?></div>
                                <div style="font-weight: bold; color: var(--text-muted); font-size: 13px;">
                                    <?= !empty($row['Телефон']) ? htmlspecialchars($row['Телефон']) : 'Нет номера' ?>
                                </div>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($row['Дата_создания'])) ?></td>
                            <td>
                                <?php if($row['Статус'] == 'в работе'): ?>
                                    <span class="status-progress">В работе</span>
                                <?php else: ?>
                                    <span class="status-done">Выполнено</span>
                                <?php endif; ?>
                            </td>
                            <?php if (!$show_done): ?>
                            <td>
                                <a href="my_tasks.php?done_id=<?= $row['ID'] ?>" class="btn btn-main">Выполнено</a>
                            </td>
                            <?php endif; ?>
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