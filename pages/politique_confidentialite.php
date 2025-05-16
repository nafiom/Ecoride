<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de confidentialité – ECORIDE</title>
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
    <h1>🔒 Politique de confidentialité</h1>

    <p>Chez ECORIDE, la protection de vos données est essentielle.</p>

    <p><strong>1. Données collectées :</strong> Nous collectons uniquement les données nécessaires à la gestion des trajets et à la mise en relation entre utilisateurs (nom, email, préférences de covoiturage, etc.).</p>

    <p><strong>2. Utilisation :</strong> Vos données sont utilisées uniquement pour le fonctionnement de la plateforme et ne sont en aucun cas vendues ou cédées à des tiers.</p>

    <p><strong>3. Sécurité :</strong> Vos informations sont stockées de manière sécurisée sur nos serveurs hébergés en France.</p>

    <p><strong>4. Droit d’accès :</strong> Vous pouvez à tout moment consulter, modifier ou supprimer vos données en vous connectant à votre espace utilisateur.</p>

    <p><strong>Contact :</strong> Pour toute question, écrivez-nous ici : <a href="/pages/contact.php"> contact</a>
</div>
</body>
</html>
