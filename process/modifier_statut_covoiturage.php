<?php
session_start();
require '../config/database.php';
require '../utils/send_mail.php';

use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'chauffeur') {
    header("Location: ../connexion.php");
    exit();
}


function genererTemplateMail($titre, $messagePrincipal, $infos = [], $footer = '')
{
    $listeInfos = '';
    foreach ($infos as $label => $valeur) {
        $listeInfos .= "<li><strong>$label :</strong> " . htmlspecialchars($valeur) . "</li>";
    }

    return "
    <div style='font-family:Segoe UI,sans-serif; background:#f4fff6; padding:30px; border-radius:10px; color:#2e7d32; max-width:600px; margin:auto; box-shadow:0 0 15px rgba(0,0,0,0.05);'>
        <h2 style='text-align:center; color:#28a745;'>$titre</h2>
        <p style='font-size:16px;'>$messagePrincipal</p>
        <ul style='margin: 20px 0; font-size:15px;'>$listeInfos</ul>
        <p style='font-size:14px;'>$footer</p>
        <hr style='margin:30px 0; border:none; border-top:1px solid #d4edda;'>
        <p style='text-align:center; font-size:13px; color:#888;'>L’équipe ECORIDE 🌱</p>
    </div>
    ";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $covoiturage_id = $_POST['covoiturage_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$covoiturage_id || !$action) {
        exit("Paramètres manquants.");
    }

    $nouveau_statut = match($action) {
        'demarrer' => 'en_cours',
        'terminer' => 'termine',
        'annuler' => 'annule',
        default => null
    };

    if (!$nouveau_statut) {
        exit("Action invalide.");
    }

    // Récupération du trajet
    $trajetQuery = $pdo->prepare("SELECT * FROM covoiturages WHERE id = ?");
    $trajetQuery->execute([$covoiturage_id]);
    $trajet = $trajetQuery->fetch(PDO::FETCH_ASSOC);

    if (!$trajet) exit("Trajet introuvable.");

    // Nom du chauffeur
    $chauffeurQuery = $pdo->prepare("SELECT pseudo FROM utilisateurs WHERE id = ?");
    $chauffeurQuery->execute([$_SESSION['user_id']]);
    $chauffeur = $chauffeurQuery->fetchColumn();

    if ($nouveau_statut === 'annule') {
        $resQuery = $pdo->prepare("
            SELECT r.id AS reservation_id, r.utilisateur_id, u.email, u.pseudo, c.prix
            FROM reservations r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            JOIN covoiturages c ON c.id = r.covoiturage_id
            WHERE r.covoiturage_id = ? AND r.statut = 'confirme'
        ");
        $resQuery->execute([$covoiturage_id]);
        $passagers = $resQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($passagers as $p) {
            $pdo->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE id = ?")
                ->execute([$p['prix'], $p['utilisateur_id']]);

            $pdo->prepare("UPDATE reservations SET statut = 'annule' WHERE id = ?")
                ->execute([$p['reservation_id']]);

            $sujet = "❌ Trajet ECORIDE annulé par le chauffeur";
            $contenuHtml = genererTemplateMail(
                "Trajet annulé",
                "Bonjour <strong>{$p['pseudo']}</strong>,<br>Le chauffeur <strong>{$chauffeur}</strong> a annulé le trajet suivant :",
                [
                    "Départ" => $trajet['depart'],
                    "Arrivée" => $trajet['arrivee'],
                    "Date et heure" => $trajet['date_depart'],
                    "Crédits remboursés" => "{$p['prix']} €"
                ],
                "Vos crédits ont été automatiquement recrédités sur votre compte."
            );

            envoyer_mail($p['email'], $p['pseudo'], $sujet, $contenuHtml);
        }
    }

    $pdo->prepare("UPDATE covoiturages SET statut = ? WHERE id = ?")
        ->execute([$nouveau_statut, $covoiturage_id]);

    if ($nouveau_statut === 'en_cours') {
        $resQuery = $pdo->prepare("
            SELECT u.email, u.pseudo
            FROM reservations r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            WHERE r.covoiturage_id = ? AND r.statut = 'confirme'
        ");
        $resQuery->execute([$covoiturage_id]);
        $passagers = $resQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($passagers as $passager) {
            $sujet = "🚗 Le chauffeur est en route pour votre trajet ECORIDE";
            $contenuHtml = genererTemplateMail(
                "Le chauffeur est en route",
                "Bonjour <strong>{$passager['pseudo']}</strong>,<br>Le chauffeur <strong>{$chauffeur}</strong> vient de démarrer le trajet :",
                [
                    "Départ" => $trajet['depart'],
                    "Arrivée" => $trajet['arrivee'],
                    "Date et heure" => $trajet['date_depart']
                ],
                "Merci d’être prêt à le rejoindre à l’heure prévue ⏰"
            );

            envoyer_mail($passager['email'], $passager['pseudo'], $sujet, $contenuHtml);
        }
    }
// Si le trajet est terminé, créditer le chauffeur
if ($nouveau_statut === 'termine') {
    $gainQuery = $pdo->prepare("
        SELECT SUM(c.prix) AS total
        FROM reservations r
        JOIN covoiturages c ON c.id = r.covoiturage_id
        WHERE r.covoiturage_id = ? AND r.statut = 'confirme'
    ");
    $gainQuery->execute([$covoiturage_id]);
    $result = $gainQuery->fetch();
    $creditsTotal = (int)$result['total'];

    if ($creditsTotal > 0) {
        // Créditer le chauffeur
        $pdo->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE id = ?")
            ->execute([$creditsTotal, $_SESSION['user_id']]);

        // Envoi d'un email récapitulatif au chauffeur
        $sujet = "✅ Trajet terminé - Crédits ajoutés";
        $contenuHtml = genererTemplateMail(
            "Trajet terminé avec succès",
            "Bonjour <strong>{$chauffeur}</strong>,<br>Vous venez de terminer un trajet ECORIDE. Les crédits correspondants vous ont été attribués.",
            [
                "Trajet" => $trajet['depart'] . " → " . $trajet['arrivee'],
                "Date" => $trajet['date_depart'],
                "Crédits gagnés" => "{$creditsTotal} €"
            ],
            "Merci pour votre engagement pour une mobilité plus verte 🌿"
        );

        // Récupération de l’email du chauffeur
        $emailQuery = $pdo->prepare("SELECT email FROM utilisateurs WHERE id = ?");
        $emailQuery->execute([$_SESSION['user_id']]);
        $email = $emailQuery->fetchColumn();

        envoyer_mail($email, $chauffeur, $sujet, $contenuHtml);
    }
}
    header("Location: ../pages/espace_chauffeur.php");
    exit();
}
?>





