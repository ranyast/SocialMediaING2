<?php
session_start();
include 'db_connection.php';

$id_chatroom = $_POST['id_chatroom'];
$id_utilisateur = $_SESSION['user_id'];
$contenu = $_POST['contenu'];
$envoi = date('Y-m-d H:i:s');

$sql = "INSERT INTO message (id_chatroom, id_utilisateur, contenu, envoi) VALUES ($id_chatroom, $id_utilisateur, '$contenu', '$envoi')";
if ($conn->query($sql) === TRUE) {
    echo "Message sent successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
