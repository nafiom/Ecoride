<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/connexion.php");
    exit();
}

$trajet_id = $_POST['trajet_id'];
$user_id = $_SESSION['user_id'];


$query = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? AND trajet_id = ?");
$query->execute([$user_id, $trajet_id]);
$reservation = $query->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo "Vous n'avez pas participé à ce trajet.";
    exit();
}


$query = $pdo->prepare("UPDATE reservations SET valide = 1 WHERE user_id = ? AND trajet_id = ?");
$query->execute([$user_id, $trajet_id]);

echo "Trajet validé avec succès !";
?>
