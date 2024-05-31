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
        // Check if the user is already a friend
        $stmt2 = $conn->prepare("SELECT 1 FROM friends WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?) AND status = 'accepted'");
        $stmt2->bind_param("iiii", $id_user, $row['id_user'], $row['id_user'], $id_user);
        $stmt2->execute();
        $stmt2->store_result();
        if ($stmt2->num_rows > 0)         {
            // User is already a friend
            $row['is_friend'] = true;
        } else {
            // User is not a friend
            $row['is_friend'] = false;
        }
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
                <div class="col-sm-2" id="recherche" style="text-align: right">
                    <p>Recherche</p>
                </div>
                <div class="col-sm-7" id="logos">
                    <nav>
                        <a href="accueil.php"><img src="logo/accueil2.jpg" height="70" width="128" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau.jpg" height="70" width="128" alt="Réseau"></a>
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
                <div class="col-sm-5" id="partieGauche">
                    <h2>Mon réseau</h2>
                </div>
                <div class="col-sm-7" id="partieMilieu">
                    <h3>Rechercher des amis</h3>
                    <form action="monreseau.php" method="get">
                        <input type="text" name="query" placeholder="Rechercher des amis...">
                        <button type="submit">Rechercher</button>
                    </form>
                    <h3>Résultats de recherche</h3>
                    <?php if ($searchResults): ?>
                        <ul>
                            <?php foreach ($searchResults as $result): ?>
                                <li>
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
                    <h3>Demandes d'amis reçues</h3>
                    <?php
                    $stmt = $conn->prepare("SELECT fr.id_friend_requests, u.nom, u.prenom, u.email 
                                            FROM friend_requests fr 
                                            JOIN utilisateur u ON fr.sender = u.id_user 
                                            WHERE fr.receiver = ? AND fr.status = 'pending'");
                    $stmt->bind_param("i", $id_user);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0): ?>
                        <ul>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <li>
                                    <?= htmlspecialchars($row['prenom']) . ' ' . htmlspecialchars($row['nom']) . ' (' . htmlspecialchars($row['email']) . ')' ?>
                                    <form action="monreseau.php" method="post" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['id_friend_requests']) ?>">
                                        <button type="submit" name="respondRequest" value="accept">Accepter</button>
                                        <button type="submit" name="respondRequest" value="reject">Rejeter</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Vous n'avez aucune demande d'ami en attente.</p>
                    <?php endif; ?>
                    <?php $stmt->close(); ?>
                </div>

                <div style="margin: 10px; padding: 10px" class="col-sm-12" id="partieDroite">
                    <h3>Mes amis</h3>
                    <?php if ($mutuals): ?>
                        <ul>
                            <?php foreach ($mutuals as $friend): ?>
                                <li>
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
</div>

            </div>
        </div>
    </div>
</div>
</body>
</html>
