<?php
session_start();
include '../config/database.php';


if (!isset($_SESSION['user_id'])) {
    exit();
}


if (isset($_POST['avis_id'])) {
    $avis_id = $_POST['avis_id'];

    
    $query = $pdo->prepare("UPDATE avis SET signalement = 1 WHERE id = ?");
    $query->execute([$avis_id]);

    echo "Avis signalé.";
} else {
    echo "Erreur: L'ID de l'avis n'a pas été transmis.";
}
?>

