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
$stmt = $conn->prepare("SELECT utilisateur.nom, utilisateur.prenom, utilisateur.photo_profil FROM utilisateur WHERE utilisateur.id_user = ?");
$stmt->bind_param("i", $_SESSION['id_user']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $photo_profil);
$stmt->fetch();
$stmt->close();

// Récupérer les posts depuis la base de données avec jointure pour récupérer la photo de profil
$sql = "SELECT posts.content, posts.media_path, posts.location, posts.datetime, utilisateur.nom, utilisateur.prenom, utilisateur.photo_profil 
        FROM posts 
        JOIN utilisateur ON posts.id_user = utilisateur.id_user 
        ORDER BY posts.datetime DESC";
$result = $conn->query($sql);

// Fermer la connexion à la base de données
$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>ECE In</title>
    <meta charset="utf-8"/>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="carrousel.js"></script>
    <script type="text/javascript" src="carrousel2.js"></script>
    <script type="text/javascript" src="carrousel3.js"></script>
    <script type="text/javascript" src="popup.js"></script>
    <style>
    .profile-photo {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .post {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
    }
    .post-author {
        font-weight: bold;
        margin-right: 10px;
    }
    .post-media {
        max-width: 100%;
        height: auto;
    }
    .post-datetime {
        color: #888;
        font-size: 0.9em;
    }
</style>

</head>
<body>
<div id="wrapper">
    <div id="nav">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2" id="logo">
                    <h1><img src="logo/logo_ece.png" height="80" width="146" alt="Logo"></h1>
                </div>
                <div class="col-sm-8" id="logos">
                    <!-- Barre de navigation avec les boutons -->
                    <nav>
                        <a href="accueil.php"><img src="logo/accueil2.jpg" height="70" width="125" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau.jpg" height="70" width="125" alt="Réseau"></a>
                        <a href="vous.php"><img src="logo/vous.jpg" height="70" width="125" alt="Vous"></a>
                        <a href="notifications.php"><img src="logo/notification.jpg" height="70" width="125" alt="Notifications"></a>
                        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="70" width="125" alt="Messagerie"></a>
                        <a href="emploi.php"><img src="logo/emploi.jpg" height="70" width="125" alt="Emploi"></a>
                    </nav>
                </div>
                <div class="col-sm-2" id="logo">
                    <a href="../backend/connexion/connexion.html"><img src="logo/deconnexion.jpg" height="70" width="125" alt="Deconnexion"></a>
                </div>
            </div>
        </div>
    </div>

<!-- Barre de gauche ou on trouve les publicités ainsi que le bouton nouvelle publication -->
    <div id="leftcolumn">
        <a href="#new"><button type="submit" style="background-color: #028E98; border: none" class="btn btn-primary" value="validation">Nouvelle Publication</button></a>
        <br>
        <br>
        <a href="https://www.opodo.fr/travel/secure/#details/16847456699/" target="_blank">
            <img src="https://th.bing.com/th/id/OIP.sMy6FwWi6JbSqdSyp6BDKAHaD4?rs=1&pid=ImgDetMain" alt="Publicité Ryanair" style="max-width: 100%; height: auto;">
        </a>
        <br><br>
        <a href="https://www.aldi.fr/offres-et-bons-plans.html#2024-05-28" target="_blank">
            <img src="evenements/aldi.jpg" alt="Publicité Ryanair" style="max-width: 100%; height: auto;">
        </a>
    </div>
    
<!-- Barre de droite : Quelques mots sur ECE In -->
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

<!-- Endroit principal, ua centre de la page -->
    <div id="section">
        <h2>Bonjour <?php echo $prenom ; ?>, Voici les actualités du jour :</h2>
        <div id="carrousel" align="center">
            <hr>
            <h4>Evénements de la semaine</h4>
            <!-- Carrousel de photos d'événements -->
            <a href="javascript:void(0);" onclick="showPopup('popup1')"><img src="evenements/welcomeday.jpeg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup2')"><img src="evenements/RDD.jpg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup3')"><img src="evenements/rugby.jpeg" width="700" height="356"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup4')"><img src="evenements/karting.jpeg" width="700" height="392"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup5')"><img src="evenements/jeece.jpeg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup6')"><img src="evenements/RencontreEtudiants.jpg" width="700" height="356"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup7')"><img src="evenements/JPO.jpg" width="700" height="392"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup8')"><img src="evenements/soireelancement.jpeg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup9')"><img src="evenements/basket.jpeg" width="700" height="466"></a>
            <hr>
        </div>
        <br><br>
        <!-- Fermeture de tous les popups (ces derniers s'ouvrent quand on clique su une des photos du carrousel) -->
        <div id="popup1" class="popup popup1">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup1')">&times;</span>
                <img src="evenements/welcomeday2.png" width="700" height="466">
            </div>
        </div>

        <div id="popup2" class="popup popup2">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup2')">&times;</span>
                <img src="evenements/remisediplome2.png" width="700" height="466">
            </div>
        </div>

        <div id="popup3" class="popup popup3">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup3')">&times;</span>
                <img src="evenements/rugby2.png" width="700" height="356">
            </div>
        </div>

        <div id="popup4" class="popup popup4">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup4')">&times;</span>
                <img src="evenements/karting2.png" width="700" height="392">
            </div>
        </div>

        <div id="popup5" class="popup popup5">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup5')">&times;</span>
                <img src="evenements/jeece2.png" width="700" height="466">
            </div>
        </div>

        <div id="popup6" class="popup popup6">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup6')">&times;</span>
                <img src="evenements/RencontreEtudiants.jpg" width="700" height="356">
            </div>
        </div>

        <div id="popup7" class="popup popup7">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup7')">&times;</span>
                <img src="evenements/JPO.jpg" width="700" height="392">
            </div>
        </div>

        <div id="popup8" class="popup popup8">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup8')">&times;</span>
                <img src="evenements/soiree2.png" width="700" height="466">
            </div>
        </div>

        <div id="popup9" class="popup popup9">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popup9')">&times;</span>
                <img src="evenements/basket2.png" width="700" height="466">
            </div>
        </div>
<!-- Affichage des publications du réseau -->
        <div id="posts">
            <h3>Actualités des membres</h3>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<hr>';
                    echo '<div class="post">';
                    echo '<h7>' . htmlspecialchars($row['prenom']) . ' ' . htmlspecialchars($row['nom']) . ' a posté:</h7>';
                    echo '<p>' . htmlspecialchars($row['content']) . '</p>';
                    if (!empty($row['media_path'])) {
                        echo '<img src="' . htmlspecialchars($row['media_path']) . '" alt="Post media" style="max-width: 80%;">';
                    }
                    echo '<p><small>' . htmlspecialchars($row['datetime']) . '</small></p>';
                    echo '</div>';
                    echo '<hr>';
                }
            } else {
                echo '<p>Aucun post à afficher.</p>';
            }
            ?>

        </div>

        <div id="carrousel2" align="center">
            <hr>
            <a href="javascript:void(0);" onclick="showPopup('popup1')"><img src="evenements/welcomeday.jpeg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup2')"><img src="evenements/RDD.jpg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup3')"><img src="evenements/rugby.jpeg" width="700" height="356"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup4')"><img src="evenements/karting.jpeg" width="700" height="392"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup5')"><img src="evenements/jeece.jpeg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup6')"><img src="evenements/RencontreEtudiants.jpg" width="700" height="356"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup7')"><img src="evenements/JPO.jpg" width="700" height="392"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup8')"><img src="evenements/soireelancement.jpeg" width="700" height="466"></a>
            <a href="javascript:void(0);" onclick="showPopup('popup9')"><img src="evenements/basket.jpeg" width="700" height="466"></a>
            <hr>
        </div>

