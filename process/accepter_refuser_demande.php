<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'chauffeur' && $_SESSION['role'] !== 'admin')) {
    exit();
}

if (isset($_POST['demande_id'], $_POST['action'])) {
    $demande_id = $_POST['demande_id'];
    $action = $_POST['action'];

    if ($action === 'accepter') {
        if (!isset($_POST['prix_propose']) || !is_numeric($_POST['prix_propose'])) {
            $_SESSION['error_message'] = "Le prix proposé est invalide.";
            header("Location: ../pages/gerer_demandes.php");
            exit;
        }

        $prix_propose = intval($_POST['prix_propose']);

        if ($prix_propose < 2) {
            $_SESSION['error_message'] = "Le prix proposé doit être d'au moins 2 crédits.";
            header("Location: ../pages/gerer_demandes.php");
            exit;
        }

        $query = $pdo->prepare("
            UPDATE demandes_trajets 
            SET statut = 'accepte', chauffeur_id = ?, prix_propose = ?, notification_vue = 0 
            WHERE id = ?
        ");
        $query->execute([$_SESSION['user_id'], $prix_propose, $demande_id]);

    } elseif ($action === 'refuser') {
        $query = $pdo->prepare("
            UPDATE demandes_trajets 
            SET statut = 'refuse', notification_vue = 0 
            WHERE id = ?
        ");
        $query->execute([$demande_id]);
    }
}

header("Location: ../pages/gerer_demandes.php");
exit();
?>



