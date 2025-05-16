<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Revenir au rôle d'utilisateur
$query = $pdo->prepare("UPDATE utilisateurs SET role = 'utilisateur' WHERE id = ?");
$query->execute([$user_id]);

$_SESSION['role'] = 'utilisateur'; // met à jour la session aussi
header("Location: ../pages/espace_utilisateur.php");
exit();
?>

