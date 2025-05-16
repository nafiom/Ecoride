<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $immatriculation = trim($_POST['immatriculation'] ?? '');
    $couleur = trim($_POST['couleur'] ?? '');
    $date_immat = $_POST['date_immatriculation'] ?? '';
    $places = isset($_POST['places']) ? (int)$_POST['places'] : 0;
    $ecologique = isset($_POST['ecologique']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    if (!$marque || !$modele || !$immatriculation || !$couleur || !$date_immat || $places < 1) {
        $error = "❌ Tous les champs sont obligatoires et doivent être valides.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO voitures (user_id, marque, modele, immatriculation, couleur, date_immatriculation, places, ecologique) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            htmlspecialchars($marque),
            htmlspecialchars($modele),
            htmlspecialchars($immatriculation),
            htmlspecialchars($couleur),
            $date_immat,
            $places,
            $ecologique
        ]);
        $success = "✅ Véhicule ajouté avec succès !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Ajouter un véhicule</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #e9f7ef, #d4edda);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            margin-top: 15px;
            color: #2e7d32;
            display: block;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #28a745;
            outline: none;
        }

        .checkbox {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .checkbox input {
            margin-right: 10px;
            transform: scale(1.2);
        }

        button {
            width: 100%;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>➕ Ajouter un véhicule</h2>

    <?php if (!empty($success)) : ?>
        <div class="message success"><?= $success ?></div>
    <?php elseif (!empty($error)) : ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="marque">Marque</label>
        <input type="text" name="marque" id="marque" required>

        <label for="modele">Modèle</label>
        <input type="text" name="modele" id="modele" required>

        <label for="immatriculation">Immatriculation</label>
        <input type="text" name="immatriculation" id="immatriculation" required>

        <label for="couleur">Couleur</label>
        <input type="text" name="couleur" id="couleur" required>

        <label for="date_immatriculation">Date de première immatriculation</label>
        <input type="date" name="date_immatriculation" id="date_immatriculation" required>

        <label for="places">Nombre de places</label>
        <input type="number" name="places" id="places" min="1" required>

        <div class="checkbox">
            <input type="checkbox" name="ecologique" id="ecologique">
            <label for="ecologique">Véhicule électrique (écologique)</label>
        </div>

        <button type="submit">Ajouter le véhicule</button>
    </form>
</div>

</body>
</html>



