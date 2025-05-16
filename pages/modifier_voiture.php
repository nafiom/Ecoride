<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$voiture_id = $_GET['id'];

// Récupérer la voiture
$query = $pdo->prepare("SELECT * FROM voitures WHERE id = ? AND user_id = ?");
$query->execute([$voiture_id, $user_id]);
$voiture = $query->fetch();

if (!$voiture) {
    echo "Véhicule non trouvé.";
    exit();
}

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $immatriculation = $_POST['immatriculation'];
    $couleur = $_POST['couleur'];
    $date_immat = $_POST['date_immatriculation'];
    $places = (int)$_POST['places'];
    $ecologique = isset($_POST['ecologique']) ? 1 : 0;

    $query = $pdo->prepare("
        UPDATE voitures 
        SET marque = ?, modele = ?, immatriculation = ?, couleur = ?, date_immatriculation = ?, places = ?, ecologique = ? 
        WHERE id = ? AND user_id = ?
    ");
    $query->execute([$marque, $modele, $immatriculation, $couleur, $date_immat, $places, $ecologique, $voiture_id, $user_id]);

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Modifier le véhicule</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1fdf2;
            color: #2f4730;
            padding: 2rem;
        }
        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 1.5rem;
        }
        form {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        label,
        button {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        input:focus {
            border-color: #28a745;
            outline: none;
        }
        label {
            font-weight: bold;
            color: #333;
            margin-bottom: 0.4rem;
        }
        button {
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        button:hover {
            background-color: #218838;
        }
        .success {
            max-width: 600px;
            margin: 1rem auto;
            background-color: #d4edda;
            padding: 1rem;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            color: #155724;
            text-align: center;
        }
    </style>
</head>
<body>

<h2>✏️ Modifier le véhicule</h2>

<?php if ($success): ?>
    <div class="success">✅ Véhicule mis à jour avec succès.</div>
<?php endif; ?>

<form method="POST">
    <label>Marque :</label>
    <input type="text" name="marque" value="<?= htmlspecialchars($voiture['marque']) ?>" required>

    <label>Modèle :</label>
    <input type="text" name="modele" value="<?= htmlspecialchars($voiture['modele']) ?>" required>

    <label>Immatriculation :</label>
    <input type="text" name="immatriculation" value="<?= htmlspecialchars($voiture['immatriculation']) ?>" required>

    <label>Couleur :</label>
    <input type="text" name="couleur" value="<?= htmlspecialchars($voiture['couleur']) ?>" required>

    <label>Date de première immatriculation :</label>
    <input type="date" name="date_immatriculation" value="<?= $voiture['date_immatriculation'] ?>" required>

    <label>Nombre de places :</label>
    <input type="number" name="places" value="<?= $voiture['places'] ?>" min="1" required>

    <label><input type="checkbox" name="ecologique" <?= $voiture['ecologique'] ? 'checked' : '' ?>> Véhicule électrique (écologique)</label>

    <button type="submit">✅ Enregistrer</button>
</form>

</body>
</html>

