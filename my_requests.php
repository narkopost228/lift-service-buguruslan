<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT z.*, m.Название AS Место, u.ФИО AS Механик 
          FROM `Заявки` z 
          JOIN `Места` m ON z.ID_места = m.ID 
          LEFT JOIN `Пользователи` u ON z.ID_исполнителя = u.ID
          WHERE z.ID_заявителя = $user_id 
          ORDER BY z.Дата_создания DESC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои заявки - Лифты ГБУЗ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Мои заявки</h1>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="dashboard.php">← На главную</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <?php if (mysqli_num_rows($result) == 0): ?>
                <p>У вас пока нет заявок. <a href="create_request.php">Создайте первую!</a></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Лифт (Место)</th>
                            <th>Описание</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Исполнитель</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Место']) ?></td>
                            <td><?= htmlspecialchars($row['Описание']) ?></td>
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
                            <td><?= $row['Механик'] ? htmlspecialchars($row['Механик']) : '-' ?></td>
                            <td>
                                <?php if($row['Статус'] == 'новая'): ?>
                                    <a href="delete_request.php?id=<?= $row['ID'] ?>" class="btn-del" onclick="return confirm('Удалить заявку?')">Удалить</a>
                                <?php else: ?>
                                    -
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