<!-- Boutons pour les publications -->
        <div  id="new" align="center" >
            <form method="post" action="">
                <div style="text-align: center;">
                    <h3 style="color: #028E98">Nouvelle Publication</h3>
                </div>
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 10px;">
                    <button type="button" style="background-color: #028E98; border: none"  class="btn btn-primary" onclick="showPopup('popupMedia')">Média</button>
                    <button type="button" style="background-color: #028E98; border: none" class="btn btn-primary" onclick="showPopup('popupEvenement')">Evénement</button>
                </div>
            </form>
        </div>

        <!-- Popup pour les Photos + Description -->
        <div id="popupMedia" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popupMedia')">&times;</span>
                <h2>Ajouter un Média</h2>
                <form method="post" action="media.php" enctype="multipart/form-data">
                    <label for="mediaFile">Choisir une photo ou une vidéo:</label>
                    <input type="file" id="mediaFile" name="media_path" accept="image/*,video/*">
                    <br><br>
                    <label for="content">Description:</label>
                    <textarea id="content" name="content" rows="4" cols="50"></textarea>
                    <br><br>
                    <button type="submit" style="background-color: #028E98; border: none" class="btn btn-primary">Charger</button>
                </form>
            </div>
        </div>
        <!-- Popup pour les Evénements : date + lieu + description -->
        <div id="popupEvenement" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popupEvenement')">&times;</span>
                <h2>Créer un Evénement</h2>
                <form method="post" action="event.php">
                    <label for="datetime">Date de l'événement:</label>
                    <input type="date" id="datetime" name="datetime" required>
                    <br><br>
                    <label for="location">Lieu de l'événement:</label>
                    <input type="text" id="location" name="location" required>
                    <br><br>
                    <label for="content">Description:</label>
                    <textarea id="content" name="content" rows="4" cols="50" required></textarea>
                    <br><br>
                    <button type="submit" style="background-color: #028E98; border: none" class="btn btn-primary">Créer</button>
                </form>
            </div>
        </div>
</div>
<br>
    <!-- Barre de fin avec les coordonnées -->
    <div id="footer">
        <footer>
            <h3>Nous Contacter: </h3>
            <table>
                <td style="padding-right:350px;padding-left:210px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.372438613096!2d2.285962676518711!
                    3d48.85110800121897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93f
                    ae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1716991235930!5m2!1sfr!2sfr"
                            width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </td>

                <td style="font-size: 18px; text-align: center; padding :20px;">
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