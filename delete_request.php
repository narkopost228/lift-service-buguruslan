<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Удаляем только если заявка принадлежит этому пользователю и статус "новая"
$query = "DELETE FROM `Заявки` WHERE ID = $id AND ID_заявителя = $user_id AND Статус = 'новая'";
mysqli_query($connect, $query);

header('Location: my_requests.php');
exit;
?>