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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $date_naissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];


    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $servername = "localhost";
    $username = "root";
    $password_db = "";
    $dbname = "ece_in";

    $conn = new mysqli($servername, $username, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO utilisateur (prenom, nom, date_naissance, email, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $prenom, $nom, $date_naissance, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Inscription réussie";
        header("Location: /ProjetPiscine/frontend/accueil.html");
        exit();
    } else {
        echo "Erreur lors de l'inscription : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
</body>
</html>
