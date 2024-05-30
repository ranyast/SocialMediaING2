<?php
session_start();
if (isset($_SESSION['id_user']) && isset($_POST['message']) && isset($_POST['recipient_id'])) {
    $message = $_POST['message'];
    $recipient_id = $_POST['recipient_id'];
    $sender_id = $_SESSION['id_user'];

    $servername = "localhost";
    $username = "root";
    $password_db = "";
    $dbname = "ece_in";

    $conn = new mysqli($servername, $username, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender, recipient, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $recipient_id, $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
