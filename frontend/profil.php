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

// Get logged in user status
$stmt = $conn->prepare("SELECT statut FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($current_user_statut);
$stmt->fetch();
$stmt->close();

// Get profile user information
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, formation, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $profil_user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $formation, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();

if($sexe == '0') {
    $sexe = 'Homme';
} else if($sexe == '1') {
    $sexe = 'Femme';
} else {
    $sexe = 'Autre';
}

if($etudes == '0') {
    $etudes = 'Terminale';
} else if($etudes == '1') {
    $etudes = 'Bac+1';
} else if($etudes == '2') {
    $etudes = 'Bac+2';
} else if($etudes == '3') {
    $etudes = 'Bac+3';
} else if($etudes == '4') {
    $etudes = 'Bac+4';
} else if($etudes == '5') {
    $etudes = 'Bac+5';
} else if ($etudes == '6'){
    $etudes = 'Autre';
}

if($statut == '0') {
    $statut = "Administrateur";
} else if($statut == '1') {
    $statut = "Professeur";
} else if($statut == '2') {
    $statut = "Eleve";
}

// Get friends of the profile user
function getFriends($profil_user_id, $conn) {
    $friends = [];
    $sql = "SELECT u.id_user, u.nom, u.prenom 
            FROM utilisateur u
            INNER JOIN friends f ON (u.id_user = f.user1 OR u.id_user = f.user2) 
            WHERE (f.user1 = ? OR f.user2 = ?) AND u.id_user != ? AND f.status = 'accepted'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $profil_user_id, $profil_user_id, $profil_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
    }

    $stmt->close();
    return $friends;
}

$friends = getFriends($profil_user_id, $conn);

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account']) && $current_user_statut == 0 && $profil_user_id != $id_user && $statut != "Administrateur") {
    $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id_user = ?");
    $stmt->bind_param("i", $profil_user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: monreseau.php");
    exit();
}

// Handle removing friend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_friend'])) {
    $stmt = $conn->prepare("DELETE FROM friends WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?)");
    $stmt->bind_param("iiii", $id_user, $profil_user_id, $profil_user_id, $id_user);
    $stmt->execute();
    $stmt->close();

    header("Location: profil.php?id_user=" . $profil_user_id);
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil de <?= htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) ?></title>
    <meta charset="utf-8"/>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
</head>
<body>
<div id="wrapper">
    <div id="nav">
        <a href="accueil.php"><img src="logo/accueil.jpg" height="70" width="128" alt="Accueil"></a>
        <a href="monreseau.php"><img src="logo/reseau2.jpg" height="70" width="128" alt="Réseau"></a>
        <a href="vous.php"><img src="logo/vous.jpg" height="70" width="128" alt="Vous"></a>
        <a href="notifications.php"><img src="logo/notification.jpg" height="70" width="128" alt="Notifications"></a>
        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="70" width="128" alt="Messagerie"></a>
        <a href="emploi.php"><img src="logo/emploi.jpg" height="70" width="128" alt="Emploi"></a>
    </div>
    <div id="section">
        <div class="container-fluid">
            <h1>Profil de <?= htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom) ?></h1>
            <p><strong>Nom:</strong> <?= htmlspecialchars($nom) ?></p>
            <p><strong>Prénom:</strong> <?= htmlspecialchars($prenom) ?></p>
            <p><strong>Date de naissance:</strong> <?= htmlspecialchars($date_naissance) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Statut:</strong> <?= htmlspecialchars($statut) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($description) ?></p>
            <p><strong>Experience:</strong> <?= htmlspecialchars($experience) ?></p>
            <p><strong>Formation:</strong> <?= htmlspecialchars($formation) ?></p>
            <p><strong>Etudes:</strong> <?= htmlspecialchars($etudes) ?></p>
            <p><strong>Sexe:</strong> <?= htmlspecialchars($sexe) ?></p>
            <p><strong>Compétences:</strong> <?= htmlspecialchars($competences) ?></p>

            <?php if ($current_user_statut == 0 && $profil_user_id != $id_user && $statut != "Administrateur"): ?>
                <form method="post" style="display: inline;">
                    <button type="submit" name="delete_account">Supprimer le compte</button>
                </form>
            <?php endif; ?>

            <form method="post" style="display: inline;">
                <button type="submit" name="remove_friend">Supprimer de la liste d'amis</button>
            </form>

            <h2>Amis</h2>
            <ul>
                <?php foreach ($friends as $friend): ?>
                    <li><a href="profil.php?id_user=<?= $friend['id_user'] ?>"><?= htmlspecialchars($friend['prenom']) . ' ' . htmlspecialchars($friend['nom']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
