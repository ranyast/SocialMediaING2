<?php
session_start();

//verifie si l'utilisateur est connecté
if (isset($_SESSION['id_user']) && isset($_POST['message']) && isset($_POST['recipient_id'])) {
    //recupere les parametres
    $message = $_POST['message'];
    $recipient_id = $_POST['recipient_id'];
    $sender_id = $_SESSION['id_user'];

    //connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password_db = "";
    $dbname = "ece_in";

    $conn = new mysqli($servername, $username, $password_db, $dbname);

    //verifie la connexion a la bdd
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //requete sql pour insérer un message
    $stmt = $conn->prepare("INSERT INTO messages (sender, recipient, message) VALUES (?, ?, ?)");
    //liaison des paramètres
    $stmt->bind_param("iis", $sender_id, $recipient_id, $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>