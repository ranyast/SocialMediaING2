<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}


$nom = $_SESSION['nom'] ?? '';
$prenom = $_SESSION['prenom'] ?? '';
$date_naissance = $_SESSION['date_naissance'] ?? '1970-01-01';
$email = $_SESSION['email'] ?? '';
$statut = $_SESSION['statut'] ?? '';

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Préparation et exécution de la requête SQL pour récupérer les données de l'utilisateur
$stmt = $conn->prepare("SELECT utilisateur.nom, utilisateur.prenom, utilisateur.date_naissance, utilisateur.email, utilisateur.statut, profil.description, profil.experience FROM utilisateur LEFT JOIN profil ON utilisateur.id_user = profil.id_user WHERE utilisateur.id_user = ?");
$stmt->bind_param("i", $_SESSION['id_user']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $description, $experience);
$stmt->fetch();

if ($statut == '0') {
    $statut = 'Administrateur';
} else if ($statut == '1'){
    $statut = 'Professeur';
} else if ($statut == '2'){
    $statut = 'Etudiant';
} 

?>

<!DOCTYPE html>
<html>
<head>
    <title>ECE In</title>
    <meta charset="utf-8"/>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        #nav{}
        #footer{}
        #wrapper{}
        #section{}
    </style>
</head>
<body>
<div id="wrapper">
    <div id="nav">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-1" id="logo">
                    <h1><img src="logo/logo_ece.png" height="82" width="158" alt="Logo"></h1>
                </div>
                <div class="col-sm-11" id="logos">
                    <nav>
                        <a href="accueil.html"><img src="logo/accueil.jpg" height="56" width="100" alt="Accueil"></a>
                        <a href="monreseau.html"><img src="logo/reseau.jpg" height="56" width="100" alt="Réseau"></a>
                        <a href="vous.html"><img src="logo/vous2.jpg" height="56" width="100" alt="Vous"></a>
                        <a href="notifications.html"><img src="logo/notification.jpg" height="56" width="100" alt="Notifications"></a>
                        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="56" width="100" alt="Messagerie"></a>
                        <a href="emploi.html"><img src="logo/emploi.jpg" height="56" width="100" alt="Emploi"></a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div id="leftcolumn">
        <h3> Mes compétences </h3>
        <textarea> ="Écrivez votre commentaire ici..."></textarea>
    </div>

    <div id="rightcolumn">

        <h3>A Propos de nous:</h3>
        <p>
            ECE In est un site internet créé par un groupe d'étudiantes de l'ECE Paris.
        </p>
        <p>
            Sur ce site, différentes fonctionnalités ont été mises en place et pensées par nos soins afin d'avoir un site facile d'utilisation. Voici certaines de nos fonctionnalités:
        </p>
        <ul>
            <li>
                Poster différentes choses
            </li>
            <li>
                Postuler à des offres d'emploi diverses
            </li>
            <li>
                Développement de votre réseau
            </li>
            <li>
                Discuter en live avec vos amis !
            </li>
            <li>
                Et bien d'autres ...
            </li>
        </ul>
        <p>
            N'hésitez pas à parcourir notre site afin d'en découvrir plus sur nous!
        </p>
        <p><font size="-1">Fait par: STITOU Ranya, SENOUSSI Ambrine, PUTOD Anna et DEROUICH Shaïma</font></p>
    </div>
    <div id="section">
        <div class="media">
            <div class="media-left">
                <img src="logo/photoprofil.png" class="img-circle" alt="Photo profil" >
            </div>
            <div class="media-body">
                <br><br><br><br>
                <!-- Affichez le nom et le prénom de l'utilisateur -->
                <h2 class="media-heading"><?php echo $prenom . ' ' . $nom; ?></h2>
                <!-- Affichez le statut de l'utilisateur -->
                <p style="color: gray;"><?php echo $statut; ?></p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8 case" id="description">
                    <h3> Description </h3>
                    <textarea>Écrivez votre commentaire ici...</textarea>
                    <br>
                    <button type="button" class="btn btn-primary">Enregistrer</button>
                </div>
                <div class="col-sm-3 case">
                    <h3> Informations Personnelles </h3>
                    <!-- Affichez la date de naissance de l'utilisateur -->
                    <p>Date de naissance: <?php echo $date_naissance; ?></p>
                    <!-- Affichez l'email de l'utilisateur -->
                    <p>Email: <?php echo $email; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
        <footer>
            <p><font size="-1">ECE In, un site créé par des étudiants de l'ECE Paris</font></p>
        </footer>
    </div>
</div>
</body>
</html>


