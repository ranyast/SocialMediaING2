<!DOCTYPE html>
<html>
<head>
    <title>ECE In</title>
    <meta charset="utf-8"/>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

</head>
<body>
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
    header("Location: /ProjetPiscine/frontend/accueil.html");
    exit();
} else {

    echo "Adresse e-mail ou mot de passe incorrect.";
}

$conn->close();
?>
</body>
</html>
