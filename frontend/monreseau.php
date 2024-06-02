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

$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, formation, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $formation, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();


$mutuals = getMutualFriends($id_user, $conn);



$searchResults = [];
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $searchResults = searchUsers($query, $id_user, $conn);
}

// Handle friend request sending

if (isset($_POST['sendRequest'])) {
    $receiver_email = $_POST['receiver'];
    sendFriendRequest($id_user, $receiver_email, $conn);
}

// Handle friend request response

if (isset($_POST['respondRequest'])) {
    $request_id = $_POST['request_id'];
    $response = $_POST['respondRequest'];
    respondToFriendRequest($request_id, $response, $conn);
}

// Function to get mutual friends

function getMutualFriends($id_user, $conn) {
    $mutualFriends = array();
    $sql = "SELECT DISTINCT u.id_user, u.nom, u.prenom, u.email, u.photo_profil FROM utilisateur u INNER JOIN friends f ON (u.id_user = f.user1 OR u.id_user = f.user2) WHERE (f.user1 = ? OR f.user2 = ?) AND u.id_user != ? AND f.status = 'accepted'";    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $id_user, $id_user, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Use user id as key to ensure uniqueness
        $mutualFriends[$row['id_user']] = $row;
    }
    $stmt->close();
    return array_values($mutualFriends); // Convert associative array back to indexed array
}
// Function to search users
function searchUsers($query, $id_user, $conn) {
    $searchResults = [];
    $query = "%$query%";
    $sql = "SELECT id_user, nom, prenom, email, photo_profil FROM utilisateur WHERE (nom LIKE ? OR prenom LIKE ?) AND id_user != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $query, $query, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }
    $stmt->close();
    return $searchResults;
}
function sendFriendRequest($sender_id, $receiver_email, $conn) {
    $stmt = $conn->prepare("SELECT id_user FROM utilisateur WHERE email = ?");
    $stmt->bind_param("s", $receiver_email);
    $stmt->execute();
    $stmt->bind_result($receiver_id);
    $stmt->fetch();
    $stmt->close();
    if ($receiver_id) {
        $stmt = $conn->prepare("INSERT INTO friend_requests (sender, receiver, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("ii", $sender_id, $receiver_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Demande d\'ami envoyée!');</script>";
    } else {
        echo "<script>alert('Utilisateur introuvable!');</script>";
    }
}
// Function to respond to friend request
function respondToFriendRequest($request_id, $response, $conn) {
    $status = $response == 'accept' ? 'accepted' : 'rejected';
    $stmt = $conn->prepare("UPDATE friend_requests SET status = ? WHERE id_friend_requests = ?");
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();
    if ($status == 'accepted') {
        $stmt = $conn->prepare("SELECT sender, receiver FROM friend_requests WHERE id_friend_requests = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->bind_result($sender_id, $receiver_id);
        $stmt->fetch();
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO friends (user1, user2, status) VALUES (?, ?, 'accepted')");
        $stmt->bind_param("ii", $sender_id, $receiver_id);
        $stmt->execute();
        $stmt->close();
    }
    echo "<script>alert('Demande d\'ami " . ($status == 'accepted' ? "acceptée" : "rejetée") . "!');</script>";
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

if($statut =='0') {
    $statut ='Administrateur';
    }else if($statut =='1') {
        $statut ='Professeur';
    }
    else if($statut =='2') {
        $statut ='Etudiant';
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
    <style>
        .search-result {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .search-result img {
            border-radius: 50%;
            margin-right: 10px;
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
                    <nav>
                        <a href="accueil.php"><img src="logo/accueil.jpg" height="70" width="125" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau2.jpg" height="70" width="125" alt="Réseau"></a>
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
    <div id="leftcolumn">
        <img src="<?php echo $photo_profil; ?>" class="img-circle" alt="Photo de profil" width="150">
        <h3><?php echo $prenom . " " . $nom; ?></h3>
        <p><?php echo $statut; ?></p>
        <p>Email: <?php echo $email; ?></p>
        <p>Etudes: <?php echo $etudes; ?></p>
        <p>Sexe: <?php echo $sexe; ?></p>
    </div>
    <div id="section2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3" id="partieGauche">
                    <h2>Mon réseau</h2>
                </div>
                <div class="col-sm-7" id="partieMilieu">
                    <h3>Rechercher des amis</h3>
                    <form action="monreseau.php" method="get">
                        <input type="text" name="query" placeholder="Rechercher des amis...">
                        <button type="submit">Rechercher</button>
                    </form>
                    <br>
                    <h3>Résultats de recherche</h3>
                    <?php if ($searchResults): ?>
                        <ul>
                            <?php foreach ($searchResults as $result): ?>
                                <li class="search-result">
                                    <img src="<?= htmlspecialchars($result['photo_profil']) ?>" alt="Photo de profil" width="50" height="50">
                                    <?= htmlspecialchars($result['prenom']) . ' ' . htmlspecialchars($result['nom']) . ' (' . htmlspecialchars($result['email']) . ')' ?>
                                    <form action="monreseau.php" method="post" style="display:inline;">
                                        <input type="hidden" name="receiver" value="<?= htmlspecialchars($result['email']) ?>">
                                        <button type="submit" name="sendRequest">Envoyer une demande d'ami</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Aucun résultat trouvé.</p>
                    <?php endif; ?>
                    <br>
                    <h3>Demandes d'amis reçues</h3>
                    <?php
                    $stmt = $conn->prepare("SELECT r.id_friend_requests, u.nom, u.prenom, u.email, u.photo_profil FROM friend_requests r JOIN utilisateur u ON r.sender = u.id_user WHERE r.receiver = ? AND r.status = 'pending'");
                    $stmt->bind_param("i", $id_user);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='search-result'>";
                        echo "<img src='" . $row['photo_profil'] . "' alt='Photo de profil' width='50' height='50'>";
                        echo "<div>";
                        echo "<p>" . $row['prenom'] . " " . $row['nom'] . " (" . $row['email'] . ")</p>";
                        echo "<form method='post' action=''>";
                        echo "<input type='hidden' name='request_id' value='" . $row['id_friend_requests'] . "'>";
                        echo "<button type='submit' name='respondRequest' value='accept'>Accepter</button>";
                        echo "<button type='submit' name='respondRequest' value='reject'>Rejeter</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                    }
                    $stmt->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div id="section2">
        <h3>Mes amis</h3>
        <?php if ($mutuals): ?>
            <ul>
                <?php foreach ($mutuals as $friend): ?>
                    <li class="search-result">
                        <img src="<?= htmlspecialchars($friend['photo_profil'] ?? 'logo/photoprofil.png') ?>" alt="Photo de profil" width="50" height="50">
                        <a href="profil.php?id_user=<?= htmlspecialchars($friend['id_user']) ?>">
                            <?= htmlspecialchars($friend['prenom']) . ' ' . htmlspecialchars($friend['nom']) ?>
                        </a>
                        (<?= htmlspecialchars($friend['email']) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Vous n'avez pas encore d'amis.</p>
        <?php endif; ?>
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