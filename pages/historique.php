<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$isChauffeur = ($role === 'chauffeur');

$retourPage = "connexion.php";
if ($role === 'chauffeur') {
    $retourPage = "espace_chauffeur.php";
} elseif ($role === 'admin') {
    $retourPage = "espace_admin.php";
} elseif ($role === 'employe') {
    $retourPage = "espace_employe.php";
} else {
    $retourPage = "espace_utilisateur.php";
}

if ($isChauffeur) {
    $query = $pdo->prepare("
        SELECT id AS covoiturage_id, depart, arrivee, date_depart, date_arrivee, prix, ecologique
        FROM covoiturages
        WHERE chauffeur_id = ?
        ORDER BY date_depart DESC
    ");
    $query->execute([$user_id]);
    $trajets = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    $query = $pdo->prepare("
        SELECT c.id AS covoiturage_id, c.depart, c.arrivee, c.date_depart, c.date_arrivee, c.prix, c.ecologique,
               u.pseudo AS chauffeur, r.statut
        FROM reservations r
        JOIN covoiturages c ON r.covoiturage_id = c.id
        JOIN utilisateurs u ON c.chauffeur_id = u.id
        WHERE r.utilisateur_id = ? AND r.statut = 'confirme'
        ORDER BY c.date_depart DESC
    ");
    $query->execute([$user_id]);
    $trajets = $query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Historique des Trajets</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1faf3;
            color: #2f4730;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #1b5e20;
        }

        ul {
            list-style: none;
            padding: 0;
            max-width: 800px;
            margin: auto;
        }

        li {
            background: #fff;
            border-left: 5px solid #4caf50;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,128,0,0.1);
            transition: transform 0.2s ease;
        }

        li:hover {
            transform: scale(1.01);
        }

        strong {
            font-size: 1.1rem;
        }

        .retour {
            display: block;
            text-align: center;
            margin-top: 30px;
            font-weight: bold;
            color: #2e7d32;
            text-decoration: none;
        }

        .retour:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Historique de mes trajets</h1>

<?php if (empty($trajets)) : ?>
    <p style="text-align:center;">Aucun trajet trouvé.</p>
<?php else : ?>
    <ul>
        <?php foreach ($trajets as $trajet) : ?>
            <li>
                <strong><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['arrivee']) ?></strong><br>
                 Date : <?= $trajet['date_depart'] ?> → <?= $trajet['date_arrivee'] ?><br>
                 Prix : <?= $trajet['prix'] ?> crédits<br>
                🌱 Écologique : <?= $trajet['ecologique'] ? " Oui" : " Non" ?>

                <?php if (!$isChauffeur && isset($trajet['chauffeur'])): ?>
                    <br> Chauffeur : <?= htmlspecialchars($trajet['chauffeur']) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a class="retour" href="<?= $retourPage ?>">← Retour</a>

</body>
</html>





