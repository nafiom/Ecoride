<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/connexion.php");
    exit();
}

if (!isset($_POST['user_id'], $_POST['action'])) {
    header("Location: ../pages/espace_admin.php?error=missing_data");
    exit();
}

$user_id = (int)$_POST['user_id'];
$action = $_POST['action'];

if ($action === 'suspend') {
    $query = $pdo->prepare("UPDATE utilisateurs SET suspendu = 1 WHERE id = ?");
    $query->execute([$user_id]);
    header("Location: ../pages/espace_admin.php?action=suspendu");
    exit();
} elseif ($action === 'unsuspend') {
    $query = $pdo->prepare("UPDATE utilisateurs SET suspendu = 0 WHERE id = ?");
    $query->execute([$user_id]);
    header("Location: ../pages/espace_admin.php?action=reactive");
    exit();
}

header("Location: ../pages/espace_admin.php");
exit();


