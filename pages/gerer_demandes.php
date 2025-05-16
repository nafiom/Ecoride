<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'chauffeur' && $_SESSION['role'] !== 'admin')) {
    header("Location: connexion.php");
    exit();
}

$query = $pdo->query("
    SELECT d.id, d.depart, d.arrivee, d.date_depart, u.pseudo 
    FROM demandes_trajets d 
    JOIN utilisateurs u ON d.utilisateur_id = u.id
    WHERE d.statut = 'en_attente'
    ORDER BY d.date_depart ASC
");
$demandes = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Gérer les demandes de trajets</title>
    <meta http-equiv="refresh" content="10">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 2rem;
            background: #f0faf4;
            color: #2e7d32;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #1b5e20;
        }

        ul {
            list-style: none;
            padding: 0;
            max-width: 700px;
            margin: auto;
        }

        li {
            background: white;
            margin-bottom: 1.5rem;
            padding: 1.2rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 128, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        li:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 128, 0, 0.2);
        }

        form {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        input[type="number"] {
            padding: 8px;
            border: 1px solid #a5d6a7;
            border-radius: 5px;
            width: 140px;
        }

        button {
            padding: 8px 14px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button[name="action"][value="accepter"] {
            background-color: #2e7d32;
            color: white;
        }

        button[name="action"][value="refuser"] {
            background-color: #c62828;
            color: white;
        }

        a {
            display: block;
            margin-top: 3rem;
            text-align: center;
            color: #2e7d32;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>📩 Demandes de trajets en attente</h1>

<?php if (empty($demandes)) : ?>
    <p style="text-align: center;">Aucune demande en attente pour le moment.</p>
<?php else : ?>
    <ul>
        <?php foreach ($demandes as $d) : ?>
            <li>
                <strong> <?= htmlspecialchars($d['depart']); ?> → <?= htmlspecialchars($d['arrivee']); ?></strong><br>
                📅 <em><?= $d['date_depart']; ?></em><br>
                👤 Demandeur : <strong><?= htmlspecialchars($d['pseudo']); ?></strong>

                <form action="../process/accepter_refuser_demande.php" method="POST">
                    <input type="hidden" name="demande_id" value="<?= $d['id']; ?>">
                    <input type="number" name="prix_propose" placeholder="Prix (crédits)" min="2" required>
                    <button type="submit" name="action" value="accepter">✅ Accepter</button>
                    <button type="submit" name="action" value="refuser">❌ Refuser</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="espace_chauffeur.php">← Retour à mon espace chauffeur</a>
</body>
</html>



