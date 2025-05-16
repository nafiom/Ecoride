<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}


$queryCovoiturages = $pdo->query("SELECT COUNT(*) as total, DATE(date_depart) as jour FROM covoiturages GROUP BY jour ORDER BY jour DESC");
$covoiturages = $queryCovoiturages->fetchAll(PDO::FETCH_ASSOC);

$queryCredits = $pdo->query("SELECT SUM(prix) as total, DATE(date_depart) as jour FROM covoiturages GROUP BY jour ORDER BY jour DESC");
$credits = $queryCredits->fetchAll(PDO::FETCH_ASSOC);

$queryTotalCredits = $pdo->query("SELECT SUM(prix) as total FROM covoiturages");
$totalCredits = $queryTotalCredits->fetch(PDO::FETCH_ASSOC)['total'];


$queryUtilisateurs = $pdo->query("SELECT id, pseudo, email, role, suspendu FROM utilisateurs");
$utilisateurs = $queryUtilisateurs->fetchAll(PDO::FETCH_ASSOC);


$queryAvisSignales = $pdo->query("SELECT * FROM avis WHERE signalement = 1");
$avisSignales = $queryAvisSignales->fetchAll(PDO::FETCH_ASSOC);


$allowedActions = ['suspendu', 'reactive'];
$successMessage = '';
if (isset($_GET['action']) && in_array($_GET['action'], $allowedActions)) {
    $successMessage = $_GET['action'] === 'suspendu' ? '✅ Utilisateur suspendu avec succès.' : '✅ Utilisateur réactivé avec succès.';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Espace Administrateur - ECORIDE</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #e8f5e9, #f1f8f4);
            margin: 0;
            padding: 2rem;
        }
        h1, h2, h3 { color: #2e7d32; }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        ul { padding-left: 20px; }
        li { margin-bottom: 10px; }
        .suspendu { color: red; font-weight: bold; margin-left: 10px; }
        button {
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        form { display: inline; }
        .suspend-btn { background-color: #dc3545; color: white; }
        .reactiver-btn { background-color: #28a745; color: white; }
        .avis-box {
            border: 1px solid #c8e6c9;
            background: #f9fdf9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<h1>📊 Tableau de bord Administrateur</h1>

<?php if (!empty($successMessage)) : ?>
    <div class="success"><?= htmlspecialchars($successMessage) ?></div>
<?php endif; ?>

<div class="card">
    <h2>📈 Statistiques</h2>
    <p><strong>Total des crédits gagnés :</strong> <?= htmlspecialchars($totalCredits ?? 0) ?> €</p>

    <h3>🚘 Covoiturages par jour</h3>
    <ul>
        <?php foreach ($covoiturages as $cov) : ?>
            <li><?= htmlspecialchars($cov['jour']) ?> : <strong><?= htmlspecialchars($cov['total']) ?></strong> covoiturages</li>
        <?php endforeach; ?>
    </ul>

    <h3>💰 Crédits gagnés par jour</h3>
    <ul>
        <?php foreach ($credits as $cred) : ?>
            <li><?= htmlspecialchars($cred['jour']) ?> : <strong><?= htmlspecialchars($cred['total']) ?></strong> crédits</li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="card">
    <h2>👥 Gestion des Utilisateurs</h2>
    <ul>
        <?php foreach ($utilisateurs as $user) : ?>
            <li>
                <?= htmlspecialchars($user['pseudo']) ?> (<?= htmlspecialchars($user['role']) ?>)
                <?php if ($user['suspendu']) : ?>
                    <span class="suspendu">[Suspendu]</span>
                    <form method="POST" action="../process/suspendre_utilisateur.php">
                        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                        <input type="hidden" name="action" value="unsuspend">
                        <button type="submit" class="reactiver-btn">✅ Réactiver</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="../process/suspendre_utilisateur.php">
                        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                        <input type="hidden" name="action" value="suspend">
                        <button type="submit" class="suspend-btn">❌ Suspendre</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="card">
    <h2>🚨 Avis signalés</h2>
    <?php if (empty($avisSignales)) : ?>
        <p>Aucun avis signalé.</p>
    <?php else : ?>
        <?php foreach ($avisSignales as $avis) : ?>
            <div class="avis-box">
                <p><strong>Avis #<?= htmlspecialchars($avis['id']) ?></strong> — Note : <?= (int)$avis['note'] ?>/5</p>
                <p><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>





