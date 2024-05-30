<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $date_naissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $statut = $_POST['role']; // Récupérer le statut

    // Hash the password
    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $servername = "localhost";
    $username = "root";
    $password_db = "";
    $dbname = "ece_in";

    // Create connection
    $conn = new mysqli($servername, $username, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind the statement
    $stmt = $conn->prepare("INSERT INTO utilisateur (prenom, nom, date_naissance, email, mot_de_passe, statut) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $prenom, $nom, $date_naissance, $email, $hashed_password, $statut);

    if ($stmt->execute()) {
        echo "Inscription réussie";

        exit();
    } else {
        echo "Erreur lors de l'inscription : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
