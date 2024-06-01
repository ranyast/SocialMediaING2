<?php
session_start();

// Vérifie si l'utilisateur est connecté
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_job_offers'])) {
    $id_job_offers = $_POST['id_job_offers'];

    // Vérifie si l'utilisateur a le droit de supprimer cette offre
    $sql = "SELECT id_user FROM job_offers WHERE id_job_offers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_job_offers);
    $stmt->execute();
    $stmt->bind_result($offer_user_id);
    $stmt->fetch();
    $stmt->close();

    if ($offer_user_id == $id_user) {
        $sql = "DELETE FROM job_offers WHERE id_job_offers = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_job_offers);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: emploi.php");
            exit();
        } else {
            echo "Erreur lors de la suppression de l'offre.";
            $stmt->close();
            $conn->close();
            exit();
        }
    } else {
        echo "Vous n'avez pas l'autorisation de supprimer cette offre.";
        $conn->close();
        exit();
    }
}

$conn->close();
?>
