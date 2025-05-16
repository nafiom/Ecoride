<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/connexion.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete_account'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    
    $stmt = $pdo->prepare("SELECT id FROM voitures WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $voitureIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($voitureIds) {
        
        $in = str_repeat('?,', count($voitureIds) - 1) . '?';
        $pdo->prepare("DELETE FROM covoiturages WHERE voiture_id IN ($in)")->execute($voitureIds);
    }

    
    $pdo->prepare("DELETE FROM covoiturages WHERE chauffeur_id = ?")->execute([$user_id]);

    
    $pdo->prepare("DELETE FROM reservations WHERE utilisateur_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM participants WHERE utilisateur_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM preferences_conducteur WHERE utilisateur_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM avis WHERE passager_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM avis WHERE conducteur_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM demandes_trajets WHERE utilisateur_id = ?")->execute([$user_id]);

    try {
        $pdo->prepare("DELETE FROM notifications WHERE utilisateur_id = ?")->execute([$user_id]);
    } catch (PDOException $e) {}

    try {
        $pdo->prepare("DELETE FROM credits_plateforme WHERE utilisateur_id = ?")->execute([$user_id]);
    } catch (PDOException $e) {}

    
    $pdo->prepare("DELETE FROM voitures WHERE user_id = ?")->execute([$user_id]);

   
    $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$user_id]);

    $pdo->commit();
    session_destroy();
    header("Location: ../index.php?account_deleted=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Erreur suppression compte : " . $e->getMessage());

    $role = $_SESSION['role'] ?? 'utilisateur';
    $redirect = match ($role) {
        'chauffeur' => '../pages/espace_chauffeur.php',
        'employe' => '../pages/espace_employe.php',
        'admin' => '../pages/espace_admin.php',
        default => '../pages/espace_utilisateur.php',
    };

    header("Location: $redirect?error=suppression");
    exit;
}



