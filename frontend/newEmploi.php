<?php
session_start();


if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";


$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$id_user = $_SESSION['id_user'];
$stmt = $conn->prepare("SELECT nom, prenom FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($nom, $prenom);
$stmt->fetch();
$stmt->close();

if (empty($nom) || empty($prenom)) {
    die("Erreur : Les informations de l'utilisateur ne sont pas disponibles.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $emploiNom = isset($_POST['emploiNom']) ? htmlspecialchars(trim($_POST['emploiNom'])) : null;
    $emploiPoste = isset($_POST['emploiPoste']) ? htmlspecialchars(trim($_POST['emploiPoste'])) : null;
    $emploiProfil = isset($_POST['emploiProfil']) ? htmlspecialchars(trim($_POST['emploiProfil'])) : null;
    $emploiDescription = isset($_POST['emploiDescription']) ? htmlspecialchars(trim($_POST['emploiDescription'])) : null;


    if ($emploiNom !== null && $emploiPoste !== null && $emploiProfil !== null && $emploiDescription !== null) {

        $stmt = $conn->prepare("INSERT INTO job_offers (id_user, nom, prenom, emploiNom, emploiPoste, emploiProfil, emploiDescription, datetime) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issssss", $id_user, $nom, $prenom, $emploiNom, $emploiPoste, $emploiProfil, $emploiDescription);
        if ($stmt->execute()) {
            header("Location: emploi.php?success=true");
            exit();
        } else {
            $response = ['success' => false, 'error' => 'Erreur lors de l\'insertion dans la base de données.'];
        }
        $stmt->close();
    } else {

        $response = ['success' => false, 'error' => 'Les données sont manquantes.'];
    }


    echo json_encode($response); //pour debug seulement - à retirer 
}


$conn->close();
?>
