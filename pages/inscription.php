<?php
if (isset($_GET['error']) && $_GET['error'] === 'exists') {
    $errorMsg = "Un compte avec cet email existe déjà.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ECORIDE</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e9f7ef, #d4edda);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 450px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: #28a745;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .eye-toggle {
            position: relative;
        }

        .eye-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
            color: #666;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2e7d32;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <form id="registerForm" action="../process/register.php" method="POST" onsubmit="return validatePassword();">
        <h2>Créer un compte</h2>

        <?php if (!empty($errorMsg)): ?>
            <div class="error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <input type="text" name="pseudo" placeholder="Pseudo" required>
        <input type="email" name="email" placeholder="Adresse email" required>

        <div class="eye-toggle">
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            <span class="eye-toggle-icon" onclick="togglePassword('password')">👁️</span>
        </div>

        <div class="eye-toggle">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
            <span class="eye-toggle-icon" onclick="togglePassword('confirm_password')">👁️</span>
        </div>

        <div id="password-error" class="error" style="display: none;">Les mots de passe ne correspondent pas.</div>

        <button type="submit">S'inscrire</button>

        <a href="connexion.php">Déjà inscrit ? Se connecter</a>
    </form>
</div>

<script>
function validatePassword() {
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const error = document.getElementById('password-error');

    if (pass !== confirm) {
        error.style.display = 'block';
        return false;
    }

    error.style.display = 'none';
    return true;
}

function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>


