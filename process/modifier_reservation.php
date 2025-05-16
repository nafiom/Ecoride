<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'chauffeur') {
    header("Location: ../pages/connexion.php");
    exit();
}

if (isset($_POST['reservation_id'], $_POST['action'])) {
    $reservation_id = $_POST['reservation_id'];
    $action = $_POST['action'];

    if (in_array($action, ['confirme', 'annule'])) {
        $query = $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
        $query->execute([$action, $reservation_id]);
    }
}

header("Location: ../pages/gestion_reservations.php");
exit();
