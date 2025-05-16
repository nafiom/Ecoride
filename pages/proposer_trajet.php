<?php
session_start();
include '../config/database.php';
require_once '../config/env.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $depart = $_POST['depart'];
    $arrivee = $_POST['arrivee'];
    $date_depart = $_POST['date_depart'];

    $query = $pdo->prepare("INSERT INTO demandes_trajets (utilisateur_id, depart, arrivee, date_depart) VALUES (?, ?, ?, ?)");
    $query->execute([$_SESSION['user_id'], $depart, $arrivee, $date_depart]);

    echo "<div style='padding:20px; background:#d4edda; color:#155724; border-radius:8px; margin:2rem auto; max-width:600px; text-align:center;'>
            🚀 Votre demande de trajet a été soumise avec succès !<br><br>
            <a href='mes_demandes.php' style='color:#155724; font-weight:bold;'>➡️ Voir mes demandes</a>
          </div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Proposer un Trajet</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0fdf5;
            padding: 2rem;
            color: #2f3e46;
        }
        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 2rem;
        }
        form {
            background: #ffffff;
            max-width: 500px;
            margin: auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
        input, button {
            display: block;
            width: 100%;
            margin-bottom: 1.2rem;
            padding: 12px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #28a745;
            outline: none;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h1> Proposer un trajet</h1>

<form method="POST">
    <input type="text" id="depart" name="depart" placeholder="Ville de départ" required>
    <input type="text" id="arrivee" name="arrivee" placeholder="Ville d'arrivée" required>
    <input type="datetime-local" name="date_depart" required>
    <button type="submit">Envoyer ma demande</button>
</form>

<script>
function initAutocomplete() {
    const options = {
        types: ['geocode'],
        componentRestrictions: { country: "fr" }
    };
    new google.maps.places.Autocomplete(document.getElementById('depart'), options);
    new google.maps.places.Autocomplete(document.getElementById('arrivee'), options);
}
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= $_ENV['GOOGLE_MAPS_API_KEY'] ?>&libraries=places&callback=initAutocomplete&language=fr&region=FR">
</script>

</body>
</html>

