<?php
session_start();

if (!isset($_SESSION['id_user'])) {
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


$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, formation, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $formation, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();

$conn->close();

if ($statut == '0') {
    $statut = 'Administrateur';
} else if ($statut == '1') {
    $statut = 'Professeur';
} else if ($statut == '2') {
    $statut = 'Etudiant';
}

if($sexe == "0"){
    $sexe = "Homme";
}
else if ($sexe == "1"){
    $sexe = "Femme";
} else if ($sexe == "2"){
    $sexe = "Autre";
}

if($etudes =='0') {
    $etudes = 'Terminale';
    }else if($etudes =='1') {
        $etudes = 'Bac +1';
    }
    else if($etudes =='2') {
        $etudes = 'Bac +2';
    }
    else if($etudes =='3') {
        $etudes = 'Bac +3';
    }
    else if($etudes =='4') {
        $etudes = 'Bac +4';
    }
    else if($etudes =='5') {
        $etudes = 'Bac +5 et plus';
    }
    else if($etudes =='6') {
        $etudes = 'Autre';
    }


$xml = new SimpleXMLElement('<CV/>');

$xml->addChild('nom', htmlspecialchars($nom));
$xml->addChild('prenom', htmlspecialchars($prenom));
$xml->addChild('date_naissance', htmlspecialchars($date_naissance));
$xml->addChild('email', htmlspecialchars($email));
$xml->addChild('statut', htmlspecialchars($statut));
$xml->addChild('photo_profil', htmlspecialchars($photo_profil));
$xml->addChild('description', htmlspecialchars($description));
$xml->addChild('experience', htmlspecialchars($experience));
$xml->addChild('formation', htmlspecialchars($formation));
$xml->addChild('etudes', htmlspecialchars($etudes));
$xml->addChild('sexe', htmlspecialchars($sexe));
$xml->addChild('competences', htmlspecialchars($competences));


$xml->asXML('cv.xml');


header('Location: telecharger_pdf.php');
exit();
?>
