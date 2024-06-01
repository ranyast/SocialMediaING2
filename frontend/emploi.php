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
$stmt = $conn->prepare("SELECT utilisateur.nom, utilisateur.prenom FROM utilisateur WHERE utilisateur.id_user = ?");
$stmt->bind_param("i", $_SESSION['id_user']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom);
$stmt->fetch();
$stmt->close();

// Récupérer les posts depuis la base de données
$sql = "SELECT nom, prenom, content, media_path, datetime FROM posts ORDER BY datetime DESC";
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <script type="text/javascript" src="popup.js"></script>

</head>
<style>
    .media-content {
        display: flex;
        align-items: center;
        margin-right: 10px;
        padding: 10px;
    }


    .post-text {
        flex: 1;
    }

    .post {
        margin-bottom: 20px;
    }
</style>
<body>
<div id="wrapper">
    <div id="nav">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2" id="logo">
                    <h1><img src="logo/logo_ece.png" height="80" width="146" alt="Logo"></h1>
                </div>
                <div class="col-sm-8" id="logos">
                    <nav>
                        <a href="accueil.php"><img src="logo/accueil.jpg" height="70" width="125" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau.jpg" height="70" width="125" alt="Réseau"></a>
                        <a href="vous.php"><img src="logo/vous.jpg" height="70" width="125" alt="Vous"></a>
                        <a href="notifications.php"><img src="logo/notification.jpg" height="70" width="125" alt="Notifications"></a>
                        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="70" width="125" alt="Messagerie"></a>
                        <a href="emploi.php"><img src="logo/emploi2.jpg" height="70" width="125" alt="Emploi"></a>
                    </nav>
                </div>
                <div class="col-sm-2" id="logo">
                    <a href="../backend/connexion/connexion.html"><img src="logo/deconnexion.jpg" height="70" width="125" alt="Deconnexion"></a>
                </div>
            </div>
        </div>
    </div>
    <div id="leftcolumn">
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

    <div id="section2">
            <div id="posts">
                <h1>Offre d'emploi</h1>
                <div id="post">
                    <form method="post" action="">
                        <button type="button" class="btn btn-primary" onclick="showPopup('popupEmploi')">Nouvelle Offre</button>
                    </form>
                </div>
                <br>
                <br>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="post">';

                    echo '<p>' . htmlspecialchars($row['prenom']) . ' ' . htmlspecialchars($row['nom']) . ' propose :</p>';

                    echo '<div class="media-content">';
                    if (!empty($row['media_path'])) {
                        echo '<img class="post-media" src="' . htmlspecialchars($row['media_path']) . '" alt="Post media" style="max-width: 10%;">';
                    }
                    echo '<p class="post-text">' . htmlspecialchars($row['content']) . '</p>';
                    echo '</div>';

                    echo '<p><small>' . htmlspecialchars($row['datetime']) . '</small></p>';

                    if (!empty($row['emploiNom'])) {
                        echo '<p><strong>Nom de l\'entreprise :</strong> ' . htmlspecialchars($row['emploiNom']) . '</p>';
                    }
                    if (!empty($row['emploiPoste'])) {
                        echo '<p><strong>Poste à pourvoir :</strong> ' . htmlspecialchars($row['emploiPoste']) . '</p>';
                    }
                    if (!empty($row['emploiProfil'])) {
                        echo '<p><strong>Profil recherché :</strong> ' . htmlspecialchars($row['emploiProfil']) . '</p>';
                    }
                    if (!empty($row['emploiDescription'])) {
                        echo '<p><strong>Description de l\'offre :</strong> ' . htmlspecialchars($row['emploiDescription']) . '</p>';
                    }

                    echo '</div>';
                    echo '<hr>';
                }
            } else {
                echo '<p>Aucun post à afficher.</p>';
            }
            ?>
        </div>


        <div id="popupEmploi" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup('popupEmploi')">&times;</span>
                <h2>Créer une nouvelle offre d'emploi</h2>
                <form method="post" action="newEmploi.php">
                    <table>
                        <tr>
                            <td><label for="mediaFile">Logo de l'entreprise :</label></td>
                            <td><input type="file" id="mediaFile" name="media_path" accept="image/*"></td>
                        </tr>
                        <tr>
                            <td><label for="emploiNom">Nom de l'entreprise :</label></td>
                            <td><textarea id="emploiNom" name="emploiNom" rows="1" cols="20"></textarea></td>
                        </tr>
                        <tr>
                            <td><label for="emploiPoste">Poste à pourvoir :</label></td>
                            <td><textarea id="emploiPoste" name="emploiPoste" rows="1" cols="20"></textarea></td>
                        </tr>
                        <tr>
                            <td><label for="emploiProfil">Profil recherché :</label></td>
                            <td><textarea id="emploiProfil" name="emploiProfil" rows="4" cols="50"></textarea></td>
                        </tr>
                        <tr>
                            <td><label for="emploiDescription">Description de l'offre :</label></td>
                            <td><textarea id="emploiDescription" name="emploiDescription" rows="4" cols="50"></textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <button type="submit" class="btn btn-primary">Poster</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

    </div>
    <br>
    <br>
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
<?php
