<?php
session_start();

// Check if user is logged in
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

// Get user information
$friendRequests = [];
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, formation, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $formation, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();

// Get mutual friends
$mutuals = getMutualFriends($id_user, $conn);

// Handle search query
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
    $mutualFriends = [];
    $sql = "SELECT DISTINCT u.id_user, u.nom, u.prenom, u.email 
            FROM utilisateur u 
            INNER JOIN friends f ON (u.id_user = f.user1 OR u.id_user = f.user2) 
            WHERE (f.user1 = ? OR f.user2 = ?) AND u.id_user != ? AND f.status = 'accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $id_user, $id_user, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $mutualFriends[$row['id_user']] = $row;
    }

    $stmt->close();
    return array_values($mutualFriends);
}

// Function to search users
function searchUsers($query, $id_user, $conn) {
    $searchResults = [];
    $query = "%$query%";
    $sql = "SELECT id_user, nom, prenom, email FROM utilisateur WHERE (nom LIKE ? OR prenom LIKE ?) AND id_user != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $query, $query, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $stmt2 = $conn->prepare("SELECT 1 FROM friends WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?) AND status = 'accepted'");
        $stmt2->bind_param("iiii", $id_user, $row['id_user'], $row['id_user'], $id_user);
        $stmt2->execute();
        $stmt2->store_result();
        $row['is_friend'] = $stmt2->num_rows > 0;
        $stmt2->close();

        $searchResults[] = $row;
    }

    $stmt->close();
    return $searchResults;
}

// Function to send a friend request
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

// Function to respond to a friend request
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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon réseau</title>
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Poppins, sans-serif;
        }

        #nav {
            font-weight: bold;
            font-size: 1.2em;
            background-color: white;
            text-align: center;
            padding: 10px;
            height: 100px;
        }

        #wrapper {
            margin: 10px;
        }

        #leftcolumn {
            padding: 20px;
            background-color: #e9e9e9;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        #section {
            padding: 20px;
            background-color: ;
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

        .notification-item {
            background-color: #4d5156;
            color: black;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
        }

    </style>
</head>

<body>
    <div id="wrapper">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-2" id="logo">
                    <h1><img src="logo/logo_ece.png" height="80" width="146" alt="Logo"></h1>
                </div>
                <div class="col-sm-8" id="logos">
                    <nav class="d-flex justify-content-center">
                        <a class="nav-link" href="accueil.php"><img src="logo/accueil.jpg" height="70" width="125" alt="Accueil"></a>
                        <a class="nav-link" href="monreseau.php"><img src="logo/reseau2.jpg" height="70" width="125" alt="Réseau"></a>
                        <a class="nav-link" href="vous.php"><img src="logo/vous.jpg" height="70" width="125" alt="Vous"></a>
                        <a class="nav-link" href="notifications.php"><img src="logo/notification.jpg" height="70" width="125" alt="Notifications"></a>
                        <a class="nav-link" href="messagerie.php"><img src="logo/messagerie.jpg" height="70" width="125" alt="Messagerie"></a>
                        <a class="nav-link" href="emploi.php"><img src="logo/emploi.jpg" height="70" width="125" alt="Emploi"></a>
                        <a class="nav-link" href="../backend/connexion/connexion.html"><img src="logo/deconnexion.jpg" height="70" width="125" alt="Deconnexion"></a>
                    </nav>
                </div>
            </div>
        </div>
        <br>
        <div id="section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <h1>Mon réseau</h1>
                        <div class="card">
                            <div class="card-body">
                                <h3>Rechercher des amis</h3>
                                <form action="monreseau.php" method="get" class="form-inline mb-3">
                                    <input type="text" name="query" placeholder="Rechercher des amis..." class="form-control mr-2">
                                    <button type="submit" class="btn btn-primary">Rechercher</button>
                                </form>
                                <h3>Résultats de recherche</h3>
                                <?php if ($searchResults): ?>
                                    <ul class="list-group mb-3">
                                        <?php foreach ($searchResults as $result): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= htmlspecialchars($result['prenom']) . ' ' . htmlspecialchars($result['nom']) . ' (' . htmlspecialchars($result['email']) . ')' ?>
                                                <div>
                                                    <a href="profil.php?id_user=<?= htmlspecialchars($result['id_user']) ?>" class="btn btn-link">Consulter le profil</a>
                                                    <form action="monreseau.php" method="post" class="d-inline">
                                                        <input type="hidden" name="receiver" value="<?= htmlspecialchars($result['id_user']) ?>">
                                                        <button type="submit" name="sendRequest" class="btn btn-secondary">Envoyer une demande d'ami</button>
                                                    </form>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>Aucun résultat trouvé.</p>
                                <?php endif; ?>
                                <h3>Demandes d'amis reçues</h3>
                                <?php if ($friendRequests): ?>
                                    <ul class="list-group mb-3">
                                        <?php foreach ($friendRequests as $request): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= htmlspecialchars($request['prenom']) . ' ' . htmlspecialchars($request['nom']) . ' (' . htmlspecialchars($request['email']) . ')' ?>
                                                <div>
                                                    <form action="accept_request.php" method="post" class="d-inline">
                                                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['request_id']) ?>">
                                                        <button type="submit" name="acceptRequest" class="btn btn-success">Accepter</button>
                                                    </form>
                                                    <form action="reject_request.php" method="post" class="d-inline">
                                                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['request_id']) ?>">
                                                        <button type="submit" name="rejectRequest" class="btn btn-danger">Rejeter</button>
                                                    </form>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>Aucune demande d'ami reçue.</p>
                                <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
                        <h1>Mes amis</h1>
                        <ul class="list-group mb-3">
                            <?php foreach ($mutuals as $mutual): ?>
                                <li class="list-group mb-3">
                                    <a href="profil.php?id_user=<?= htmlspecialchars($mutual['id_user']) ?>" class="list-group-item list-group-item-action">
                                        <?= htmlspecialchars($mutual['prenom']) . ' ' . htmlspecialchars($mutual['nom']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
    <footer class="text-center">
        <div class="row">
            <div class="col-md-6">
                <h3>Nous Contacter:</h3>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.372438613096!2d2.285962676518711!3d48.85110800121897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6701b486bb253%3A0x61e9cc6979f93fae!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris!5e0!3m2!1sfr!2sfr!4v1716991235930!5m2!1sfr!2sfr" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div style="font-size: 18px; margin-top: 10px;">
                    <p>Par Mail: <a href="mailto:ECEIN@ece.fr">ECEIN@ece.fr</a></p>
                    <p>Par Téléphone: <a href="tel:0144390600">01 44 39 06 00</a></p>
                    <p>Notre Adresse: <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/@48.851108,2.2859627,17z">10 Rue Sextius Michel, 75015 Paris</a></p>
                </div>
            </div>
            <div class="col-md-6">
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
        </div>
    </footer>
</body>
</html>

                           
