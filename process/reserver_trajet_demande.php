<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/connexion.php");
    exit();
}

if (!isset($_POST['demande_id'])) {
    echo "❌ Erreur : demande non spécifiée.";
    exit();
}

$demande_id = (int) $_POST['demande_id'];
$user_id = $_SESSION['user_id'];


$query = $pdo->prepare("
    SELECT * FROM demandes_trajets 
    WHERE id = ? AND utilisateur_id = ? AND statut = 'accepte'
");
$query->execute([$demande_id, $user_id]);
$demande = $query->fetch(PDO::FETCH_ASSOC);

if (!$demande) {
    echo "❌ Demande introuvable ou déjà traitée.";
    exit();
}

$prix = (int) $demande['prix_propose'];


$query = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();

if (!$user || $user['credits'] < $prix) {
    echo "❗ Crédits insuffisants.";
    exit();
}


$check = $pdo->prepare("SELECT id FROM reservations WHERE utilisateur_id = ? AND covoiturage_id = ?");
$check->execute([$user_id, $demande['covoiturage_id'] ?? 0]);
if ($check->fetch()) {
    echo "✅ Vous avez déjà réservé ce trajet.";
    exit();
}


if (empty($demande['covoiturage_id'])) {
    // Récupérer une voiture du chauffeur
    $voitureStmt = $pdo->prepare("SELECT * FROM voitures WHERE user_id = ? LIMIT 1");
    $voitureStmt->execute([$demande['chauffeur_id']]);
    $voiture = $voitureStmt->fetch();

    if (!$voiture) {
        echo "❌ Le chauffeur n’a pas de véhicule enregistré.";
        exit();
    }

    
    $insertTrajet = $pdo->prepare("
        INSERT INTO covoiturages (chauffeur_id, voiture_id, depart, arrivee, date_depart, date_arrivee, prix, places, ecologique)
        VALUES (?, ?, ?, ?, ?, ?, ?, 4, ?)
    ");
    $insertTrajet->execute([
        $demande['chauffeur_id'],
        $voiture['id'],
        $demande['depart'],
        $demande['arrivee'],
        $demande['date_depart'],
        $demande['date_depart'],
        $prix,
        $voiture['ecologique']
    ]);

    $covoiturage_id = $pdo->lastInsertId();

    
    $pdo->prepare("UPDATE demandes_trajets SET covoiturage_id = ? WHERE id = ?")
        ->execute([$covoiturage_id, $demande_id]);
} else {
    $covoiturage_id = $demande['covoiturage_id'];
}


$stmtPlaces = $pdo->prepare("SELECT places FROM covoiturages WHERE id = ?");
$stmtPlaces->execute([$covoiturage_id]);
$placesRow = $stmtPlaces->fetch();
if (!$placesRow || $placesRow['places'] < 1) {
    echo "❌ Aucune place disponible.";
    exit();
}


$newCredits = $user['credits'] - $prix;
$pdo->prepare("UPDATE utilisateurs SET credits = ? WHERE id = ?")
    ->execute([$newCredits, $user_id]);


$pdo->prepare("
    INSERT INTO reservations (utilisateur_id, covoiturage_id, statut)
    VALUES (?, ?, 'confirme')
")->execute([$user_id, $covoiturage_id]);


$pdo->prepare("UPDATE covoiturages SET places = places - 1 WHERE id = ?")
    ->execute([$covoiturage_id]);


$pdo->prepare("INSERT INTO credits_plateforme (date, credits_gagnes) VALUES (NOW(), ?)")
    ->execute([$prix]);

header("Location: ../pages/mes_reservations.php?success=1");
exit();



