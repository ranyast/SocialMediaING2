<?php
session_start();

// Vérifie si l'utilisateur est connecté
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

// Récupère les informations de l'utilisateur connecté
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, formation, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $formation, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();

if ($statut == '0') {
    $statut = 'Administrateur';
} else if ($statut == '1'){
    $statut = 'Professeur';
} else if ($statut == '2'){
    $statut = 'Etudiant';
}

if($sexe == '0'){
    $sexe = 'Homme';
} else if($sexe == '1'){
    $sexe = 'Femme';
} else if($sexe == '2'){
    $sexe = 'Autre';
}

if ($etudes == '0') {
    $etudes = 'Bac';
} else if ($etudes == '1') {
    $etudes = 'Bac +1';
} else if ($etudes == '2') {
    $etudes = 'Bac +2';
} else if ($etudes == '3') {
    $etudes = 'Bac +3';
} else if ($etudes == '4') {
    $etudes = 'Bac +4';
} else if ($etudes == '5') {
    $etudes = 'Bac +5';
} else if ($etudes == '6') {
    $etudes = 'Autre';
}
 




// Si le formulaire est soumis, mettez à jour les données dans la table utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $experience = $_POST['experience'];
    $formation = $_POST['formation'];
    $etudes = $_POST['etudes'];
    $sexe = $_POST['sexe'];
    $competences = $_POST['competences'];
    $photo_profil = $_POST['photo_profil'];

    $stmt = $conn->prepare("UPDATE utilisateur SET description = ?, experience = ?, formation = ?, etudes = ?, sexe = ?, competences = ?, photo_profil = ? WHERE id_user = ?");
    $stmt->bind_param("ssssissi", $description, $experience, $formation, $etudes, $sexe, $competences, $photo_profil, $id_user);


    $stmt->execute();
    $stmt->close();
    header("Location: ./../vous.php"); // Redirige vers la page vous.php après la mise à jour
    exit();
}

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

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
                        <a href="monreseau.php"><img src="logo/reseau.jpg" height="75" width="133" alt="Réseau"></a>
                        <a href="vous.php"><img src="logo/vous2.jpg" height="75" width="133" alt="Vous"></a>
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

    <div id="rightcolumn">
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

    <div id="section">
        <div class="media">
            <div class="media-left">
            <?php if (!empty($photo_profil)): ?>
                <img src="<?php echo htmlspecialchars($photo_profil); ?>" class="img-circle" alt="Photo profil">
                    <?php else: ?>
                            <p>Aucune photo de profil disponible</p>
             <?php endif; ?>
            </div>
            <div class="media-body">
                <br><br><br><br>
                <h2 class="media-heading"><?php echo htmlspecialchars($prenom ?? '') . ' ' . htmlspecialchars($nom ?? ''); ?></h2>
                <p style="color: gray;"><?php echo htmlspecialchars($statut ?? ''); ?></p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <form method="post" action="">
                    <div id="infoprofil">
                        <div class="col-sm-20 case" id="description">
                            <h3>Description</h3>
                            <p><?php echo htmlspecialchars($description ?? ''); ?></p>
                        </div>
                        <div class="col-sm-10 case">
                            <h3>Informations Personnelles</h3>
                            <p>Sexe: <?php echo htmlspecialchars($sexe ?? ''); ?></p>
                            <p>Niveau d'études: <?php echo htmlspecialchars($etudes ?? ''); ?></p>
                            <p>Date de naissance: <?php echo htmlspecialchars($date_naissance ?? ''); ?></p>
                            <p>Email: <?php echo htmlspecialchars($email ?? ''); ?></p>
                        </div>
                        <div class="col-sm-8 case" id="experience">
                            <h3>Expérience</h3>
                            <p><?php echo htmlspecialchars($experience ?? ''); ?></p>
                        </div>
                        <div class="col-sm-8 case" id="formation">
                            <h3>Formation</h3>
                            <p><?php echo htmlspecialchars($formation ?? ''); ?></p>
                        </div>
                        <div class="col-sm-8 case" id="competences">
                            <h3>Compétences</h3>
                            <p><?php echo htmlspecialchars($competences ?? ''); ?></p>
                        </div>
                    </div>
                </form>
                <div class="col-sm-12 case2" id="modifprofil">
                    <h1 style="text-align: center; color: white;">Modification</h1>
                    <br>
                    <form method="post" action="">
                        <div class="col-sm-8 case" id="description_modif">
                            <h3>Description</h3>
                            <textarea name="description"></textarea>
                        </div>
                        <div class="col-sm-8 case" id="photo_profil_modif">
                            <h3>Photo de profil</h3>
                            <input type="file" name="photo_profil">
                        <div class="col-sm-8 case" id="experience_modif">
                            <h3>Expérience</h3>
                            <textarea name="experience"></textarea>
                        </div>
                        <div class="col-sm-8 case" id="formation_modif">
                            <h3>Formation</h3>
                            <textarea name="formation"></textarea>
                        </div>
                        <div class="col-sm-8 case" id="competences_modif">
                            <h3>Compétences</h3>
                            <textarea name="competences"></textarea>
                        </div>
                        <div class="col-sm-8 case" id="etudes_modif">
                            <h3>Études</h3>
                            <select name="etudes">
                                <option value="0" <?php if ($etudes == '0') echo 'selected'; ?>>Bac</option>
                                <option value="1" <?php if ($etudes == '1') echo 'selected'; ?>>Bac +1</option>
                                <option value="2" <?php if ($etudes == '2') echo 'selected'; ?>>Bac +2</option>
                                <option value="3" <?php if ($etudes == '3') echo 'selected'; ?>>Bac +3</option>
                                <option value="4" <?php if ($etudes == '4') echo 'selected'; ?>>Bac +4</option>
                                <option value="5" <?php if ($etudes == '5') echo 'selected'; ?>>Bac +5</option>
                                <option value="6" <?php if ($etudes == '6') echo 'selected'; ?>>Autre</option>
                            </select>
                        </div>
                        <div class="col-sm-3 case" id="sexe_modif">
                            <h3>Sexe</h3>
                            <label><input type="radio" name="sexe" value="0" <?php if ($sexe == '0') echo 'checked'; ?>> Homme</label>
                            <label><input type="radio" name="sexe" value="1" <?php if ($sexe == '1') echo 'checked'; ?>> Femme</label>
                            <label><input type="radio" name="sexe" value="2" <?php if ($sexe == '2') echo 'checked'; ?>> Autre</label>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary" value="validation">Enregistrer</button>
                    </form>
                </div>
                <div class="col-sm-3 case">
                    <button type="submit" class="btn btn-primary">Générer mon CV</button>
                </div>
            </div>
        </div>
    </div>

</div>

<div id="footer">
    <footer>
        <p><font size="-1">ECE In, un site créé par des étudiants de l'ECE Paris</font></p>
    </footer>
</div>
</body>
</html>