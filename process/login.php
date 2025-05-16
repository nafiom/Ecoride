<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
        header("Location: ../pages/connexion.php");
        exit;
    }

    $query = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error_message'] = "Identifiants invalides.";
        header("Location: ../pages/connexion.php");
        exit;
    }


    if (isset($user['valide']) && $user['valide'] == 0) {
        $_SESSION['error_message'] = "Votre compte n'est pas encore activé. Veuillez vérifier vos e-mails.";
        header("Location: ../pages/connexion.php");
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        $_SESSION['success_message'] = "Connexion réussie.";

      
        switch ($user['role']) {
            case 'admin':
                header("Location: ../pages/espace_admin.php"); break;
            case 'chauffeur':
                header("Location: ../pages/espace_chauffeur.php"); break;
            case 'employe':
                header("Location: ../pages/espace_employe.php"); break;
            case 'utilisateur':
            default:
                header("Location: ../pages/espace_utilisateur.php"); break;
        }
        exit;
    } else {
        $_SESSION['error_message'] = "Mot de passe incorrect.";
        header("Location: ../pages/connexion.php");
        exit;
    }
} else {
    header("Location: ../pages/connexion.php");
    exit;
}



