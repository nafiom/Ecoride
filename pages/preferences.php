<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'chauffeur') {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$preferences = ['Fumeur', 'Animaux', 'Musique'];

// Récupérer les préférences existantes
$existingPrefsQuery = $pdo->prepare("SELECT type_pref, valeur FROM preferences_conducteur WHERE utilisateur_id = ?");
$existingPrefsQuery->execute([$user_id]);
$existingPrefs = $existingPrefsQuery->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Préférences Conducteur</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0fdf5;
            padding: 2rem;
            color: #2c3e50;
        }
        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 2rem;
        }
        form {
            background: #fff;
            max-width: 500px;
            margin: auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }
        label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f5f5f5;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 6px;
            transition: background 0.3s;
        }
        label:hover {
            background: #e0f7ea;
        }
        input[type="checkbox"] {
            transform: scale(1.2);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        button:hover {
            background-color: #218838;
        }
        .legend {
            text-align: center;
            font-size: 0.9rem;
            margin-bottom: 20px;
            color: #555;
        }
    </style>
</head>
<body>

<h2>⚙️ Mes préférences de conduite</h2>
<div class="legend">✅ Coché = Autorisé | ⬜ Non coché = Non autorisé</div>

<form action="../process/sauvegarder_preferences.php" method="post">
    <?php foreach ($preferences as $pref): ?>
        <label>
            <?= $pref ?> autorisé
            <input type="checkbox" name="prefs[<?= $pref ?>]" value="1" <?= isset($existingPrefs[$pref]) && $existingPrefs[$pref] ? 'checked' : '' ?>>
        </label>
    <?php endforeach; ?>
    <button type="submit">💾 Sauvegarder mes préférences</button>
</form>

</body>
</html>


