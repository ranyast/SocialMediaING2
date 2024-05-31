<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$id_user = $_SESSION['id_user'];
$profil_user_id = isset($_GET['id_user']) ? intval($_GET['id_user']) : 0;

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Get profile user information
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, formation, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $profil_user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $formation, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();

if($sexe == '0') {
    $sexe = 'Homme';
} else if($sexe == '1') {
    $sexe = 'Femme';
} else {
    $sexe = 'Autre';
}

if($etudes == '0') {
    $etudes = 'Terminale';
} else if($etudes == '1') {
    $etudes = 'Bac+1';
} else if($etudes == '2') {
    $etudes = 'Bac+2';
} else if($etudes == '3') {
    $etudes = 'Bac+3';
} else if($etudes == '4') {
    $etudes = 'Bac+4';
} else if($etudes == '5') {
    $etudes = 'Bac+5';
} else if ($etudes == '6'){
    $etudes = 'Autre';
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil de <?= htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) ?></title>
    <meta charset="utf-8"/>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
    <div id="nav">
        <a href="accueil.php"><img src="logo/accueil.jpg" height="70" width="128" alt="Accueil"></a>
        <a href="monreseau.php"><img src="logo/reseau.jpg" height="70" width="128" alt="Réseau"></a>
        <a href="vous.php"><img src="logo/vous2.jpg" height="70" width="128" alt="Vous"></a>
        <a href="notifications.php"><img src="logo/notification.jpg" height="70" width="128" alt="Notifications"></a>
        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="70" width="128" alt="Messagerie"></a>
        <a href="emploi.php"><img src="logo/emploi.jpg" height="70" width="128" alt="Emploi"></a>
    </div>
    <div id="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

























<h2>Profil de <?= htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) ?></h2>
                    <p>Email: <?= htmlspecialchars($email) ?></p>
                    <p>Date de naissance: <?= htmlspecialchars($date_naissance) ?></p>
                    <p>Statut: <?= htmlspecialchars($statut) ?></p>
                    <p>Description: <?= htmlspecialchars($description) ?></p>
                    <p>Expérience: <?= htmlspecialchars($experience) ?></p>
                    <p>Formation: <?= htmlspecialchars($formation) ?></p>
                    <p>Études: <?= htmlspecialchars($etudes) ?></p>
                    <p>Sexe: <?= htmlspecialchars($sexe) ?></p>
                    <p>Compétences: <?= htmlspecialchars($competences) ?></p>