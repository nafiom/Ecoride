<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions légales – ECORIDE</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0fdf4;
            padding: 2rem;
            color: #2e7d32;
        }
        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        p {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<?php include '../partials/navbar.php'; ?>
<div class="container">
    <h1>📄 Mentions légales</h1>

    <p><strong>Nom de l’entreprise :</strong> EcoRide</p>
    <p><strong>Forme juridique :</strong> Société par actions simplifiée (SAS)</p>
    <p><strong>Adresse :</strong> 12 Rue de l'Écologie, 75000 Paris, France</p>
    <p><strong>Directeur de publication :</strong> José – Directeur technique</p>

    <p>Le site ECORIDE a pour vocation de mettre en relation des utilisateurs dans le cadre d’un service de covoiturage écologique et solidaire.</p>
</div>
</body>
</html>
