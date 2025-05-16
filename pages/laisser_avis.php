<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['trajet_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$trajet_id = (int) $_GET['trajet_id'];

$query = $pdo->prepare("
    SELECT r.statut, c.chauffeur_id
    FROM reservations r
    JOIN covoiturages c ON r.covoiturage_id = c.id
    WHERE r.utilisateur_id = ? AND r.covoiturage_id = ?
");
$query->execute([$user_id, $trajet_id]);
$reservation = $query->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo "❌ Vous n'avez pas réservé ce trajet.";
    exit();
}

if ($reservation['statut'] !== 'confirme') {
    echo "❌ Vous ne pouvez laisser un avis que sur un trajet confirmé.";
    exit();
}

if ($reservation['chauffeur_id'] == $user_id) {
    echo "❌ Vous êtes le chauffeur de ce trajet, vous ne pouvez pas laisser un avis.";
    exit();
}

$feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note = (int) $_POST['note'];
    $commentaire = trim($_POST['commentaire']);
    $chauffeur_id = $reservation['chauffeur_id'];

    if ($note < 1 || $note > 5) {
        $feedback = "<div class='error'>⚠️ La note doit être entre 1 et 5.</div>";
    } elseif (empty($commentaire)) {
        $feedback = "<div class='error'>⚠️ Veuillez entrer un commentaire.</div>";
    } else {
        $check = $pdo->prepare("SELECT id FROM avis WHERE passager_id = ? AND covoiturage_id = ?");
        $check->execute([$user_id, $trajet_id]);
        if ($check->fetch()) {
            $feedback = "<div class='error'>⚠️ Vous avez déjà laissé un avis pour ce trajet.</div>";
        } else {
            $insert = $pdo->prepare("
                INSERT INTO avis (conducteur_id, passager_id, covoiturage_id, note, commentaire, valide, signalement)
                VALUES (?, ?, ?, ?, ?, 0, 0)
            ");
            $insert->execute([$chauffeur_id, $user_id, $trajet_id, $note, $commentaire]);

            $feedback = "<div class='success'>✅ Votre avis a été soumis et sera validé sous peu !</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Laisser un avis</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f9f2;
            color: #2f4730;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
        }

        form {
            background: white;
            padding: 2rem;
            max-width: 600px;
            margin: 2rem auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,128,0,0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 0.5rem;
        }

        input[type="number"],
        textarea {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        button {
            background-color: #4caf50;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #388e3c;
        }

        .success {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            padding: 1rem;
            margin: 1rem auto;
            color: #155724;
            border-radius: 5px;
            max-width: 600px;
        }

        .error {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            padding: 1rem;
            margin: 1rem auto;
            color: #721c24;
            border-radius: 5px;
            max-width: 600px;
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

<h1>📝 Laisser un avis</h1>

<?= $feedback ?>

<form method="POST">
    <label for="note">Note (1 à 5) :</label>
    <input type="number" name="note" min="1" max="5" required>

    <label for="commentaire">Commentaire :</label>
    <textarea name="commentaire" rows="5" placeholder="Écrivez votre commentaire..." required></textarea>

    <button type="submit">Soumettre l'avis</button>
</form>

<a class="retour" href="historique.php">← Retour à l'historique</a>

</body>
</html>


