<?php
session_start();
require_once '../config/database.php'; 
$success_message = "";

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – ECORIDE</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/ecoride.js" defer></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1fdf2;
            margin: 0;
            padding: 2rem;
        }

        header h2 {
            text-align: center;
            color: #2e7d32;
        }

        .container {
            max-width: 400px;
            margin: 30px auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
        }

        .form-box h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #2e7d32;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        p {
            text-align: center;
        }

        a {
            color: #2e7d32;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <h2> Connectez-vous </h2>
</header>

<div class="container fade-in">
    <?php if (!empty($success_message)): ?>
        <div class="success">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <div class="form-box">
        <h2>Connexion</h2>
        <form action="../process/login.php" method="POST">
            <label for="email">Adresse email</label>
            <input type="email" name="email" id="email" placeholder="exemple@ecoride.fr" required>

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="Mot de passe" required>

            <button type="submit">Connexion</button>
        </form>

        <p style="margin-top: 15px;">
            Pas encore inscrit ? <a href="inscription.php">Créer un compte</a>
        </p>
    </div>
</div>

</body>
</html>



