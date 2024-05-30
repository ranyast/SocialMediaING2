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


function getMutualFriends($id_user, $conn) {
    $mutualFriends = array();
    
    // Query the database to get the list of mutual friends
    $sql = "SELECT utilisateur.nom, utilisateur.prenom, utilisateur.email 
            FROM utilisateur 
            INNER JOIN friends ON utilisateur.id_user = friends.user1 OR utilisateur.id_user = friends.user2 
            WHERE (friends.user1 = ? OR friends.user2 = ?) AND utilisateur.id_user != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $id_user, $id_user, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $mutualFriends[] = $row;
    }

    $stmt->close();
    return $mutualFriends;
}

// Function to search users
function searchUsers($query, $id_user, $conn) {
    $searchResults = [];
    $query = "%$query%";
    $sql = "SELECT nom, prenom, email FROM utilisateur WHERE (nom LIKE ? AND prenom LIKE ?) AND id_user != ?";
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

// Handle friend request sending
if (isset($_POST['sendRequest'])) {
    $receiver_email = $_POST['receiver'];
    sendFriendRequest($id_user, $receiver_email, $conn);
}

// Function to send friend request
function sendFriendRequest($sender_id, $receiver_email, $conn) {
    // Get the receiver's id from their email
    $stmt = $conn->prepare("SELECT id_user FROM utilisateur WHERE email = ?");
    $stmt->bind_param("s", $receiver_email);
    $stmt->execute();
    $stmt->bind_result($receiver_id);
    $stmt->fetch();
    $stmt->close();

    if ($receiver_id) {
        // Insert friend request into friend_requests table
        $stmt = $conn->prepare("INSERT INTO friend_requests (sender, receiver, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("ii", $sender_id, $receiver_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Demande d\'ami envoyée!');</script>";
    } else {
        echo "<script>alert('Utilisateur introuvable!');</script>";
    }
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
                        <a href="accueil.php"><img src="logo/accueil.jpg" height="75" width="133" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau2.jpg" height="75" width="133" alt="Réseau"></a>
                        <a href="vous.php"><img src="logo/vous.jpg" height="75" width="133" alt="Vous"></a>
                        <a href="notifications.php"><img src="logo/notification.jpg" height="75" width="133" alt="Notifications"></a>
                        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="75" width="133" alt="Messagerie"></a>
                        <a href="emploi.php"><img src="logo/emploi.jpg" height="75" width="133" alt="Emploi"></a>
                    </nav>
                </div>
                <div class="col-sm-1" id="deconnexion">
                    <a href="../backend/connexion/connexion.html"><img src="logo/deconnexion.jpg" height="75" width="133" alt="Deconnexion"></a>
                </div>

            </div>
        </div>
    </div>
    <br>
    <div if="right-col">
        <form method="GET" action="monreseau.php">
            <div class="form-group">
                <input type="text" name="query" class="form-control" placeholder="Rechercher des amis">
            </div>

        </form>
    </div>
    <div id="section">
        <h3>Liste des amis</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($mutuals as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['prenom']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if (!empty($searchResults)): ?>
            <h3>Résultats de la recherche</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($searchResults as $result) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($result['nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($result['prenom']) . "</td>";
                        echo "<td>" . htmlspecialchars($result['email']) . "</td>";
                        echo '<td>
                                <form method="POST" action="monreseau.php">
                                    <input type="hidden" name="receiver" value="' . htmlspecialchars($result['email']) . '">
                                    <button type="submit" name="sendRequest" class="btn btn-primary">Envoyer une demande d\'ami</button>
                                </form>
                              </td>';
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div id="footer">
        <footer> © ECE Paris. All rights reserved. </footer>
    </div>
</div>
</body>
</html>