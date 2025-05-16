<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employe') {
    header("Location: connexion.php");
    exit();
}


$query = $pdo->prepare("
    SELECT a.id, a.note, a.commentaire, u.pseudo AS auteur, c.depart, c.arrivee
    FROM avis a
    JOIN utilisateurs u ON a.passager_id = u.id
    JOIN covoiturages c ON a.covoiturage_id = c.id
    WHERE a.valide = 0
");
$query->execute();
$avis = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Espace Employé - Validation des Avis</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1fdf5;
            margin: 0;
            padding: 2rem;
            color: #2e7d32;
        }

        h1 {
            text-align: center;
            color: #1b5e20;
        }

        .avis-box {
            background: #ffffff;
            border: 1px solid #c8e6c9;
            border-radius: 10px;
            padding: 20px;
            margin: 1rem auto;
            max-width: 700px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .avis-box:hover {
            transform: translateY(-4px);
        }

        p {
            margin: 0.5rem 0;
        }

        form {
            margin-top: 10px;
        }

        button {
            padding: 8px 16px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button[name="action"][value="valider"] {
            background-color: #2e7d32;
            color: white;
        }

        button[name="action"][value="valider"]:hover {
            background-color: #1b5e20;
        }

        button[name="action"][value="refuser"] {
            background-color: #c62828;
            color: white;
        }

        button[name="action"][value="refuser"]:hover {
            background-color: #b71c1c;
        }

        .no-avis {
            text-align: center;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>

<h1>Validation des Avis</h1>

<?php if (empty($avis)) : ?>
    <p class="no-avis">Aucun avis à valider pour le moment.</p>
<?php else : ?>
    <?php foreach ($avis as $av) : ?>
        <div class="avis-box">
            <p><strong><?= htmlspecialchars($av['auteur']) ?></strong> a noté le trajet <strong><?= htmlspecialchars($av['depart']) ?> → <?= htmlspecialchars($av['arrivee']) ?></strong> avec <strong><?= $av['note'] ?>/5</strong></p>
            <p><em>"<?= htmlspecialchars($av['commentaire']) ?>"</em></p>
            <form method="POST" action="../process/valider_avis.php">
                <input type="hidden" name="avis_id" value="<?= $av['id'] ?>">
                <button type="submit" name="action" value="valider">✅ Valider</button>
                <button type="submit" name="action" value="refuser">❌ Refuser</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>


