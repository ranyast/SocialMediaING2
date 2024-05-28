<?php
session_start();
include 'db_connection.php';

$nom = $_POST['nom'];

$sql = "INSERT INTO chatroom (nom) VALUES ('$nom')";
if ($conn->query($sql) === TRUE) {
    echo "Chatroom created successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
