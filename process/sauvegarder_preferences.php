<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) exit();
$user_id = $_SESSION['user_id'];


$toutesPrefs = ['Fumeur', 'Animaux', 'Musique'];


$pdo->prepare("DELETE FROM preferences_conducteur WHERE utilisateur_id = ?")->execute([$user_id]);


foreach ($toutesPrefs as $type) {
    $valeur = isset($_POST['prefs'][$type]) ? 1 : 0;
    $query = $pdo->prepare("INSERT INTO preferences_conducteur (utilisateur_id, type_pref, valeur) VALUES (?, ?, ?)");
    $query->execute([$user_id, $type, $valeur]);
}

header("Location: ../pages/espace_chauffeur.php");
exit();
?>

