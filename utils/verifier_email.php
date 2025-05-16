<?php
session_start();
include '../config/database.php'; 

if (!isset($_GET['token'])) {
    echo "❌ Lien de validation invalide.";
    exit;
}

$token = $_GET['token'];


$stmt = $pdo->prepare("SELECT id, valide FROM utilisateurs WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "❌ Ce lien est invalide ou a déjà été utilisé.";
    exit;
}


if ($user['valide'] == 1) {
    echo "✅ Ce compte est déjà activé.";
    exit;
}


$update = $pdo->prepare("UPDATE utilisateurs SET valide = 1, token = NULL WHERE id = ?");
$update->execute([$user['id']]);

echo "<h2>✅ Votre compte a bien été activé !</h2>";
echo "<p><a href='../pages/connexion.php'>Se connecter</a></p>";
