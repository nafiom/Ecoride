<?php
session_start();
include '../config/database.php';
require '../utils/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? null;

    if (empty($pseudo) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Tous les champs sont requis.";
        header("Location: ../pages/inscription.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Les mots de passe ne correspondent pas.";
        header("Location: ../pages/inscription.php");
        exit;
    }

    // Vérifier si l'email existe déjà
    $check = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        $_SESSION['error_message'] = "Un compte avec cet email existe déjà.";
        header("Location: ../pages/inscription.php");
        exit;
    }

    // Vérifier si le pseudo existe déjà
    $checkPseudo = $pdo->prepare("SELECT * FROM utilisateurs WHERE pseudo = ?");
    $checkPseudo->execute([$pseudo]);
    if ($checkPseudo->rowCount() > 0) {
        $_SESSION['error_message'] = "Ce pseudo est déjà utilisé.";
        header("Location: ../pages/inscription.php");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));

    $query = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, password, token, valide) VALUES (?, ?, ?, ?, 0)");
    $query->execute([$pseudo, $email, $hashed_password, $token]);

    $activation_link = "http://localhost:3000/pages/activer_compte.php?token=$token&email=" . urlencode($email);

    
    $sujet = "🚀 Activez votre compte ECORIDE";
    $message = "
    <div style='font-family:Segoe UI,sans-serif; background:#f4fff6; padding:30px; border-radius:10px; color:#2e7d32; max-width:600px; margin:auto; box-shadow:0 0 15px rgba(0,0,0,0.05);'>
        <h2 style='text-align:center; color:#28a745;'>Bienvenue sur ECORIDE 🌱</h2>
        <p>Bonjour <strong>$pseudo</strong>,</p>
        <p>Merci de vous être inscrit ! Pour activer votre compte, cliquez sur le bouton ci-dessous :</p>
        <div style='text-align:center; margin:30px 0;'>
            <a href='$activation_link' style='background:#28a745; color:white; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:bold;'>Activer mon compte</a>
        </div>
        <p style='font-size:14px;'>Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :</p>
        <p style='word-break:break-all; color:#2e7d32;'>$activation_link</p>
        <hr style='margin-top:30px; border:none; border-top:1px solid #c3e6cb;'>
        <p style='text-align:center; font-size:13px; color:#999;'>L’équipe ECORIDE 🌍</p>
    </div>
    ";

    envoyer_mail($email, $pseudo, $sujet, $message);

    $_SESSION['success_message'] = "Un email de confirmation vous a été envoyé. Veuillez vérifier votre boîte mail.";
    header("Location: ../pages/connexion.php");
    exit;
} else {
    header("Location: ../pages/inscription.php");
    exit;
}





