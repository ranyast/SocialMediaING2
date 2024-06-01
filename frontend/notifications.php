<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$id_user = $_SESSION['id_user'];
$profil_user_id = isset($_GET['id_user']) ? intval($_GET['id_user']) : 0;

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get profile user information
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, formation, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $profil_user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $formation, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();

function getNotifications($conn) {
    $id_user = $_SESSION['id_user'];
    $sql = "SELECT sender FROM friend_requests WHERE receiver = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sender = $row['sender'];
            $sql = "SELECT nom, prenom FROM utilisateur WHERE id_user = ?";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("i", $sender);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            if ($result2->num_rows > 0) {
                while ($row2 = $result2->fetch_assoc()) {
                    $nom = $row2['nom'];
                    $prenom = $row2['prenom'];
                    $notifications[] = "Vous avez reçu une demande d'ami de $nom $prenom";
                }
            }
            $stmt2->close();
        }
    }
    
    $stmt->close();
    return $notifications;
}

$notifications = getNotifications($conn);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>ECE In</title>
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Poppins, sans-serif;
        }

        #wrapper {
            margin: 20px;
        }

        #leftcolumn {
            padding: 20px;
            background-color: #f7f7f7;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        #section2 {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        #footer {
            padding: 20px;
            background-color: #e9e9e9;
            border-radius: 8px;
            margin-top: 20px;
        }

        .nav-link img {
            transition: transform 0.3s ease;
        }

        .nav-link img:hover {
            transform: scale(1.1);
        }

    </style>
</head>

<body>

    <div id="wrapper">
    <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2" id="logo">
                    <h1><img src="logo/logo_ece.png" height="80" width="146" alt="Logo"></h1>
                </div>
                <div class="col-sm-9" id="logos">
                <nav>
                    <a class="nav-link" href="accueil.php"><img src="logo/accueil.jpg" height="70" width="125" alt="Accueil"></a>
                    <a class="nav-link" href="monreseau.php"><img src="logo/reseau.jpg" height="70" width="125" alt="Réseau"></a>
                    <a class="nav-link" href="vous.php"><img src="logo/vous.jpg" height="70" width="125" alt="Vous"></a>
                    <a class="nav-link" href="notifications.php"><img src="logo/notification2.jpg" height="70" width="125" alt="Notifications"></a>
                    <a class="nav-link" href="messagerie.php"><img src="logo/messagerie.jpg" height="70" width="125" alt="Messagerie"></a>
                    <a class="nav-link" href="emploi.php"><img src="logo/emploi.jpg" height="70" width="125" alt="Emploi"></a>
                    <a class="nav-link" href="../backend/connexion/connexion.html"><img src="logo/deconnexion.jpg" height="70" width="125" alt="Deconnexion"></a>
                </nav>
            </div>
    </div>
</div>

        <div id="leftcolumn" class="mb-4">
            <h3>A Propos de nous:</h3>
            <p>ECE In est un site internet créé par un groupe d'étudiantes de l'ECE Paris.</p>
            <p>Sur ce site, différentes fonctionnalités ont été mises en place et pensées par nos soins afin d'avoir un site facile d'utilisation. Voici certaines de nos fonctionnalités:</p>
            <ul>
                <li>Poster différentes choses</li>
                <li>Postuler à des offres d'emploi diverses</li>
                <li>Développement de votre réseau</li>
                <li>Discuter en live avec vos amis!</li>
                <li>Et bien d'autres ...</li>
            </ul>
            <p>N'hésitez pas à parcourir notre site afin d'en découvrir plus sur nous!</p>
            <p><small>Fait par: STITOU Ranya, SENOUSSI Ambrine, PUTOD Anna et DEROUICH Shaïma</small></p>
        </div>

        <div id="section2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <h1>Notifications</h1>
                        <ul>
                            <?php foreach ($notifications as $notification) : ?>
                            <li class="notification-item"><a href="monreseau.php"><?= htmlspecialchars($notification) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="footer" class="mt-4">
            <footer class="text-center">
                <h3>Nous Contacter:</h3>
                <div class="row">
                    <div class="col-md-6">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.372438613096!2d2.285962676518711!3d48.85110800121897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1716991235930!5m2!1sfr!2sfr" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="col-md-6" style="font-size: 18px;">
                        <p>Par Mail: <a href="mailto:ECEIN@ece.fr">ECEIN@ece.fr</a></p>
                        <p>Par Téléphone: <a href="tel:0144390600">01 44 39 06 00</a></p>
                        <p>Notre Adresse: <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/@48.851108,2.2859627,17z">10 Rue Sextius Michel, 75015 Paris</a></p>
                    </div>
                </div>
            </footer>
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
>>>>>>> d2424a0a2bf771fb920663b5daf74dd8694ac3c7
</body>

