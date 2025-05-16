<?php
session_start();
session_unset();
session_destroy();

// Rediriger vers l'index avec un paramètre GET success
header("Location: ../index.php?logout=1");
exit();
?>


