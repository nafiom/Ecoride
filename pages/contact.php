<?php
session_start();
require_once '../partials/navbar.php';

$success = $_SESSION['contact_success'] ?? null;
unset($_SESSION['contact_success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact – ECORIDE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e9f7ef, #d4edda);
            margin: 0;
            padding: 0;
        }

        .contact-container {
            max-width: 600px;
            margin: 60px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2e7d32;
            text-align: center;
            margin-bottom: 25px;
        }

        input, textarea, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 1rem;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .success {
            background: #d4edda;
            border-left: 5px solid #28a745;
            padding: 10px;
            color: #155724;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="contact-container">
    <h1>📩 Nous contacter</h1>

    <?php if (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="../process/envoyer_contact.php">
        <input type="text" name="nom" placeholder="Votre nom" required>
        <input type="email" name="email" placeholder="Votre email" required>
        <textarea name="message" rows="6" placeholder="Votre message" required></textarea>
        <button type="submit">Envoyer le message</button>
    </form>
</div>

</body>
</html>

