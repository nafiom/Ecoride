<?php
session_start();
require_once '../config/database.php';

$query = $pdo->query("
    SELECT a.note, a.commentaire, 
           passager.pseudo AS passager, 
           conducteur.id AS conducteur_id, 
           conducteur.pseudo AS conducteur,
           c.depart, c.arrivee
    FROM avis a
    JOIN utilisateurs passager ON a.passager_id = passager.id
    JOIN utilisateurs conducteur ON a.conducteur_id = conducteur.id
    JOIN covoiturages c ON a.covoiturage_id = c.id
    WHERE a.valide = 1
    ORDER BY conducteur.pseudo, a.id DESC
");
$avisList = $query->fetchAll(PDO::FETCH_ASSOC);

$chauffeurs = [];
foreach ($avisList as $avis) {
    $id = $avis['conducteur_id'];
    if (!isset($chauffeurs[$id])) {
        $chauffeurs[$id] = [
            'pseudo' => $avis['conducteur'],
            'avis' => [],
            'total' => 0,
            'count' => 0
        ];
    }
    $chauffeurs[$id]['avis'][] = $avis;
    $chauffeurs[$id]['total'] += $avis['note'];
    $chauffeurs[$id]['count']++;
    $chauffeurs[$id]['moyenne'] = round($chauffeurs[$id]['total'] / $chauffeurs[$id]['count'], 1);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis sur les Chauffeurs - ECORIDE</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #e8f5e9, #f1f8f4);
            margin: 0;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 2rem;
        }

        .chauffeur-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .chauffeur-header {
            background: #43a047;
            color: white;
            padding: 1rem 1.5rem;
            font-weight: bold;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: background 0.3s;
        }

        .chauffeur-header:hover {
            background: #388e3c;
        }

        .chauffeur-avis {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
            padding: 0 1.5rem;
            background-color: #f9fdf9;
        }

        .chauffeur-avis.open {
            padding: 1rem 1.5rem;
        }

        .avis-item {
            border-bottom: 1px solid #c8e6c9;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .avis-item:last-child {
            border-bottom: none;
        }

        .note-stars {
            color: #ffc107;
        }

        .empty-message {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 50px;
        }

        .stars {
            font-size: 14px;
            color: #fbc02d;
            margin-left: 10px;
        }

        @media screen and (max-width: 600px) {
            .chauffeur-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stars {
                margin: 5px 0 0 0;
            }
        }
    </style>
</head>
<body>

<?php include '../partials/navbar.php'; ?>

<h1>Avis sur les Chauffeurs</h1>

<?php if (empty($chauffeurs)) : ?>
    <div class="empty-message">Aucun avis validé pour l’instant.</div>
<?php else : ?>
    <?php foreach ($chauffeurs as $id => $chauffeur) : ?>
        <div class="chauffeur-box">
            <div class="chauffeur-header" onclick="toggleAvis('avis-<?= $id ?>')">
                <?= htmlspecialchars($chauffeur['pseudo']) ?>
                <div class="stars">
                    Moyenne : <strong><?= $chauffeur['moyenne'] ?>/5</strong>
                    <?= str_repeat('⭐', round($chauffeur['moyenne'])) ?>
                </div>
            </div>
            <div id="avis-<?= $id ?>" class="chauffeur-avis">
                <?php foreach ($chauffeur['avis'] as $avis) : ?>
                    <div class="avis-item">
                        <p><strong>Passager :</strong> <?= htmlspecialchars($avis['passager']) ?></p>
                        <p><strong>Trajet :</strong> <?= htmlspecialchars($avis['depart']) ?> → <?= htmlspecialchars($avis['arrivee']) ?></p>
                        <p><strong>Note :</strong> <span class="note-stars"><?= str_repeat('⭐', (int)$avis['note']) ?></span> (<?= $avis['note'] ?>/5)</p>
                        <p><strong>Commentaire :</strong> <?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
function toggleAvis(id) {
    const el = document.getElementById(id);
    if (el.classList.contains('open')) {
        el.style.maxHeight = null;
        el.classList.remove('open');
    } else {
        el.style.maxHeight = el.scrollHeight + 'px';
        el.classList.add('open');
    }
}
</script>

</body>
</html>





