<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employe') {
    header("Location: ../pages/connexion.php");
    exit();
}

$avis_id = $_POST['avis_id'];
$action = $_POST['action'];

if ($action === 'valider') {
    $query = $pdo->prepare("UPDATE avis SET valide = 1 WHERE id = ?");
    $query->execute([$avis_id]);
} elseif ($action === 'refuser') {
    $query = $pdo->prepare("DELETE FROM avis WHERE id = ?");
    $query->execute([$avis_id]);
}

header("Location: ../pages/espace_employe.php");
exit();
?>
