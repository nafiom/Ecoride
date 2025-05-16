<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les réservations
$query = $pdo->prepare("
    SELECT r.id AS reservation_id, c.id AS trajet_id, c.depart, c.arrivee, c.date_depart, c.date_arrivee, c.prix,
           u.pseudo AS chauffeur, r.statut
    FROM reservations r
    JOIN covoiturages c ON r.covoiturage_id = c.id
    JOIN utilisateurs u ON c.chauffeur_id = u.id
    WHERE r.utilisateur_id = ?
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
    <title>Mes réservations</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2fdf4;
            padding: 2rem;
            color: #2f4730;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 2rem;
        }

        .reservation {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        .reservation:hover {
            transform: translateY(-3px);
        }

        .reservation p {
            margin: 0.4rem 0;
        }

        .leave-review, .cancel-reservation {
            margin-top: 10px;
            text-align: center;
        }

        .already-reviewed {
            text-align: center;
            color: #28a745;
            font-weight: bold;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .cancel-reservation button {
            background-color: #dc3545;
        }

        .cancel-reservation button:hover {
            background-color: #c82333;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 2rem;
            text-decoration: none;
            font-weight: bold;
            color: #2e7d32;
        }

        a:hover {
            text-decoration: underline;
        }

        .alert-success {
            background-color: #d4edda;
            padding: 10px;
            border-radius: 5px;
            color: #155724;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<?php if (isset($_GET['annulation']) && $_GET['annulation'] == 'success') : ?>
    <div class="alert-success">
        ✅ Réservation annulée et crédits remboursés.
    </div>
<?php endif; ?>

<h1>Mes réservations</h1>

<?php if (empty($reservations)) : ?>
    <p style="text-align:center;">Vous n'avez encore réservé aucun trajet.</p>
<?php else : ?>
    <?php foreach ($reservations as $r) : ?>
        <?php
            $avisQuery = $pdo->prepare("SELECT id FROM avis WHERE passager_id = ? AND covoiturage_id = ?");
            $avisQuery->execute([$user_id, $r['trajet_id']]);
            $avis_exists = $avisQuery->fetch();
        ?>
        <div class="reservation">
            <p><strong>Départ :</strong> <?= htmlspecialchars($r['depart']) ?> → <strong>Arrivée :</strong> <?= htmlspecialchars($r['arrivee']) ?></p>
            <p><strong>Date départ :</strong> <?= $r['date_depart'] ?></p>
            <p><strong>Date arrivée :</strong> <?= $r['date_arrivee'] ?></p>
            <p><strong>Chauffeur :</strong> <?= htmlspecialchars($r['chauffeur']) ?></p>
            <p><strong>Prix :</strong> <?= $r['prix'] ?> crédits</p>
            <p><strong>Statut :</strong> 
                <?php
                    switch ($r['statut']) {
                        case 'en_attente': echo '⏳ En attente'; break;
                        case 'confirme': echo '✅ Confirmé'; break;
                        case 'annule': echo ' Annulé'; break;
                        default: echo htmlspecialchars($r['statut']); break;
                    }
                ?>
            </p>

            <?php if ($r['statut'] === 'confirme') : ?>
                <?php if (!$avis_exists) : ?>
                    <div class="leave-review">
                        <form action="laisser_avis.php" method="GET">
                            <input type="hidden" name="trajet_id" value="<?= htmlspecialchars($r['trajet_id']) ?>">
                            <button type="submit"> Laisser un avis</button>
                        </form>
                    </div>
                <?php else : ?>
                    <div class="already-reviewed">✔️ Avis déjà laissé</div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($r['statut'] === 'en_attente') : ?>
                <div class="cancel-reservation">
                    <form action="../process/annuler_reservation.php" method="POST">
                        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($r['reservation_id']) ?>">
                        <button type="submit">❌ Annuler la réservation</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="espace_utilisateur.php">← Retour à mon espace</a>

</body>
</html>





