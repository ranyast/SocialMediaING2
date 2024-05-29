<?php
session_start(); // Démarrer une session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $servername = "localhost";
    $username = "root";
    $password_db = "";
    $dbname = "ece_in";

    $conn = new mysqli($servername, $username, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id_user, mot_de_passe FROM utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id_user, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($mot_de_passe, $hashed_password)) {
            $_SESSION['id_user'] = $id_user; // Stocker l'ID de l'utilisateur dans la session
            //redirigé vers la page vous.php après connexion avec les bonnes informations                      
            header("Location: ../../frontend/vous.php");
            exit();
        } else {
            echo "Adresse e-mail ou mot de passe incorrect.";
            rest(10);
            header("Location: connexion.html"); // Rediriger vers la page de connexion si le mot de passe est incorrect
        }
    } else {
        header("Location: connexion.html"); // Rediriger vers la page d'inscription si l'utilisateur n'existe pas
    }

    $stmt->close();
    $conn->close();
}
?>
