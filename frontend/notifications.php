<?php
session_start();

//verifie si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: connexion.html");
    exit();
}

$id_user = $_SESSION['id_user'];

// connexion à la base de données
$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

//connexion à la base de données
$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fonction pour recuperer tous les utilisateurs
function getAllUsers($conn) {
    $sql = "SELECT id_user FROM utilisateur";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['id_user'];
    }
    return $users;
}

// fonction pour recuperer les demandes d'amis
function getFriendRequests($conn, $id_user) {
    $sql = "SELECT sender FROM friend_requests WHERE receiver = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $requests = [];
    
    //recuperer le nom et prénom de la personne faisant de la demande
    while ($row = $result->fetch_assoc()) {
        $sender = $row['sender'];
        $sql = "SELECT nom, prenom FROM utilisateur WHERE id_user = ?";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("i", $sender);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        if ($result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            $nom = $row2['nom'];
            $prenom = $row2['prenom'];
            $requests[] = "Vous avez reçu une demande d'ami de $nom $prenom";
        }
        $stmt2->close();
    }

    $stmt->close();
    return $requests;
}

// fonction pour recuperer les nouveaux posts
function getNewPosts($conn) {
    $sql = "SELECT id_user, content FROM posts";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    $users = getAllUsers($conn);

    //recuperer le contenu du post
    while ($row = $result->fetch_assoc()) {
        $content = $row['content'];
        $posts[] = "Un utilisateur a posté : $content";
        
        foreach ($users as $user_id) {
            $message = "Un utilisateur a posté : $content";
            $sql2 = "INSERT INTO notifications (id_user, message) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("is", $user_id, $message);
            $stmt2->execute();
            $stmt2->close();
        }
    }

    $stmt->close();
    return $posts;
}

// fonction pour recuperer les posts des amis des amis
function getPostsOfFriendsOfFriends($conn, $id_user) {
    $sql = "SELECT p.content FROM posts p
            JOIN friends f1 ON p.id_user = f1.user2
            JOIN friends f2 ON f1.user1 = f2.user2
            WHERE f2.user1 = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];

    //recuperer le contenu du post
    while ($row = $result->fetch_assoc()) {
        $content = $row['content'];
        $posts[] = "Un ami d'un ami a posté : $content";
    }

    $stmt->close();
    return $posts;
}

// fonction pour recuperer les offres d'emploi
function OffreEmploi($conn) {
    $sql = "SELECT id_job_offers, emploiPoste FROM job_offers";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $offres = [];
    $users = getAllUsers($conn);
    $types = ["CDI", "CDD", "Stage", "Alternance"];

    //recuperer le type de l'offre d'emploi
    while ($row = $result->fetch_assoc()) {
        $id_offre = $row['id_job_offers'];
        $poste = $types[$row['emploiPoste']] ?? "Inconnu";
        $message = "Une nouvelle offre d'emploi est disponible: $poste";
        $offres[] = $message;

        foreach ($users as $user_id) {
            $sql2 = "INSERT INTO notifications (id_user, id_job_offers, message) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("iis", $user_id, $id_offre, $message);
            $stmt2->execute();
            $stmt2->close();
        }
    }

    $stmt->close();
    return $offres;
}

// recuperer les notifications pour l'utilisateur connecté
$friendRequests = getFriendRequests($conn, $id_user);
$newPosts = getNewPosts($conn);
$postsOfFriendsOfFriends = getPostsOfFriendsOfFriends($conn, $id_user);
$jobOffers = OffreEmploi($conn);

// combiner toutes les notifications en une seule liste
$notifications = array_merge($friendRequests, $newPosts, $postsOfFriendsOfFriends, $jobOffers);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ECE In</title>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    
</head>
<body>
<div id="wrapper">
    <div id="nav">
        <!-- barre de navigation presente sur toutes les pages-->
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
                        <a href="notifications.php"><img src="logo/notification2.jpg" height="70" width="125" alt="Notifications"></a>
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
    <div class="col-md-3" id="leftcolumn">
        <!-- section a propos de nous-->
        <h3>À Propos de nous:</h3>
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
    <!-- section notifications-->
    <div id="section2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h1>Notifications</h1>
                    <ul>
                        <?php foreach ($notifications as $notification): ?>
                            <li>
                                <a href="traitement_notification.php?notification=<?php echo urlencode($notification); ?>"><?php echo $notification; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <br><br><br>
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <div id="footer">
        <!-- footer de la page--> 
        <footer>
            <h3>Nous Contacter: </h3>
            <table>
                <td style="padding-right:350px;padding-left:210px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.372438613096!2d2.285962676518711!3d48.85110800121897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1716991235930!5m2!1sfr!2sfr" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </td>
                <td style="font-size: 18px; text-align: center; padding :20px;">
                    <p>Par Mail: <a href="mailto : ECEIN@ece.fr">ECEIN@ece.fr</a></p>
                    <p>Par Téléphone: <a href="tel:0144390600">01 44 39 06 00</a></p>
                    <p>Notre Adresse: <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/@48.851108,2.2859627,17z/data=!3m1!4b1!4m6!3m5!1s0x47e6701b486bb253:0x61e9cc6979f93fae!8m2!3d48.8511045!4d2.2885376!16s%2Fg%2F11bw3xcdpj?entry=ttu">10 Rue Sextius Michel, 75015 Paris</a></p>
                </td>
            </table>
            <p>ECE In Corporation &copy; 2024</p>
        </footer>
    </div>

</div>
</body>
</html>
