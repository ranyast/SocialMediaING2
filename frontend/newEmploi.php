<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$id_user = $_SESSION['id_user'];
$nom = $_SESSION['nom'] ?? '';
$prenom = $_SESSION['prenom'] ?? '';

$id_user = $_SESSION['id_user'];
$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($emploiPoste == 0){
    $emploiPoste = "CDI";
} elseif($emploiPoste == 1){
    $emploiPoste = "CDD";
} elseif($emploiPoste == 2){
    $emploiPoste = "Stage";
} elseif($emploiPoste == 3){
    $emploiPoste = "Alternance";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emploiNom = $_POST['emploiNom'];
    $emploiPoste = $_POST['emploiPoste'];
    $emploiProfil = $_POST['emploiProfil'];
    $emploiDescription = $_POST['emploiDescription'];
    $location = $_POST['location'];
    $datetime = date('Y-m-d H:i:s');


    if (!empty($_FILES['media_path']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['media_path']['name']);
        move_uploaded_file($_FILES['media_path']['tmp_name'], $target_file);
    } else {
        $target_file = null;
    }

    $stmt = $conn->prepare("INSERT INTO job_offers (id_user, nom, prenom, emploiNom, emploiPoste, emploiProfil, emploiDescription, location, datetime, media_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssss", $id_user, $nom, $prenom, $emploiNom, $emploiPoste, $emploiProfil, $emploiDescription, $location, $datetime, $target_file);

    if ($stmt->execute()) {
        header("Location: emploi.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>