<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Marquer les notifications comme vues
$pdo->prepare("
    UPDATE demandes_trajets 
    SET notification_vue = 1 
    WHERE utilisateur_id = ? 
    AND statut IN ('accepte', 'refuse') 
    AND notification_vue = 0
")->execute([$user_id]);

// Récupérer les demandes enrichies
$query = $pdo->prepare("
    SELECT d.*, 
           u.pseudo AS chauffeur_pseudo, u.photo AS chauffeur_photo,
           v.marque, v.modele, v.immatriculation, v.ecologique,
           (SELECT ROUND(AVG(note),1) FROM avis WHERE conducteur_id = d.chauffeur_id AND valide = 1) AS moyenne_note
    FROM demandes_trajets d
    LEFT JOIN utilisateurs u ON d.chauffeur_id = u.id
    LEFT JOIN voitures v ON v.user_id = d.chauffeur_id
    WHERE d.utilisateur_id = ?
    ORDER BY d.date_depart DESC
");
$query->execute([$user_id]);
$demandes = $query->fetchAll(PDO::FETCH_ASSOC);

// Préférences des chauffeurs
$chauffeurPreferences = [];
$chauffeurIds = array_filter(array_unique(array_column($demandes, 'chauffeur_id')));
if (!empty($chauffeurIds)) {
    $placeholders = implode(',', array_fill(0, count($chauffeurIds), '?'));
    $prefsQuery = $pdo->prepare("SELECT utilisateur_id, type_pref, valeur FROM preferences_conducteur WHERE utilisateur_id IN ($placeholders)");
    $prefsQuery->execute(array_values($chauffeurIds));
    while ($row = $prefsQuery->fetch(PDO::FETCH_ASSOC)) {
        $chauffeurPreferences[$row['utilisateur_id']][$row['type_pref']] = $row['valeur'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Mes demandes de trajets</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f2fdf4; padding: 2rem; color: #2c5e3f; }
        h1 { text-align: center; color: #2e7d32; margin-bottom: 2rem; }
        ul { list-style: none; padding: 0; }
        li { background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 1.5rem; transition: transform 0.2s ease; }
        li:hover { transform: translateY(-3px); }
        .info { margin: 0.4rem 0; }
        .chauffeur-info { display: flex; align-items: center; margin: 0.5rem 0; gap: 12px; }
        .chauffeur-photo { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #28a74544; }
        button { background-color: #28a745; color: white; padding: 10px 18px; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s ease; margin-top: 0.7rem; }
        button:hover { background-color: #218838; }
        .green-check { color: #28a745; font-weight: bold; }
        a { display: block; text-align: center; margin-top: 2rem; text-decoration: none; font-weight: bold; color: #2e7d32; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h1>Mes demandes de trajets</h1>

<?php if (empty($demandes)) : ?>
    <p style="text-align:center;">Vous n'avez pas encore proposé de trajet.</p>
<?php else : ?>
    <ul>
        <?php foreach ($demandes as $d) : ?>
            <li>
                <div class="info"><strong>Trajet :</strong> <?= htmlspecialchars($d['depart']); ?> → <?= htmlspecialchars($d['arrivee']); ?></div>
                <div class="info"><strong>Date de départ :</strong> <?= $d['date_depart']; ?></div>
                <?php if (!empty($d['date_arrivee'])): ?>
                    <div class="info"><strong>Date d’arrivée :</strong> <?= $d['date_arrivee']; ?></div>
                <?php endif; ?>
                <div class="info"><strong>Statut :</strong> <?= ucfirst($d['statut']); ?></div>

                <?php
                    $reservationExists = false;
                    if (!empty($d['id']) && !empty($d['covoiturage_id'])) {
                        $resCheck = $pdo->prepare("SELECT id FROM reservations WHERE utilisateur_id = ? AND covoiturage_id = ?");
                        $resCheck->execute([$user_id, $d['covoiturage_id']]);
                        $reservationExists = $resCheck->fetch();
                    }
                ?>

                <?php if ($d['statut'] === 'accepte' && !empty($d['chauffeur_pseudo']) && !$reservationExists) : ?>
                    <div class="chauffeur-info">
                        <?php if (!empty($d['chauffeur_photo'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($d['chauffeur_photo']); ?>" alt="Photo du chauffeur" class="chauffeur-photo">
                        <?php endif; ?>
                        <div><strong>Chauffeur :</strong> <?= htmlspecialchars($d['chauffeur_pseudo']); ?></div>
                    </div>
                    <div class="info"><strong>Prix proposé :</strong> <?= htmlspecialchars($d['prix_propose']); ?> crédits</div>
                    <div class="info"><strong>Note du chauffeur :</strong> <?= $d['moyenne_note'] ? $d['moyenne_note'].' / 5 ⭐' : 'Non noté' ?></div>
                    <div class="info"><strong>Voiture :</strong> <?= htmlspecialchars($d['marque'].' '.$d['modele']); ?> - <?= htmlspecialchars($d['immatriculation']); ?></div>
                    <div class="info"><strong>Écologique :</strong> <?= $d['ecologique'] ? 'Oui' : 'Non' ?></div>

                    <?php
                        $prefs = $chauffeurPreferences[$d['chauffeur_id']] ?? [];
                        foreach (['Fumeur', 'Animaux', 'Musique'] as $pref) {
                            $valeur = isset($prefs[$pref]) ? ($prefs[$pref] ? 'Oui' : 'Non') : 'Non spécifié';
                            echo "<div class='info'><strong>$pref :</strong> $valeur</div>";
                        }
                    ?>

                    <form id="form-demande-<?= $d['id'] ?>" action="../process/reserver_trajet_demande.php" method="POST">
                        <input type="hidden" name="demande_id" value="<?= $d['id']; ?>">
                        <button type="button" onclick="confirmerReservation(<?= $d['id'] ?>, <?= $d['prix_propose'] ?>)">Réserver ce trajet accepté</button>
                    </form>

                <?php elseif ($reservationExists): ?>
                    <p class="green-check">✅ Trajet déjà réservé</p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="recherche.php">← Retour à la recherche</a>

<script>
function confirmerReservation(id, prix) {
    if (confirm("Ce trajet coûte " + prix + " crédits. Confirmez-vous votre réservation ?")) {
        document.getElementById("form-demande-" + id).submit();
    }
}
</script>

</body>
</html>








