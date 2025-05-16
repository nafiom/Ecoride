<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'chauffeur' && $_SESSION['role'] !== 'admin')) {
    header("Location: connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'utilisateur';


$query = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);


$credits = $user['credits'];


$query = $pdo->prepare("SELECT * FROM voitures WHERE user_id = ?");
$query->execute([$user_id]);
$voitures = $query->fetchAll();
$hasVoiture = count($voitures) > 0;


$query = $pdo->prepare("SELECT COUNT(*) FROM preferences_conducteur WHERE utilisateur_id = ?");
$query->execute([$user_id]);
$hasPreferences = $query->fetchColumn() > 0;


$notifQuery = $pdo->prepare("SELECT COUNT(*) FROM demandes_trajets WHERE statut = 'en_attente'");
$notifQuery->execute();
$pendingDemands = $notifQuery->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../partials/navbar.php'; ?>
    <title>Espace Chauffeur</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0fdf4;
            margin: 0;
            padding: 2rem;
            color: #333;
        }
        h1, h2 {
            text-align: center;
            color: #2e7d32;
        }
        .box {
            background: #fff;
            border: 1px solid #c8e6c9;
            padding: 1.5rem;
            margin: 1rem auto;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            max-width: 800px;
        }
        .alert {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .btn-action {
            margin-top: 10px;
            padding: 10px 16px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-action:hover {
            background-color: #1b5e20;
        }
        .btn-danger {
            background-color: #c62828;
        }
        .btn-danger:hover {
            background-color: #b71c1c;
        }
        a {
            display: inline-block;
            margin: 8px 0;
            color: #2e7d32;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .photo-profil {
            text-align: center;
            margin-bottom: 1rem;
        }
        .photo-profil img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #2e7d32;
        }
    </style>
</head>
<body>

<?php if (isset($_GET['error']) && $_GET['error'] === 'suppression'): ?>
    <div class="alert">
        ❌ Une erreur est survenue lors de la suppression de votre compte. Veuillez réessayer ou contacter le support.
    </div>
<?php endif; ?>

<h1>Mon espace chauffeur</h1>

<div class="box" style="text-align:center;">
    <h2>Crédits disponibles</h2>
    <p style="font-size: 1.8rem; font-weight: bold; color: #28a745;"><?= $credits ?> €</p>
</div>

<div class="box">
    <h2>Photo de profil</h2>
    <div class="photo-profil">
        <?php if (!empty($user['photo'])): ?>
            <img src="../uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil">
            <form action="../process/upload_photo.php" method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                <input type="file" name="photo" accept="image/*" required>
                <button type="submit" class="btn-action">Mettre à jour la photo</button>
            </form>
            <form method="POST" action="../process/supprimer_photo.php">
                <button type="submit" class="btn-action btn-danger">Supprimer la photo</button>
            </form>
        <?php else: ?>
            <form action="../process/upload_photo.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="photo" accept="image/*" required>
                <button type="submit" class="btn-action">Choisir une photo</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="box">
    <h2>Vos véhicules</h2>
    <?php if ($hasVoiture): ?>
        <?php foreach ($voitures as $voiture): ?>
            <div style="margin-bottom:1rem;">
                <strong><?= htmlspecialchars($voiture['marque']) ?> <?= htmlspecialchars($voiture['modele']) ?></strong><br>
                Immatriculation : <?= htmlspecialchars($voiture['immatriculation']) ?><br>
                Couleur : <?= htmlspecialchars($voiture['couleur']) ?><br>
                Places : <?= $voiture['places'] ?><br>
                Écologique : <?= $voiture['ecologique'] ? 'Oui' : 'Non' ?><br>
                <a href="modifier_voiture.php?id=<?= $voiture['id'] ?>">✏️ Modifier</a>
                <form action="../process/supprimer_voiture.php" method="POST" onsubmit="return confirm('Supprimer ce véhicule ?');">
                    <input type="hidden" name="voiture_id" value="<?= $voiture['id'] ?>">
                    <button type="submit" class="btn-action btn-danger">🗑️ Supprimer</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="alert">⚠️ Vous devez ajouter au moins un véhicule.</p>
    <?php endif; ?>
</div>

<div class="box">
    <h2>⚙️ Vos préférences</h2>
    <?php if ($hasPreferences): ?>
        <p>✅ Préférences enregistrées. <a href="preferences.php">Modifier</a></p>
    <?php else: ?>
        <p class="alert">⚠️ Vous devez <a href="preferences.php">renseigner vos préférences</a>.</p>
    <?php endif; ?>
</div>

<div class="box">
    <h2>Vos trajets proposés</h2>
    <?php
    $query = $pdo->prepare("SELECT * FROM covoiturages WHERE chauffeur_id = ?");
    $query->execute([$user_id]);
    $trajets = $query->fetchAll();

    if ($trajets):
        foreach ($trajets as $trajet): ?>
            <div style="margin-bottom: 1rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
                <strong><?= htmlspecialchars($trajet['depart']) ?> → <?= htmlspecialchars($trajet['arrivee']) ?></strong><br>
                Départ : <?= $trajet['date_depart'] ?><br>
                Prix : <?= $trajet['prix'] ?> crédits<br>
                Places restantes : <?= $trajet['places'] ?><br>
                Écologique : <?= $trajet['ecologique'] ? 'oui' : 'non' ?><br>
                Statut : <strong><?= ucfirst($trajet['statut'] ?? 'en_attente') ?></strong><br>

                <?php if ($trajet['statut'] === 'en_attente'): ?>
                    <form method="POST" action="../process/modifier_statut_covoiturage.php" style="display:inline-block;">
                        <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
                        <input type="hidden" name="action" value="demarrer">
                        <button type="submit" class="btn-action">▶️ Démarrer</button>
                    </form>
                    <form method="POST" action="../process/modifier_statut_covoiturage.php" style="display:inline-block;" onsubmit="return confirm('Annuler ce trajet ?')">
                        <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
                        <input type="hidden" name="action" value="annuler">
                        <button type="submit" class="btn-action btn-danger">❌ Annuler course</button>
                    </form>
                <?php elseif ($trajet['statut'] === 'en_cours'): ?>
                    <form method="POST" action="../process/modifier_statut_covoiturage.php">
                        <input type="hidden" name="covoiturage_id" value="<?= $trajet['id'] ?>">
                        <input type="hidden" name="action" value="terminer">
                        <button type="submit" class="btn-action">✅ Arrivée à destination</button>
                    </form>
                <?php elseif ($trajet['statut'] === 'termine'): ?>
                    <p>🛑 Trajet terminé.</p>
                <?php endif; ?>
            </div>
        <?php endforeach;
    else:
        echo "<p>Vous n'avez pas encore proposé de trajet.</p>";
    endif;
    ?>
</div>

<div class="box" style="text-align:center;">
    <a href="ajouter_voiture.php">Ajouter un véhicule</a> |
    <a href="historique.php">Historique</a> |
    <a href="gestion_reservations.php">Réservations</a> |
    <a href="gerer_demandes.php">Demandes 
        <?php if ($pendingDemands > 0): ?>
            <span style="color:red;">🔔 (<?= $pendingDemands ?>)</span>
        <?php endif; ?>
    </a> |
    <a href="../process/logout.php">Déconnexion</a>

    <div style="margin-top: 1rem;">
        <?php if ($role === 'chauffeur'): ?>
            <p>Vous êtes actuellement chauffeur.</p>
            <p><a href="../process/ne_plus_etre_chauffeur.php">🚫 Ne plus être chauffeur</a></p>
        <?php endif; ?>

        <form method="POST" action="../process/supprimer_compte.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
            <input type="hidden" name="delete_account" value="1">
            <button type="submit" class="btn-action btn-danger">🗑️ Supprimer mon compte</button>
        </form>
    </div>
</div>

</body>
</html>










