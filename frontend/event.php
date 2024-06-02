<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datetime = $_POST['datetime'];
    $location = $_POST['location'];
    $content = $_POST['content'];
    $id_user = $_SESSION['id_user'];

    $sql = "INSERT INTO posts (id_user, datetime, location, content) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $id_user, $datetime, $location, $content);
    if ($stmt->execute()) {
        header("Location: accueil.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
