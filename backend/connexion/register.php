<?php
session_start();

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupération des données du formulaire
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$date_naissance = $_POST['date_naissance'];
$statut = $_POST['role'];

// Préparation de la requête SQL pour insérer l'utilisateur dans la table utilisateur
$stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, date_naissance, statut) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $nom, $prenom, $date_naissance, $statut);

// Exécution de la requête
if ($stmt->execute() === TRUE) {
    // Redirection vers la page de connexion
    header("Location: connexion.html");
} else {
    echo "Erreur lors de l'inscription : " . $conn->error;
}

$stmt->close();
$conn->close();
?>

