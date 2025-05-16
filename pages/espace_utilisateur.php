<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];


$notifQuery = $pdo->prepare("SELECT COUNT(*) FROM demandes_trajets WHERE utilisateur_id = ? AND notification_vue = 0 AND statut IN ('accepte', 'refuse')");
$notifQuery->execute([$user_id]);
$notifCount = $notifQuery->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Espace Utilisateur</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0faf4;
            padding: 2rem;
            color: #2e7d32;
        }

        h1 {
            text-align: center;
            color: #1b5e20;
            margin-bottom: 2rem;
        }

        ul {
            list-style: none;
            padding: 0;
            max-width: 600px;
            margin: auto;
        }

        li {
            background: #ffffff;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 15px;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 128, 0, 0.1);
        }

        a {
            text-decoration: none;
            color: #2e7d32;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .notif-badge {
            background-color: #d32f2f;
            color: white;
            padding: 4px 10px;
            font-size: 0.8rem;
            border-radius: 20px;
        }
    </style>
</head>
<body>

<h1> Mon espace utilisateur</h1>

<ul>
    <?php if ($role === 'chauffeur'): ?>
        <li> Vous êtes actuellement chauffeur.</li>
        <li><a href="../process/ne_plus_etre_chauffeur.php">🚫 Ne plus être chauffeur</a></li>
        <li><a href="espace_chauffeur.php">➡️ Accéder à mon espace chauffeur</a></li>
    <?php else: ?>
        <li><a href="../process/devenir_chauffeur.php"> Devenir chauffeur</a></li>
    <?php endif; ?>

    <li><a href="recherche.php">🔍 Recherche de covoiturages</a></li>
    <li><a href="mes_reservations.php">Mes réservations</a></li>
    <li>
        <a href="mes_demandes.php">Voir mes demandes</a>
        <?php if ($notifCount > 0): ?>
            <span class="notif-badge"><?= $notifCount ?></span>
        <?php endif; ?>
    </li>
    <li><a href="mes_annulations.php">❌ Voir mes annulations</a></li>
    <form method="POST" action="../process/supprimer_compte.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
    <button type="submit" style="background:red;color:white;border:none;padding:10px;border-radius:5px;">🗑️ Supprimer mon compte</button>
</form>
</ul>

</body>
</html>


