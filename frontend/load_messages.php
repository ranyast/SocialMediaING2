<?php
session_start();

//verifie si l'utilisateur est connecté
if (isset($_SESSION['id_user']) && isset($_GET['friend_id'])) {
    //recupere les parametres
    $current_user_id = $_SESSION['id_user'];
    $friend_id = $_GET['friend_id'];

    //connexion à la bdd
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ece_in";

    $conn = new mysqli($servername, $username, $password, $dbname);

    //verifie la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //requete sql pour récupérer les messages
    $stmt = $conn->prepare("
        SELECT m.*, u1.nom as sender_nom, u1.prenom as sender_prenom, u2.nom as recipient_nom, u2.prenom as recipient_prenom
        FROM messages m
        JOIN utilisateur u1 ON m.sender = u1.id_user
        JOIN utilisateur u2 ON m.recipient = u2.id_user
        WHERE (m.sender = ? AND m.recipient = ?) OR (m.sender = ? AND m.recipient = ?)
        ORDER BY m.timestamp
    ");
    //liaison des paramètres
    $stmt->bind_param("iiii", $current_user_id, $friend_id, $friend_id, $current_user_id);
    $stmt->execute();
    //resultat de la requete
    $result = $stmt->get_result();

    //affichage des messages
    while ($row = $result->fetch_assoc()) {
        echo "<div class='msgln'><span class='chat-time'>" . date("g:i A", strtotime($row['timestamp'])) . "</span> ";
        echo "<b class='user-name'>" . $row['sender_nom'] . " " . $row['sender_prenom'] . "</b>: ";
        echo htmlspecialchars($row['message']) . "<br></div>";
    }
    $stmt->close();
    $conn->close();
}
?>