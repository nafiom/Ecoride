<?php
include '../config/database.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$message = "";
$success = false;

if ($token && $email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND token = ?");
    $stmt->execute([$email, $token]);
    $user = $stmt->fetch();

    if ($user) {
        $update = $pdo->prepare("UPDATE utilisateurs SET valide = 1, token = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        $message = "✅ Compte activé avec succès ! Vous pouvez maintenant vous connecter.";
        $success = true;
    } else {
        $message = "❌ Lien invalide ou expiré.";
    }
} else {
    $message = "❌ Paramètres manquants ou invalides.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation du compte</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e9f7ef, #d4edda);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .message-box {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }

        .message-box h1 {
            font-size: 1.5rem;
            color: #2e7d32;
        }

        .message-box h1.error {
            color: #c82333;
        }

        .message-box a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .message-box a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="message-box">
    <h1 class="<?= $success ? '' : 'error' ?>"><?= htmlspecialchars($message) ?></h1>
    <a href="connexion.php">Se connecter</a>
</div>

</body>
</html>

