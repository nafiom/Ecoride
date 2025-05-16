<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$voiture_id = $_POST['voiture_id'] ?? null;

if (!$voiture_id) {
    header("Location: ../pages/espace_chauffeur.php?error=no_id");
    exit();
}


$query = $pdo->prepare("SELECT * FROM voitures WHERE id = ? AND user_id = ?");
$query->execute([$voiture_id, $user_id]);
$voiture = $query->fetch();

if (!$voiture) {
    header("Location: ../pages/espace_chauffeur.php?error=not_owner");
    exit();
}


$check = $pdo->prepare("SELECT COUNT(*) FROM covoiturages WHERE voiture_id = ?");
$check->execute([$voiture_id]);
if ($check->fetchColumn() > 0) {
    header("Location: ../pages/espace_chauffeur.php?error=used_in_trajet");
    exit();
}

// Supprimer
$delete = $pdo->prepare("DELETE FROM voitures WHERE id = ? AND user_id = ?");
$delete->execute([$voiture_id, $user_id]);

header("Location: ../pages/espace_chauffeur.php?success=deleted");
exit();
