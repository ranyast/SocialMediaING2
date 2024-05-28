<?php

$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$mot_de_passe = $_POST['mot_de_passe'];
$date_naissance = $_POST['date_naissance'];
$code_postal = $_POST['code_postal'];
$telephone = $_POST['telephone'];
$photo = $_POST['photo'];
$description = isset($_POST['description']) ? $_POST['description'] : null;


$terminale = isset($_POST['niveau_etudes']) && $_POST['niveau_etudes'] == 'terminale' ? 1 : 0;
$bac_plus_1 = isset($_POST['niveau_etudes']) && $_POST['niveau_etudes'] == 'bac_plus_1' ? 1 : 0;
$bac_plus_2 = isset($_POST['niveau_etudes']) && $_POST['niveau_etudes'] == 'bac_plus_2' ? 1 : 0;
$bac_plus_3 = isset($_POST['niveau_etudes']) && $_POST['niveau_etudes'] == 'bac_plus_3' ? 1 : 0;
$bac_plus_4 = isset($_POST['niveau_etudes']) && $_POST['niveau_etudes'] == 'bac_plus_4' ? 1 : 0;
$bac_plus_5 = isset($_POST['niveau_etudes']) && $_POST['niveau_etudes'] == 'bac_plus_5' ? 1 : 0;
$autre = isset($_POST['niveau_etudes']) && $_POST['niveau_etudes'] == 'autre' ? 1 : 0;


$admin = isset($_POST['role']) && in_array('admin', $_POST['role']) ? 1 : 0;
$eleve = isset($_POST['role']) && in_array('eleve', $_POST['role']) ? 1 : 0;
$prof = isset($_POST['role']) && in_array('prof', $_POST['role']) ? 1 : 0;


$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "social_media";

$conn = new mysqli($servername, $username, $password_db, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connexion réussie!";
}
