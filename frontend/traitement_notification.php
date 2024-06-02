<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$notification = $_GET['notification'] ?? '';

if (empty($notification)) {
    // Redirection si la notification est vide ou absente
    header("Location: accueil.php");
    exit();
}

// Vérifier le type de notification et rediriger en conséquence
if (strpos($notification, "demande d'ami") !== false) {
    // Redirection vers monreseau.php pour une demande d'ami
    header("Location: monreseau.php");
    exit();
} elseif (strpos($notification, "offre d'emploi") !== false) {
    // Redirection vers emploi.php pour une offre d'emploi
    header("Location: emploi.php");
    exit();
} else {
    // Redirection vers accueil.php pour d'autres types de notifications (par défaut)
    header("Location: accueil.php");
    exit();
}
?>
