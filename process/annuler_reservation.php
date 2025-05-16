<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.php');
    exit();
}

if (!isset($_POST['reservation_id'])) {
    echo "Erreur : ID de réservation manquant.";
    exit();
}

$reservation_id = $_POST['reservation_id'];
$user_id = $_SESSION['user_id'];


$query = $pdo->prepare("
    SELECT r.*, c.prix
    FROM reservations r
    JOIN covoiturages c ON r.covoiturage_id = c.id
    WHERE r.id = ? AND r.utilisateur_id = ?
");
$query->execute([$reservation_id, $user_id]);
$reservation = $query->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo "Réservation introuvable ou non autorisée.";
    exit();
}


if ($reservation['statut'] === 'annule') {
    echo "Réservation déjà annulée.";
    exit();
}


$query = $pdo->prepare("UPDATE reservations SET statut = 'annule' WHERE id = ?");
$query->execute([$reservation_id]);


$query = $pdo->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE id = ?");
$query->execute([$reservation['prix'], $user_id]);


$query = $pdo->prepare("UPDATE covoiturages SET places = places + 1 WHERE id = ?");
$query->execute([$reservation['covoiturage_id']]);


header('Location: ../pages/mes_reservations.php?annulation=success');
exit();
?>

