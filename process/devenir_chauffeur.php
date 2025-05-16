<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = $pdo->prepare("UPDATE utilisateurs SET role = 'chauffeur' WHERE id = ?");
$query->execute([$user_id]);

$_SESSION['role'] = 'chauffeur'; // met à jour la session aussi
header("Location: ../pages/espace_chauffeur.php");
exit();
?>
