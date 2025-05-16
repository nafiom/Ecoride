<?php
session_start();
include '../config/database.php';
require_once '../config/env.php';

$results = [];
$userCredits = null;
$message = null;
$redirectToMesDemandes = false;
$depart = $arrivee = $date = $heureDepart = $heureArrivee = $maxPrix = $maxDuree = $minNote = '';
$ecoOnly = false;

if (isset($_SESSION['user_id'])) {
    $query = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = ?");
    $query->execute([$_SESSION['user_id']]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) $userCredits = $user['credits'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['depart'], $_POST['arrivee'], $_POST['date'], $_POST['heure_depart'], $_POST['heure_arrivee'])) {
    $depart = trim($_POST['depart']);
    $arrivee = trim($_POST['arrivee']);
    $date = $_POST['date'];
    $heureDepart = $_POST['heure_depart'];
    $heureArrivee = $_POST['heure_arrivee'];
    $maxPrix = $_POST['max_prix'] ?? '';
    $maxDuree = $_POST['max_duree'] ?? '';
    $minNote = $_POST['min_note'] ?? '';
    $ecoOnly = isset($_POST['ecologique']);

    $datetimeDepart = $date . ' ' . $heureDepart;
    $datetimeArrivee = $date . ' ' . $heureArrivee;

    $sql = "SELECT c.*, u.pseudo, u.photo,
                   v.marque, v.modele, v.immatriculation, v.ecologique,
                   TIMESTAMPDIFF(MINUTE, c.date_depart, c.date_arrivee) AS duree_minutes,
                   (SELECT ROUND(AVG(note),1) FROM avis WHERE conducteur_id = u.id AND valide = 1) AS note_conducteur
            FROM covoiturages c
            JOIN utilisateurs u ON c.chauffeur_id = u.id
            LEFT JOIN voitures v ON v.user_id = u.id
            WHERE c.depart = ? AND c.arrivee = ? AND c.date_depart >= ?";

    $params = [$depart, $arrivee, $datetimeDepart];

    if (!empty($maxPrix)) {
        $sql .= " AND c.prix <= ?";
        $params[] = $maxPrix;
    }

    if (!empty($maxDuree)) {
        $sql .= " AND TIMESTAMPDIFF(MINUTE, c.date_depart, c.date_arrivee) <= ?";
        $params[] = $maxDuree;
    }

    if (!empty($minNote)) {
        $sql .= " AND (SELECT AVG(note) FROM avis WHERE conducteur_id = u.id AND valide = 1) >= ?";
        $params[] = $minNote;
    }

    if ($ecoOnly) {
        $sql .= " AND v.ecologique = 1";
    }

    $query = $pdo->prepare($sql);
    $query->execute($params);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        if (isset($_SESSION['user_id'])) {
            $insert = $pdo->prepare("
                INSERT INTO demandes_trajets (utilisateur_id, depart, arrivee, date_depart, date_arrivee)
                VALUES (?, ?, ?, ?, ?)
            ");
            $insert->execute([
                $_SESSION['user_id'],
                $depart,
                $arrivee,
                $datetimeDepart,
                $datetimeArrivee
            ]);

            $message = "Aucun trajet trouvé, votre demande a été enregistrée. Redirection...";
            $redirectToMesDemandes = true;
        } else {
            $message = " Aucun trajet trouvé. Connectez-vous pour proposer votre trajet.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher un covoiturage</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0fdf4;
            margin: 0;
            padding: 2rem;
            color: #2e2e2e;
        }
        h1 {
            text-align: center;
            color: #28a745;
        }
        form {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            padding: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        form input, form select, form button, form label {
            padding: 0.75rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1 1 30%;
        }
        form button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #218838;
        }
        .ride {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            padding: 1.5rem;
        }
        .ride-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .ride-header img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 1rem;
        }
        .credit-info {
            text-align: right;
            font-weight: bold;
            color: #155724;
            margin-bottom: 1rem;
        }
        #map {
            height: 400px;
            width: 100%;
            margin-top: 2rem;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .estimation {
            font-weight: bold;
            color: #28a745;
        }
    </style>
    <?php if ($redirectToMesDemandes): ?>
        <meta http-equiv="refresh" content="2;url=mes_demandes.php">
    <?php endif; ?>
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<h1>🔎 Recherche de covoiturages</h1>

<?php if ($message): ?>
    <div style="background-color: #d4edda; border-left: 5px solid #28a745; padding: 1rem; margin-bottom: 1rem;">
        <?= $message ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="credit-info">
        Crédits disponibles : <?= htmlspecialchars($userCredits) ?> €
    </div>
<?php endif; ?>

<form method="POST" id="searchForm">
    <input id="depart" type="text" name="depart" placeholder="Adresse de départ" value="<?= htmlspecialchars($depart) ?>" required>
    <input id="arrivee" type="text" name="arrivee" placeholder="Adresse d'arrivée" value="<?= htmlspecialchars($arrivee) ?>" required>
    <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
    <input type="time" id="heure_depart" name="heure_depart" value="<?= htmlspecialchars($heureDepart) ?>" required>
    <input type="text" id="heure_arrivee" name="heure_arrivee" placeholder="Heure d'arrivée (auto)" value="<?= htmlspecialchars($heureArrivee) ?>" readonly required>
    <div class="estimation" id="estimation"><?= $heureArrivee ? "Durée estimée : calculée" : "" ?></div>
    <input type="number" name="max_prix" placeholder="Prix max (crédits)" value="<?= htmlspecialchars($maxPrix) ?>">
    <input type="number" name="max_duree" placeholder="Durée max (minutes)" value="<?= htmlspecialchars($maxDuree) ?>">
    <input type="number" name="min_note" placeholder="Note min. conducteur (1-5)" step="0.1" min="1" max="5" value="<?= htmlspecialchars($minNote) ?>">
    <label><input type="checkbox" name="ecologique" <?= $ecoOnly ? 'checked' : '' ?>> Trajets écologiques uniquement</label>
    <button type="button" onclick="calculerEtSoumettre()">Rechercher</button>
</form>

<?php if ($results): ?>
    <h2>Trajets disponibles</h2>
    <?php foreach ($results as $row): ?>
        <div class="ride">
            <div class="ride-header">
                <?php if (!empty($row['photo'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($row['photo']) ?>" alt="Photo du chauffeur">
                <?php endif; ?>
                <div>
                    <strong><?= htmlspecialchars($row['pseudo']) ?></strong> |
                    <?= htmlspecialchars($row['marque'] . ' ' . $row['modele']) ?> (<?= htmlspecialchars($row['immatriculation']) ?>)
                    <br>
                    Note : <?= $row['note_conducteur'] ? $row['note_conducteur'] . ' ⭐' : 'Non noté' ?> |
                    Écologique : <?= $row['ecologique'] ? 'oui' : 'non' ?>
                </div>
            </div>
            <p><strong><?= $row['depart'] ?></strong> → <strong><?= $row['arrivee'] ?></strong></p>
            <p>Départ : <?= $row['date_depart'] ?> | Arrivée : <?= $row['date_arrivee'] ?> | Durée : <?= $row['duree_minutes'] ?> min</p>
            <p>Prix : <?= $row['prix'] ?> € | Places disponibles : <?= $row['places'] ?></p>
            <?php if (isset($_SESSION['user_id']) && $userCredits >= $row['prix']): ?>
                <form action="../process/reserver_trajet.php" method="POST">
                    <input type="hidden" name="trajet_id" value="<?= $row['id'] ?>">
                    <button type="submit">Réserver</button>
                </form>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <p><a href="connexion.php">Connectez-vous</a> pour réserver.</p>
            <?php else: ?>
                <p style="color: red;"> Crédits insuffisants</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div id="map"></div>

<script>
function initMap() {
    const center = { lat: 46.603354, lng: 1.888334 };
    new google.maps.Map(document.getElementById("map"), {
        zoom: 6,
        center: center
    });
}

function initAutocomplete() {
    const options = {
        types: ["address"],
        componentRestrictions: { country: "fr" }
    };
    new google.maps.places.Autocomplete(document.getElementById("depart"), options);
    new google.maps.places.Autocomplete(document.getElementById("arrivee"), options);
}

function calculerEtSoumettre() {
    const origin = document.getElementById("depart").value;
    const destination = document.getElementById("arrivee").value;
    const heureDepart = document.getElementById("heure_depart").value;
    const heureArriveeInput = document.getElementById("heure_arrivee");
    const estimationDiv = document.getElementById("estimation");
    const form = document.getElementById("searchForm");

    if (!origin || !destination || !heureDepart) {
        alert("Veuillez remplir tous les champs.");
        return;
    }

    const [h, m] = heureDepart.split(":");
    const baseTime = new Date();
    baseTime.setHours(h, m, 0, 0);

    const directionsService = new google.maps.DirectionsService();
    directionsService.route({
        origin,
        destination,
        travelMode: 'DRIVING'
    }, (response, status) => {
        if (status === "OK") {
            const durationSec = response.routes[0].legs[0].duration.value;
            const durationText = response.routes[0].legs[0].duration.text;
            const arrivee = new Date(baseTime.getTime() + durationSec * 1000);
            const hh = String(arrivee.getHours()).padStart(2, '0');
            const mm = String(arrivee.getMinutes()).padStart(2, '0');
            heureArriveeInput.value = `${hh}:${mm}`;
            estimationDiv.textContent = `Durée estimée : ${durationText}`;
            setTimeout(() => form.submit(), 100);
        } else {
            alert("Erreur lors du calcul de la durée du trajet.");
        }
    });
}
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= $_ENV['GOOGLE_MAPS_API_KEY'] ?>&libraries=places&callback=initAutocomplete&language=fr&region=FR">
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= $_ENV['GOOGLE_MAPS_API_KEY'] ?>&callback=initMap&language=fr&region=FR">
</script>
</body>
</html>
















