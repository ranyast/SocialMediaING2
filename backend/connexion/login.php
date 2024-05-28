<?php

$email = $_POST['email'];
$mot_de_passe = $_POST['mot_de_passe'];


$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "social_media";

$conn = new mysqli($servername, $username, $password_db, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM utilisateur WHERE email='$email' AND mot_de_passe='$mot_de_passe'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
 
    echo "Connexion réussie!";
} else {

    echo "Adresse e-mail ou mot de passe incorrect.";
}

$conn->close();
?>
