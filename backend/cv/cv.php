<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    // Redirect to login page
    header("Location: connexion.html");
    exit();
}

$id_user = $_SESSION['id_user'];

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user information
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, photo_profil, description, experience, formation, etudes, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $photo_profil, $description, $experience, $formation, $etudes, $competences);
$stmt->fetch();
$stmt->close();

// Calculate age
$birthDate = new DateTime($date_naissance);
$today = new DateTime();
$age = $today->diff($birthDate)->y;

// Generate CV HTML content
$cv_content = "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>CV</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #028E98;
        }
        .container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .header h1, .header h2 {
            margin: 10px 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            display: inline-block; /* Affiche le titre sur la même ligne */
            color: black;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .section p {
            display: inline-block; /* Affiche le paragraphe sur la même ligne */
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <img src='$photo_profil' alt='Photo Profil'>
            <h1>$nom</h1>
            <h2>$prenom</h2>
            <p>Âge : $age</p>
            <p>Email : <a href='mailto:$email'>$email</a></p>
        </div>
        <div class='section'>
            <h3>Description : </h3>
            <p>$description</p>
        </div>
        <div class='section'>
            <h3>Expérience :</h3>
            <p>$experience</p>
        </div>
        <div class='section'>
            <h3>Formation :</h3>
            <p>$formation</p>
        </div>
        <div class='section'>
            <h3>Études :</h3>
            <p>$etudes</p>
        </div>
        <div class='section'>
            <h3>Compétences : </h3>
            <p>$competences</p>
        </div>
    </div>
</body>
</html>
";

// Save the CV content to an HTML file
$cv_file = 'cv_' . $id_user . '.html';
file_put_contents($cv_file, $cv_content);

// Redirect to the generated CV file
header("Location: $cv_file");
exit();
?>
