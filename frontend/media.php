<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les informations de l'utilisateur
$id_user = $_SESSION['id_user'];
$stmt = $conn->prepare("SELECT nom, prenom FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($nom, $prenom);
$stmt->fetch();
$stmt->close();

if (empty($nom) || empty($prenom)) {
    die("Erreur : Les informations de l'utilisateur ne sont pas disponibles.");
}

// Vérifie si la méthode HTTP est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assurez-vous de bien valider et nettoyer les données reçues
    $content = isset($_POST['content']) ? htmlspecialchars(trim($_POST['content'])) : null;
    $media_path = isset($_FILES['media_path']) ? $_FILES['media_path'] : null;

    // Vérifier si les clés sont définies et ne sont pas nulles
    if ($content !== null && $media_path !== null) {
        // Vérifier si le fichier a été correctement téléchargé
        if ($media_path['error'] !== UPLOAD_ERR_OK) {
            // Gérer l'erreur de téléchargement du fichier
            $response = ['success' => false, 'error' => 'Erreur lors du téléchargement du fichier.'];
        } else {
            // Enregistrer le fichier média
            $mediaDir = 'uploads/';
            if (!is_dir($mediaDir)) {
                mkdir($mediaDir, 0777, true);
            }
            $mediaPath = $mediaDir . basename($media_path['name']);

            // Vérifier si le fichier est bien un fichier téléchargé et le déplacer vers le dossier des uploads
            if (move_uploaded_file($media_path['tmp_name'], $mediaPath)) {
                // Insertion des données dans la base de données
                $stmt = $conn->prepare("INSERT INTO posts (id_user, nom, prenom, content, media_path, datetime) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("issss", $id_user, $nom, $prenom, $content, $mediaPath);
                if ($stmt->execute()) {
                    header("Location: accueil.php?success=true");
                    exit();
                } else {
                    $response = ['success' => false, 'error' => 'Erreur lors de l\'insertion dans la base de données.'];
                }
                $stmt->close();
            } else {
                // Gérer l'erreur de déplacement du fichier
                $response = ['success' => false, 'error' => 'Erreur lors de l\'enregistrement du fichier.'];
            }
        }
    } else {
        // Gérer le cas où les clés ne sont pas définies ou nulles
        $response = ['success' => false, 'error' => 'Les données sont manquantes.'];
    }

    // Renvoyer la réponse JSON
    echo json_encode($response);
}

// Fermeture de la connexion
$conn->close();
?>
