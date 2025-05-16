<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = $pdo->prepare("
    SELECT c.depart, c.arrivee, c.date_depart, c.date_arrivee, c.prix, u.pseudo AS chauffeur
    FROM reservations r
    JOIN covoiturages c ON r.covoiturage_id = c.id
    JOIN utilisateurs u ON c.chauffeur_id = u.id
    WHERE r.utilisateur_id = ? AND r.statut = 'annule'
    ORDER BY c.date_depart DESC
");
$query->execute([$user_id]);
$annulations = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Mes annulations</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 2rem;
            background: #eefaf1;
            color: #2e5e3e;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 2rem;
        }

        .annulation {
            background: #ffffff;
            border-left: 5px solid #dc3545;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s ease;
        }

        .annulation:hover {
            transform: translateY(-2px);
        }

        .annulation p {
            margin: 0.5rem 0;
        }

        .annulation strong {
            color: #1b4626;
        }

        .no-data {
            text-align: center;
            color: #555;
            background-color: #fefefe;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: auto;
        }

        .retour {
            display: block;
            text-align: center;
            margin-top: 2rem;
            font-weight: bold;
            text-decoration: none;
            color: #2e7d32;
        }

        .retour:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1> Mes trajets annulés</h1>

<?php if (empty($annulations)) : ?>
    <div class="no-data">
        Vous n'avez pas encore annulé de réservation.
    </div>
<?php else : ?>
    <?php foreach ($annulations as $a) : ?>
        <div class="annulation">
            <p><strong>Départ :</strong> <?= htmlspecialchars($a['depart']) ?> → <strong>Arrivée :</strong> <?= htmlspecialchars($a['arrivee']) ?></p>
            <p><strong>Date départ :</strong> <?= $a['date_depart'] ?></p>
            <p><strong>Date arrivée :</strong> <?= $a['date_arrivee'] ?></p>
            <p><strong>Chauffeur :</strong> <?= htmlspecialchars($a['chauffeur']) ?></p>
            <p><strong>Prix remboursé :</strong> <?= $a['prix'] ?> crédits</p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a class="retour" href="espace_utilisateur.php">← Retour à mon espace</a>

</body>
</html>

