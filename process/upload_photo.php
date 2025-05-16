<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) exit();

$user_id = $_SESSION['user_id'];

if (!empty($_FILES['photo']['tmp_name'])) {
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $uploadDir = '../uploads/';

    if (!is_dir($uploadDir)) mkdir($uploadDir);

    move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename);

    $query = $pdo->prepare("UPDATE utilisateurs SET photo = ? WHERE id = ?");
    $query->execute([$filename, $user_id]);

    header("Location: ../pages/espace_chauffeur.php");
    exit();
}
?>
