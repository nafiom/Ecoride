<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) exit();

$user_id = $_SESSION['user_id'];


$query = $pdo->prepare("SELECT photo FROM utilisateurs WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user && $user['photo']) {
    $path = '../uploads/' . $user['photo'];
    if (file_exists($path)) unlink($path);

    $query = $pdo->prepare("UPDATE utilisateurs SET photo = NULL WHERE id = ?");
    $query->execute([$user_id]);
}

header("Location: ../pages/espace_chauffeur.php");
exit();
?>
