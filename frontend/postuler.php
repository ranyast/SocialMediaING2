<?php
session_start();


if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$id_user = $_SESSION['id_user'];
$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['offer_id']) && isset($_FILES['cv_file'])) {
    $offer_id = $_POST['offer_id'];
    $cv_file = $_FILES['cv_file'];




    $target_dir = "uploads/cv/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $cv_path = $target_dir . basename($cv_file['name']);
    if (move_uploaded_file($cv_file['tmp_name'], $cv_path)) {
        $sql = "INSERT INTO job_applications (offer_id, user_id, cv_path) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $offer_id, $id_user, $cv_path);
        if ($stmt->execute()) {
            echo "Votre candidature a été envoyée avec succès.";
        } else {
            echo "Erreur lors de l'envoi de votre candidature.";
        }
        $stmt->close();
    } else {
        echo "Erreur lors du téléchargement du fichier.";
    }
}

$conn->close();
header("Location: emploi.php");
exit();
?>
