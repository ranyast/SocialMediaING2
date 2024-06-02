<?php
session_start();

// Vérifie si l'utilisateur est connecté et si friend_id est défini dans l'URL
if (isset($_SESSION['id_user']) && isset($_GET['friend_id'])) {
    // Récupère les paramètres
    $current_user_id = $_SESSION['id_user'];
    $friend_id = $_GET['friend_id'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ece_in";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifie la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Requête SQL pour récupérer les messages
    $stmt = $conn->prepare("
        SELECT m.*, u.nom as sender_nom, u.prenom as sender_prenom
        FROM messages m
        JOIN utilisateur u ON m.id_user = u.id_user
        WHERE (m.id_user = ? AND m.id_groupe = ?) OR (m.id_groupe = ?)
        ORDER BY m.sent_at
    ");
    // Liaison des paramètres
    $stmt->bind_param("iii", $friend_id, $current_user_id, $friend_id);
    $stmt->execute();
    // Résultat de la requête
    $result = $stmt->get_result();

    // Affichage des messages
    while ($row = $result->fetch_assoc()) {
        echo "<div class='msgln'><span class='chat-time'>" . date("g:i A", strtotime($row['sent_at'])) . "</span> ";
        echo "<b class='user-name'>" . $row['sender_nom'] . " " . $row['sender_prenom'] . "</b>: ";
        echo htmlspecialchars($row['message']) . "<br></div>";
    }

    $stmt->close();
    $conn->close();
}
?>
