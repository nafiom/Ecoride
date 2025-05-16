<?php
session_start();
require_once '../config/database.php';
require_once '../config/env.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'chauffeur') {
    header("Location: ../connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM voitures WHERE user_id = ?");
$stmt->execute([$user_id]);
$voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);

$prefStmt = $pdo->prepare("SELECT COUNT(*) FROM preferences_conducteur WHERE utilisateur_id = ?");
$prefStmt->execute([$user_id]);
$preferencesSet = $prefStmt->fetchColumn() > 0;

if (empty($voitures) || !$preferencesSet) {
    echo "<h2 style='color:red;'>⚠️ Vous devez avoir au moins un véhicule <u>et</u> avoir défini vos préférences pour pouvoir ajouter un trajet.</h2>";
    if (empty($voitures)) echo "<p>🚘 <a href='ajouter_voiture.php'>Ajouter un véhicule</a></p>";
    if (!$preferencesSet) echo "<p>🛠️ <a href='preferences.php'>Définir mes préférences</a></p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $depart = trim($_POST['depart']);
    $arrivee = trim($_POST['arrivee']);
    $date_depart = $_POST['date_depart'];
    $date_arrivee = $_POST['date_arrivee'];
    $prix = (int) $_POST['prix'];
    $places = (int) $_POST['places'];
    $voiture_id = (int) $_POST['voiture_id'];

    if ($prix <= 0 || $places <= 0 || !$depart || !$arrivee) {
        echo "<p style='color:red;'>❌ Données invalides.</p>";
    } else {
        $voiture_query = $pdo->prepare("SELECT ecologique FROM voitures WHERE id = ? AND user_id = ?");
        $voiture_query->execute([$voiture_id, $user_id]);
        $voiture = $voiture_query->fetch();

        if (!$voiture) {
            echo "<p style='color:red;'>❌ Voiture non valide.</p>";
        } else {
            $ecologique = $voiture['ecologique'];

            $insert = $pdo->prepare("INSERT INTO covoiturages 
                (chauffeur_id, depart, arrivee, date_depart, date_arrivee, prix, places, voiture_id, ecologique)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $insert->execute([
                $user_id,
                htmlspecialchars($depart),
                htmlspecialchars($arrivee),
                $date_depart,
                $date_arrivee,
                $prix,
                $places,
                $voiture_id,
                $ecologique
            ]);

            echo "<p style='color:green;'>✅ Trajet ajouté avec succès !</p>";
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
    <title>Ajouter un trajet</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #e9f7ef, #d4edda);
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            animation: fadeIn 0.6s ease;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #2e7d32;
        }

        input[type="text"],
        input[type="datetime-local"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            border-color: #28a745;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .info {
            text-align: center;
            margin-top: 15px;
            color: #2e7d32;
        }

        a {
            color: #2e7d32;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🚗 Ajouter un nouveau trajet</h2>

    <form method="POST" id="trajetForm">
        <label for="depart">Adresse de départ :</label>
        <input id="depart" type="text" name="depart" required>

        <label for="arrivee">Adresse d'arrivée :</label>
        <input id="arrivee" type="text" name="arrivee" required>

        <label for="date_depart">Date et heure de départ :</label>
        <input type="datetime-local" id="date_depart" name="date_depart" required>

        <label for="date_arrivee">Date et heure d'arrivée (auto-calculée) :</label>
        <input type="datetime-local" id="date_arrivee" name="date_arrivee" readonly required>

        <label for="prix">Prix (€) :</label>
        <input type="number" name="prix" placeholder="Ex: 5" required>

        <label for="places">Nombre de places :</label>
        <input type="number" name="places" placeholder="Ex: 3" required>

        <label for="voiture_id">Voiture utilisée :</label>
        <select name="voiture_id" id="voiture_id" required>
            <?php foreach ($voitures as $voiture): ?>
                <option value="<?= $voiture['id']; ?>">
                    <?= htmlspecialchars($voiture['marque'] . " " . $voiture['modele'] . " - " . $voiture['immatriculation']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="button" onclick="calculerArriveeEtSoumettre()">Ajouter le trajet</button>
    </form>
</div>

<script>
function initAutocomplete() {
    const options = { types: ["address"], componentRestrictions: { country: "fr" } };
    new google.maps.places.Autocomplete(document.getElementById("depart"), options);
    new google.maps.places.Autocomplete(document.getElementById("arrivee"), options);
}

function calculerArriveeEtSoumettre() {
    const depart = document.getElementById("depart").value;
    const arrivee = document.getElementById("arrivee").value;
    const dateDepartStr = document.getElementById("date_depart").value;
    const form = document.getElementById("trajetForm");

    if (!depart || !arrivee || !dateDepartStr) {
        alert("Veuillez remplir tous les champs.");
        return;
    }

    const dateDepartObj = new Date(dateDepartStr);
    if (isNaN(dateDepartObj.getTime())) {
        alert("Format de date/heure invalide.");
        return;
    }

    const directionsService = new google.maps.DirectionsService();
    directionsService.route({
        origin: depart,
        destination: arrivee,
        travelMode: 'DRIVING'
    }, (response, status) => {
        if (status === 'OK') {
            const durationSec = response.routes[0].legs[0].duration.value;
            const arriveeDateObj = new Date(dateDepartObj.getTime() + durationSec * 1000);
            const yyyy = arriveeDateObj.getFullYear();
            const mm = String(arriveeDateObj.getMonth() + 1).padStart(2, '0');
            const dd = String(arriveeDateObj.getDate()).padStart(2, '0');
            const hh = String(arriveeDateObj.getHours()).padStart(2, '0');
            const min = String(arriveeDateObj.getMinutes()).padStart(2, '0');

            document.getElementById("date_arrivee").value = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
            setTimeout(() => form.submit(), 150);
        } else {
            alert("Erreur lors du calcul de la durée. Vérifiez les adresses.");
        }
    });
}
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= $_ENV['GOOGLE_MAPS_API_KEY'] ?>&libraries=places&callback=initAutocomplete&language=fr&region=FR">
</script>

</body>
</html>





