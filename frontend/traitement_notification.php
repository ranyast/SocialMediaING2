<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$notification = $_GET['notification'] ?? '';

if (empty($notification)) {
    header("Location: accueil.php");
    exit();
}


if (strpos($notification, "demande d'ami") !== false) {

    header("Location: monreseau.php");
    exit();
} elseif (strpos($notification, "offre d'emploi") !== false) {

    header("Location: emploi.php");
    exit();
} else {

    header("Location: accueil.php");
    exit();
}
?>
