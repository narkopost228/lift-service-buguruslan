<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'исполнитель' && $_SESSION['user_role'] != 'админ')) {
    die('Доступ запрещен.');
}

$message = '';

if (isset($_GET['take_id'])) {
    $take_id = intval($_GET['take_id']);
    $executor_id = $_SESSION['user_id'];
    
    $update = "UPDATE `Заявки` SET `ID_исполнителя` = $executor_id, `Статус` = 'в работе' WHERE `ID` = $take_id AND `Статус` = 'новая'";
    mysqli_query($connect, $update);
    
    $_SESSION['flash_message'] = '<div style="color: green; padding: 10px; background: #d4edda; border-radius: 5px; margin-bottom: 15px;">Заявка взята в работу! Она перемещена во вкладку "Мои задачи".</div>';
    
    header('Location: pool.php');
    exit;
}

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

$query = "SELECT z.*, m.Название AS Место, u.ФИО AS Заявитель, u.Телефон 
          FROM `Заявки` z 
          JOIN `Места` m ON z.ID_места = m.ID 
          JOIN `Пользователи` u ON z.ID_заявителя = u.ID
          WHERE z.Статус = 'новая' 
          ORDER BY z.Дата_создания ASC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Новые заявки - Лифты ГБУЗ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Новые заявки</h1>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="dashboard.php">← На главную</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <?= $message ?>
            <?php if (mysqli_num_rows($result) == 0): ?>
                <div style="text-align: center; padding: 30px; color: var(--text-muted);">
                    <h3>Ура! Новых заявок нет. Все лифты работают.</h3>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Лифт (Место)</th>
                            <th>Описание</th>
                            <th>Заявитель</th>
                            <th>Телефон</th>
                            <th>Дата</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Место']) ?></td>
                            <td><?= htmlspecialchars($row['Описание']) ?></td>
                            <td><?= htmlspecialchars($row['Заявитель']) ?></td>
                            <td style="font-weight: bold; white-space: nowrap;"><?= !empty($row['Телефон']) ? htmlspecialchars($row['Телефон']) : 'Нет номера' ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($row['Дата_создания'])) ?></td>
                            <td>
                                <a href="pool.php?take_id=<?= $row['ID'] ?>" class="btn btn-main">Взять себе</a>
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