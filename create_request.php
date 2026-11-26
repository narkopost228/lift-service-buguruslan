<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location_id = $_POST['location_id'];
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    $check_query = "SELECT ID FROM `Заявки` WHERE `ID_места` = $location_id AND `Статус` != 'выполнено'";
    $check_result = mysqli_query($connect, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = '<div style="color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin-bottom: 15px;">Ошибка: На этот лифт уже есть активная заявка. Дождитесь её выполнения.</div>';
    } else {
        $photo_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
            $filename = time() . '_' . basename($_FILES['photo']['name']);
            $photo_path = $upload_dir . $filename;
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        }

        $stmt = $connect->prepare("INSERT INTO `Заявки` (`ID_заявителя`, `ID_места`, `Описание`, `Путь_к_фото`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $location_id, $description, $photo_path);
        
        if ($stmt->execute()) {
            $message = '<div style="color: green; padding: 10px; background: #d4edda; border-radius: 5px; margin-bottom: 15px;">Заявка успешно создана! Она появилась в общем пуле.</div>';
        } else {
            $message = '<div style="color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin-bottom: 15px;">Ошибка: ' . $connect->error . '</div>';
        }
    }
}

$locations = mysqli_query($connect, "SELECT * FROM `Места` ORDER BY `Название`");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Новая заявка</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Новая заявка</h1>
        <div>
            <button class="theme-toggle" onclick="toggleTheme()">🌓 Сменить тему</button>
            <a href="dashboard.php">← На главную</a>
        </div>
    </div>

    <div class="container" style="max-width: 600px;">
        <div class="card">
            <?= $message ?>
            
            <form method="POST" enctype="multipart/form-data">
                <label>Выберите лифт (место)</label>
                <select name="location_id" required>
                    <option value="">-- Выберите из списка --</option>
                    <?php while($loc = mysqli_fetch_assoc($locations)): ?>
                        <option value="<?= $loc['ID'] ?>"><?= htmlspecialchars($loc['Название']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Что случилось?</label>
                <select id="quick_problem" onchange="document.getElementById('description').value = this.value;">
                    <option value="">-- Выберите частую проблему (или напишите ниже) --</option>
                    <option value="Не закрываются двери лифта">Не закрываются двери</option>
                    <option value="Не открываются двери лифта">Не открываются двери</option>
                    <option value="Не работает кнопка вызова на этаже">Не работает кнопка вызова</option>
                    <option value="Не работает освещение в кабине лифта">Не горит свет в кабине</option>
                    <option value="Лифт издает посторонний шум/скрип при движении">Посторонний шум/скрип</option>
                    <option value="Не работает связь с диспетчером (кнопка вызова)">Не работает связь с диспетчером</option>
                    <option value="Требуется проведение планового ТО">Требуется плановое ТО</option>
                </select>

                <textarea id="description" name="description" rows="4" required placeholder="Опишите неисправность своими словами или выберите из списка выше..."></textarea>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: -15px; margin-bottom: 20px;">
                    Если выбираете из списка, текст появится автоматически. Вы можете его отредактировать.
                </p>

                <label>Фото (опционально)</label>
                <input type="file" name="photo" accept="image/*" style="background: none; border: none; padding: 0; margin-bottom: 20px;">

                <button type="submit" class="btn btn-main" style="width: 100%;">Отправить заявку</button>
            </form>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>