<?php
session_start();


if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$id_user = $_SESSION['id_user'];
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


$sql = "SELECT id_job_offers, id_user, nom, prenom, emploiNom, emploiPoste, emploiProfil, emploiDescription, location, datetime, media_path FROM job_offers ORDER BY datetime DESC";
$result = $conn->query($sql);


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
            padding-left: 20px;
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
                <h1>Offres d'emploi</h1>
                <div id="post">
                    <form method="post" action="">
                        <button type="button" style="background-color: #028E98; border: none" class="btn btn-primary" onclick="showPopup('popupEmploi')">Nouvelle Offre</button>
                    </form>
                </div>
                <br>
                <br>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="post">';
                        echo '<p> On vous propose :</p>';
                        echo '<div class="media-content">';
                        if (!empty($row['media_path'])) {
                            echo '<img class="post-media" src="' . htmlspecialchars($row['media_path']) . '" alt="Post media" style="max-width: 30%;">';
                        }
                        echo '<div class="post-text">';
                        if (!empty($row['emploiNom'])) {
                            echo '<p><strong>' . htmlspecialchars($row['emploiNom']) . '</strong> </p>';
                        }
                        if (!empty($row['emploiPoste'])) {
                        
                            switch ($row['emploiPoste']) {
                                case '0':
                                    $poste = "CDI";
                                    break;
                                case '1':
                                    $poste = "CDD";
                                    break;
                                case '2':
                                    $poste = "Stage";
                                    break;
                                case '3':
                                    $poste = "Alternance";
                                    break;
                                default:
                                    $poste = "Inconnu";
                            }
                            echo '<p><strong> Poste pourvu:</strong> ' . htmlspecialchars($poste) . '</p>';

                        }
                        if (!empty($row['emploiProfil'])) {
                            echo '<p><strong>Profil recherché :</strong> ' . htmlspecialchars($row['emploiProfil']) . '</p>';
                        }
                        if (!empty($row['emploiDescription'])) {
                            echo '<p><strong>Description de l\'offre :</strong> ' . htmlspecialchars($row['emploiDescription']) . '</p>';
                        }
                        if (!empty($row['location'])) {
                            echo '<p><strong>Lieu :</strong> ' . htmlspecialchars($row['location']) . '</p>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '<p><small>' . htmlspecialchars($row['datetime']) . '</small></p>';
                        if ($row['id_user'] == $id_user) {
                            echo '<form method="post" action="deleteEmploi.php" style="display:inline;">';
                            echo '<input type="hidden" name="id_job_offers" value="' . htmlspecialchars($row['id_job_offers']) . '">';
                            echo '<button type="submit" class="btn btn-danger">Supprimer</button>';
                            echo '</form>';
                        } else {
                            echo '<button type="button" style="background-color: #028E98; border: none" class="btn btn-primary" onclick="showPopup(\'popupPostuler\', ' . htmlspecialchars($row['id_job_offers']) . ')">Postuler</button>';
                        }
                        echo '</div>';
                        echo '<hr>';
                    }
                } else {
                    echo '<p>Aucune offre d\'emploi disponible pour le moment.</p>';
                }
                ?>
            </div>

            <div id="popupEmploi" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closePopup('popupEmploi')">&times;</span>
                    <h2>Créer une nouvelle offre d'emploi</h2>
                    <form method="post" action="newEmploi.php" enctype="multipart/form-data">
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
                                <td><label for="emploiPoste">Type de poste :</label></td>
                                <td>
                                    <select id="emploiPoste" name="emploiPoste">
                                        <option value="0">CDI</option>
                                        <option value="1">CDD</option>
                                        <option value="2">Stage</option>
                                        <option value="3">Alternance</option>
                                    </select>
                                </td>
                            <tr>
                                <td><label for="emploiProfil">Profil recherché :</label></td>
                                <td><textarea id="emploiProfil" name="emploiProfil" rows="2" cols="30"></textarea></td>
                            </tr>
                            <tr>
                                <td><label for="emploiDescription">Description de l'offre :</label></td>
                                <td><textarea id="emploiDescription" name="emploiDescription" rows="4" cols="30"></textarea></td>
                            </tr>
                            <tr>
                                <td><label for="location">Lieu :</label></td>
                                <td><textarea id="location" name="location" rows="1" cols="30"></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="submit" value="Créer"></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>

            <div id="popupPostuler" class="popup">
                <div class="popup-content">
                    <span class="close-btn" onclick="closePopup('popupPostuler')">&times;</span>
                    <h2>Postuler à cette offre</h2>
                    <form method="post" action="postuler.php" enctype="multipart/form-data">
                        <input type="hidden" id="offer_id" name="offer_id">
                        <table>
                            <tr>
                                <td><label for="cvFile">Votre CV (PDF) :</label></td>
                                <td><input type="file" id="cvFile" name="cv_file" accept="application/pdf"></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="submit" value="Postuler"></td>
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
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.372438613096!2d2.285962676518711!3d48.85110800121897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1716991235930!5m2!1sfr!2sfr"
                                width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </td>
                    <td style="font-size: 18px; text-align: center; padding :20px;">
                        <p>Par Mail: <a href="mailto : ECEIN@ece.fr"> ECEIN@ece.fr</a></p>
                        <p>Par Téléphone: <a href="tel:0144390600">01 44 39 06 00</a></p>
                        <p>Notre Adresse: <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/@48.851108,2.2859627,17z/data=!3m1!4b1!4m6!3m5!1s0x47e6701b486bb253:0x61e9cc6979f93fae!8m2!3d48.8511045!4d2.2885376!16s%2Fg%2F11bw3xcdpj?entry=ttu">10 Rue Sextius Michel, 75015 Paris</a></p>
                    </td>
                </table>
                <p>ECE In Corporation &copy; 2024</p>
            </footer>
        </div>
    </div>

    <script>
        function showPopup(popupId, offerId = null) {
            document.getElementById(popupId).style.display = 'block';
            if (offerId !== null) {
                document.getElementById('offer_id').value = offerId;
            }
        }

        function closePopup(popupId) {
            document.getElementById(popupId).style.display = 'none';
        }
    </script>
    </body>
    </html>