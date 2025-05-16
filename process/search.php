<?php
include '../config/database.php';

$depart = $_POST['depart'];
$arrivee = $_POST['arrivee'];
$date = $_POST['date'];

$query = $pdo->prepare("SELECT * FROM covoiturages WHERE depart = ? AND arrivee = ? AND date_depart >= ?");
$query->execute([$depart, $arrivee, $date]);
$results = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    echo "<p>Départ : " . $row['depart'] . " - Arrivée : " . $row['arrivee'] . " - Prix : " . $row['prix'] . "€</p>";
}
?>
