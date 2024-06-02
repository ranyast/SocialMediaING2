<?php
session_start();

// Vérifie si l'utilisateur est connecté et si les données POST sont définies
if (isset($_SESSION['id_user']) && isset($_POST['message']) && isset($_POST['recipient_id'])) {
    // Récupère les données POST
    $message = $_POST['message'];
    $recipient_id = $_POST['recipient_id'];
    $sender_id = $_SESSION['id_user'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password_db = "";
    $dbname = "ece_in";

    $conn = new mysqli($servername, $username, $password_db, $dbname);

    // Vérifie la connexion à la base de données
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Requête SQL pour insérer un message
    $stmt = $conn->prepare("INSERT INTO messages (id_groupe, id_user, message) VALUES (?, ?, ?)");
    // Liaison des paramètres
    $stmt->bind_param("iis", $recipient_id, $sender_id, $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
