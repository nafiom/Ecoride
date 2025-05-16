<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;

// Définir le lien vers l'espace selon le rôle
$espaceLink = "/pages/connexion.php";

switch ($role) {
    case 'chauffeur':
        $espaceLink = "/pages/espace_chauffeur.php";
        break;
    case 'admin':
        $espaceLink = "/pages/espace_admin.php";
        break;
    case 'employe':
        $espaceLink = "/pages/espace_employe.php";
        break;
    case 'utilisateur':
        $espaceLink = "/pages/espace_utilisateur.php";
        break;
}
?>

<style>
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
</style>

<nav class="eco-navbar">
    <a href="/index.php">Accueil</a>
    <a href="/pages/recherche.php"> Rechercher</a>
    <a href="/pages/avis.php"> Avis</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?= $espaceLink ?>"> Mon espace</a>
        <a href="/process/logout.php">Déconnexion</a>
    <?php else: ?>
        <a href="/pages/connexion.php">Connexion</a>
        <a href="/pages/inscription.php"> Inscription</a>
    <?php endif; ?>

    <a href="/pages/contact.php">Contact</a>
</nav>



