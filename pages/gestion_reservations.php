<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'chauffeur') {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = $pdo->prepare("
    SELECT r.id AS reservation_id, r.statut, u.pseudo AS passager, c.depart, c.arrivee, c.date_depart, c.date_arrivee
    FROM reservations r
    JOIN utilisateurs u ON r.utilisateur_id = u.id
    JOIN covoiturages c ON r.covoiturage_id = c.id
    WHERE c.chauffeur_id = ?
    ORDER BY c.date_depart DESC
");
$query->execute([$user_id]);
$reservations = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Gestion des réservations</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f9f4;
            margin: 2rem;
            color: #2f4730;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #1b5e20;
        }

        .reservation-box {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 128, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .reservation-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 128, 0, 0.15);
        }

        p {
            margin: 0.3rem 0;
        }

        .statut {
            font-weight: bold;
            color: #388e3c;
        }

        form {
            margin-top: 10px;
        }

        button {
            padding: 8px 14px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[name="action"][value="confirme"] {
            background-color: #2e7d32;
            color: white;
        }

        button[name="action"][value="annule"] {
            background-color: #c62828;
            color: white;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 3rem;
            font-weight: bold;
            color: #2e7d32;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Réservations reçues</h1>

<?php if (empty($reservations)) : ?>
    <p style="text-align: center;">Aucune réservation pour l’instant.</p>
<?php else : ?>
    <?php foreach ($reservations as $r) : ?>
        <div class="reservation-box">
            <p><strong> Passager :</strong> <?= htmlspecialchars($r['passager']) ?></p>
            <p><strong>Trajet :</strong> <?= htmlspecialchars($r['depart']) ?> → <?= htmlspecialchars($r['arrivee']) ?></p>
            <p><strong>Départ :</strong> <?= $r['date_depart'] ?></p>
            <p><strong> Statut :</strong> <span class="statut"><?= ucfirst($r['statut']) ?></span></p>
            <?php if ($r['statut'] === 'en_attente') : ?>
                <form method="POST" action="../process/modifier_reservation.php">
                    <input type="hidden" name="reservation_id" value="<?= $r['reservation_id'] ?>">
                    <button type="submit" name="action" value="confirmé">✅ Valider</button>
                    <button type="submit" name="action" value="annulé">❌ Annuler</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="espace_chauffeur.php">← Retour à l’espace chauffeur</a>

</body>
</html>

