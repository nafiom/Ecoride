<?php
session_start();
include '../config/database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/connexion.php");
    exit();
}


if (!isset($_POST['trajet_id'])) {
    echo "Erreur : trajet non spécifié.";
    exit();
}

$trajet_id = $_POST['trajet_id'];
$user_id = $_SESSION['user_id'];


$query = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);


$query = $pdo->prepare("SELECT prix, places FROM covoiturages WHERE id = ?");
$query->execute([$trajet_id]);
$trajet = $query->fetch(PDO::FETCH_ASSOC);


if (!$trajet) {
    echo "Trajet introuvable.";
    exit();
}

if ($trajet['places'] <= 0) {
    echo "Plus de places disponibles.";
    exit();
}

if ($user['credits'] < $trajet['prix']) {
    echo "Crédits insuffisants.";
    exit();
}

// Déduire les crédits
$new_credits = $user['credits'] - $trajet['prix'];
$query = $pdo->prepare("UPDATE utilisateurs SET credits = ? WHERE id = ?");
$query->execute([$new_credits, $user_id]);

// Mettre à jour les places
$new_places = $trajet['places'] - 1;
$query = $pdo->prepare("UPDATE covoiturages SET places = ? WHERE id = ?");
$query->execute([$new_places, $trajet_id]);


$query = $pdo->prepare("INSERT INTO reservations (utilisateur_id, covoiturage_id, statut) VALUES (?, ?, 'en_attente')");
$query->execute([$user_id, $trajet_id]);


$query = $pdo->prepare("INSERT INTO credits_plateforme (date, credits_gagnes) VALUES (CURDATE(), ?)");
$query->execute([$trajet['prix']]);

header("Location: ../pages/recherche.php?success=1");
exit();
?>

