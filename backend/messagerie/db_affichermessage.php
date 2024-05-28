<?php
session_start();
include 'db_connection.php';

$id_chatroom = $_GET['id_chatroom'];

$sql = "SELECT message.contenu, message.envoi, utilisateur.nom, utilisateur.prenom 
        FROM message
        JOIN utilisateur ON message.id_utilisateur = utilisateur.id_utilisateur
        WHERE message.id_chatroom = $id_chatroom
        ORDER BY message.envoi ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<p><strong>" . $row['nom'] . " " . $row['prenom'] . ":</strong> " . $row['contenu'] . " <em>(" . $row['envoi'] . ")</em></p>";
    }
} else {
    echo "No messages found.";
}

$conn->close();
?>
