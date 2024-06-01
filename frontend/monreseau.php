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

// Get user information
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
    $mutualFriends = array();
    $sql = "SELECT DISTINCT u.id_user, u.nom, u.prenom, u.email 
            FROM utilisateur u 
            INNER JOIN friends f ON (u.id_user = f.user1 OR u.id_user = f.user2) 
            WHERE (f.user1 = ? OR f.user2 = ?) AND u.id_user != ? AND f.status = 'accepted'";
    $stmt = $conn->prepare($sql);
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
    $sql = "SELECT id_user, nom, prenom, email FROM utilisateur WHERE (nom LIKE ? OR prenom LIKE ?) AND id_user != ?";
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

// Function to send friend request
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

// Function to check if already friends
function checkIfAlreadyFriends($id_user, $id_user2, $conn) {
    $stmt = $conn->prepare("SELECT * FROM friends WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?)");
    $stmt->bind_param("iiii", $id_user, $id_user2, $id_user2, $id_user);
    $stmt->execute();
    $stmt->store_result();
    $isFriends = $stmt->num_rows > 0;
    $stmt->close();
    return $isFriends;
}

// Function to check if a friend request is pending
function checkIfFriendRequestPending($sender_id, $receiver_id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM friend_requests WHERE sender = ? AND receiver = ? AND status = 'pending'");
    $stmt->bind_param("ii", $sender_id, $receiver_id);
    $stmt->execute();
    $stmt->store_result();
    $isPending = $stmt->num_rows > 0;
    $stmt->close();
    return $isPending;
}

if($statut == "0") {
    $statut = "Administrateur";
} else if ($statut == "1") {
    $statut = "Professeur";
} else if ($statut == "2") {
    $statut = "Etudiant";
}

if($sexe == "0") {
    $sexe = "Homme";
} else if ($sexe == "1") {
    $sexe = "Femme";
} else if ($sexe == "2") {
    $sexe = "Autre";
}

if ($etudes == "0") {
    $etudes = "Terminale";
} else if ($etudes == "1") {
    $etudes = "Bac+1";
} else if ($etudes == "2") {
    $etudes = "Bac+2";
} else if ($etudes == "3") {
    $etudes = "Bac+3";
} else if ($etudes == "4") {
    $etudes = "Bac+4";
} else if ($etudes == "5") {
    $etudes = "Bac+5";
} else if ($etudes == "6") {
    $etudes = "Autre";
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
                <div class="col-sm-7" id="logos">
                    <nav>
                        <a href="accueil.php"><img src="logo/accueil.jpg" height="70" width="128" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau2.jpg" height="70" width="128" alt="Réseau"></a>
                        <a href="vous.php"><img src="logo/vous.jpg" height="70" width="128" alt="Vous"></a>
                        <a href="notifications.php"><img src="logo/notification.jpg" height="70" width="128" alt="Notifications"></a>
                        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="70" width="128" alt="Messagerie"></a>
                        <a href="emploi.php"><img src="logo/emploi.jpg" height="70" width="128" alt="Emploi"></a>
                    </nav>
                </div>
                <div class="col-sm-1" id="deconnexion">
                    <a href="../backend/connexion/connexion.html"><img src="logo/deconnexion.jpg" height="75" width="133" alt="Deconnexion"></a>
                </div>
            </div>
        </div>
    </div>
    <div id="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div id="profile-info">
                        <img src="<?php echo $photo_profil; ?>" alt="Photo de profil" width="150">
                        <h3><?php echo $prenom . " " . $nom; ?></h3>
                        <p>Email: <?php echo $email; ?></p>
                        <p>Statut: <?php echo $statut; ?></p>
                        <p>Description: <?php echo $description; ?></p>
                        <p>Experience: <?php echo $experience; ?></p>
                        <p>Formation: <?php echo $formation; ?></p>
                        <p>Etudes: <?php echo $etudes; ?></p>
                        <p>Sexe: <?php echo $sexe; ?></p>
                        <p>Compétences: <?php echo $competences; ?></p>
                    </div>
                </div>
                <div class="col-md-9">
                    <h2>Recherche d'utilisateurs</h2>
                    <form method="get" action="">
                        <input type="text" name="query" placeholder="Recherche d'utilisateurs..." required>
                        <button type="submit">Rechercher</button>
                    </form>
                    <div id="search-results">
                        <?php if (!empty($searchResults)): ?>
                            <h3>Résultats de la recherche :</h3>
                            <ul>
                                <?php foreach ($searchResults as $result): ?>
                                    <li>
                                        <?php echo $result['prenom'] . " " . $result['nom']; ?> - <?php echo $result['email']; ?>
                                        <?php if (checkIfAlreadyFriends($id_user, $result['id_user'], $conn)): ?>
                                            <button disabled>Déjà amis</button>
                                        <?php elseif (checkIfFriendRequestPending($id_user, $result['id_user'], $conn)): ?>
                                            <button disabled>Demande en attente</button>
                                        <?php else: ?>
                                            <form method="post" action="">
                                                <input type="hidden" name="receiver" value="<?php echo $result['email']; ?>">
                                                <button type="submit" name="sendRequest">Envoyer une demande d'ami</button>
                                            </form>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <h3>Mes amis</h3>
                    <ul>
                        <?php foreach ($mutuals as $mutual): ?>
                        <li>
                        <a href="profil.php?id_user=<?= htmlspecialchars($mutual['id_user']) ?>">
                        <?= htmlspecialchars($mutual['prenom']) . " " . htmlspecialchars($mutual['nom']) ?>
                        </a>
                        <?= htmlspecialchars($mutual['email']) ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
