<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

$id_user = $_SESSION['id_user'];

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

// Créer une connexion
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Préparer et exécuter la requête SQL
$stmt = $conn->prepare("SELECT utilisateur.nom, utilisateur.prenom, utilisateur.date_naissance, utilisateur.statut, profil.photo_profil, profil.description, profil.experience, profil.etudes, profil.sexe, profil.competences FROM utilisateur JOIN profil ON utilisateur.id_user = profil.id_user WHERE utilisateur.id_user = ?;");
$stmt->bind_param("isssississs", $id_user, $nom, $prenom, $date_naissance, $email, $mot_de_passe, $statut, $photo_profil, $description, $etudes, $sexe);
$stmt->execute();
$stmt->bind_result($nom, $prenom, $date_naissance, $statut, $photo_profil, $description, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();
$conn->close();

// Définir les rôles
$roles = ["Admin", "Prof", "Élève"];
$role_name = isset($statut) ? $roles[$statut] : 'Inconnu';


$nom = $nom ?? '';
$prenom = $prenom ?? '';
$date_naissance = $date_naissance ?? '1970-01-01'; 
$photo_profil = $photo_profil ?? 'path/to/default/profile/photo.png'; 
$etudes = $etudes ?? 0;
$sexe = $sexe ?? 0;
$competences = $competences ?? '';

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
                <br>
                <br>
                <br>
                <br>

                <h2 class="media-heading"> 
                    <?php echo $prenom . ' ' . $nom; ?>
                </h2>
                <p style="color: gray;"> <?php echo $role_name; ?> </p>
             </p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8 case"  id="description">
                    <h3> Description </h3>
                    <textarea> ="Écrivez votre commentaire ici..."></textarea>
                </div>
                <div class="col-sm-3 case">
                    <h3> Informations Personnelles </h3>
                    <p> Date de naissance : <?php echo $date_naissance; ?> </p>
                    <p> Niveau d'etude :
                        <?php
                        switch ($etudes) {
                            case 1:
                                echo "Bac";
                                break;
                            case 2:
                                echo "Bac +2";
                                break;
                            case 3:
                                echo "Bac +3";
                                break;
                            case 4:
                                echo "Bac +4";
                                break;
                            case 5:
                                echo "Bac +5";
                                break;
                            default:
                                echo "Inconnu";
                        }
                        ?>
                    </p>

                </div>

                <div class="col-sm-11 case" id="experience">
                    <h3> Experience </h3>
                    <textarea> ="Écrivez votre commentaire ici..."></textarea>
                </div>

                <div class="col-sm-11  case"  id="Formation">
                    <h3> Formation </h3>
                    <textarea> ="Écrivez votre commentaire ici..."></textarea>
                </div>
            </div>
        </div>
    </div>


    <div id="footer">
        <footer>
            <h3>Nous Contacter: </h3>
            <table>
                <td style="padding-right:350px;padding-left:310px;">
                    <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/
            @48.851108,2.2859627,17z/data=!3m1!4b1!4m6!3m5!1s0x47e6701b486bb253:0x61e9cc6979f93fae!8m2!3d48.
            8511045!4d2.2885376!16s%2Fg%2F11bw3xcdpj?entry=ttu"><img src="logo/carte_map.PNG" width="500" height="280"></a>
                </td>

                <td style="font-size: 18px; text-align: right; padding :20px;">
                    <p>Par Mail: <a href="mailto : ECEIN@ece.fr"> ECEIN@ece.fr</a></p>
                    <p>Par Téléphone: <a href="tel:0144390600">01 44 39 06 00</a></p>
                    <p>Notre Adresse: <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/
            @48.851108,2.2859627,17z/data=!3m1!4b1!4m6!3m5!1s0x47e6701b486bb253:0x61e9cc6979f93fae!8m2!3d48.
            8511045!4d2.2885376!16s%2Fg%2F11bw3xcdpj?entry=ttu">10 Rue Sextius Michel, 75015 Paris</a></p>
                </td>
            </table>


            <p>ECE In Corporation &copy; 2024</p>


        </footer>
    </div>


</div>
</body>
</html>