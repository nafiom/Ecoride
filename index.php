<?php
session_start();
include 'config/database.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECORIDE - Covoiturage Responsable</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="/assets/js/ecoride.js" defer></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1fdf2;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #28a745;
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .alert-logout {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 1rem auto;
            border-radius: 5px;
            text-align: center;
            width: 80%;
            max-width: 600px;
            transition: opacity 0.5s ease-out;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.6s ease-in-out;
        }

        .container h2 {
            color: #28a745;
        }

        nav.eco-navbar {
            background: #28a745;
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            flex-wrap: wrap;
        }

        nav.eco-navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s, transform 0.2s;
        }

        nav.eco-navbar a:hover {
            color: #dfffe1;
            transform: translateY(-2px);
        }

        footer {
            text-align: center;
            font-size: 0.9rem;
            margin: 2rem auto;
            color: #777;
        }

        footer a {
            color: #2e7d32;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header>
    <h1>Bienvenue sur <span style="color: #dfffe1;">ECORIDE</span></h1>
    <p>🌱 Le covoiturage éco-responsable et solidaire</p>
</header>

<?php if (isset($_GET['logout']) && $_GET['logout'] == 1): ?>
    <div id="logoutMessage" class="alert-logout">
        ✅ Vous êtes maintenant déconnecté.
    </div>
    <script>
        setTimeout(function() {
            const alert = document.getElementById('logoutMessage');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => { alert.remove(); }, 500);
            }
        }, 5000);
    </script>
<?php endif; ?>
<?php if (isset($_GET['account_deleted'])): ?>
<div class="alert-logout">
    ✅ Votre compte a été supprimé avec succès.
</div>
<?php endif; ?>


<nav class="eco-navbar">
    <a href="/index.php">Accueil</a>
    <a href="/pages/recherche.php">Rechercher</a>
    <a href="/pages/avis.php">Avis</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/pages/espace_utilisateur.php">Mon espace</a>
        <a href="/process/logout.php">Déconnexion</a>
    <?php else: ?>
        <a href="/pages/connexion.php">Connexion</a>
        <a href="/pages/inscription.php">Inscription</a>
    <?php endif; ?>

    <a href="/pages/contact.php">Contact</a>
</nav>

<div class="container">
    <h2>🌍 Une plateforme pensée pour l'environnement</h2>
    <p>La startup EcoRide, récemment fondée en France, 
    s’est donné pour mission de réduire l’impact environnemental des déplacements en favorisant activement le covoiturage entre particuliers. À travers une vision claire et engagée, EcoRide promeut une mobilité plus verte, plus solidaire et accessible à tous.</p>

    <p> Lancée sous l’impulsion de José, directeur technique de l’entreprise, la plateforme web a été pensée comme un outil moderne, simple et intuitif, destiné à mettre en relation conducteurs et passagers partageant les mêmes trajets.
    </p>

    <p>EcoRide ambitionne de devenir la référence du covoiturage écologique en France, en s’adressant en priorité aux voyageurs soucieux de leur empreinte carbone, mais également à ceux qui recherchent une alternative économique, pratique et fiable pour leurs déplacements.</p>
<p>Il est important de souligner que la plateforme est exclusivement dédiée aux déplacements en voiture, garantissant une expérience ciblée, cohérente et sécurisée.

</p>
    <img src="assets/image1.jpg" alt="Covoiturage" style="max-width: 100%; border-radius: 10px; margin-top: 1rem;">
</div>

<footer>
    <p>&copy; <?= date('Y') ?> EcoRide – Tous droits réservés.</p>
    <p>
        <a href="/pages/mentions_legales.php">Mentions légales</a> |
        <a href="/pages/politique_confidentialite.php">Politique de confidentialité</a>
    </p>
</footer>

</body>
</html>




