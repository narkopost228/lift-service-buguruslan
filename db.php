<?php
// Настройки подключения к базе данных MariaDB в Open Server 6
$host = 'MariaDB-10.11';
$user = 'root';
$password = ''; // В Open Server 6 пароль по умолчанию пустой
$dbname = 'lift_service';

$connect = mysqli_connect($host, $user, $password, $dbname);

if (!$connect) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}

// Устанавливаем кодировку, чтобы русские буквы работали корректно
mysqli_set_charset($connect, 'utf8mb4');
?>