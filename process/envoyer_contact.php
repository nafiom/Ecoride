<?php
session_start();
require_once '../config/env.php';
require '../utils/send_mail.php';

$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$message_content = trim($_POST['message'] ?? '');

if ($nom && $email && $message_content) {
    
    $sujetAdmin = "📩 Nouveau message de contact ECORIDE";
    $messageAdmin = "
        <h3>Nouveau message reçu via ECORIDE</h3>
        <p><strong>Nom :</strong> " . htmlspecialchars($nom) . "</p>
        <p><strong>Email :</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Message :</strong></p>
        <p>" . nl2br(htmlspecialchars($message_content)) . "</p>
    ";

    
    envoyer_mail($_ENV['MAIL_USERNAME'], "ECORIDE Contact", $sujetAdmin, $messageAdmin);

    
    $sujetUser = "📨 Merci pour votre message – ECORIDE";
    $messageUser = "
        <p>Bonjour <strong>$nom</strong>,</p>
        <p>Merci pour votre message. Nous l'avons bien reçu et nous vous répondrons sous peu.</p>
        <hr>
        <p><strong>Votre message :</strong></p>
        <p>" . nl2br(htmlspecialchars($message_content)) . "</p>
        <hr>
        <p>À bientôt sur ECORIDE 🌱</p>
    ";

    envoyer_mail($email, $nom, $sujetUser, $messageUser);

    $_SESSION['contact_success'] = true;
} else {
    $_SESSION['contact_success'] = false;
}

header("Location: ../pages/contact.php");
exit;


