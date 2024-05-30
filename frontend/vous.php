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

$stmt = $conn->prepare("SELECT nom, prenom, date_naissance, email, statut, photo_profil, description, experience, etudes, sexe, competences FROM utilisateur WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($nom, $prenom, $date_naissance, $email, $statut, $photo_profil, $description, $experience, $etudes, $sexe, $competences);
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

// Si le formulaire est soumis, mettez à jour les données dans la table utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $experience = $_POST['experience'];
    $etudes = $_POST['etudes'];
    $sexe = $_POST['sexe'];
    $competences = $_POST['competences'];

    $stmt = $conn->prepare("UPDATE utilisateur SET description = ?, experience = ?, etudes = ?, sexe = ?, competences = ? WHERE id_user = ?");
    $stmt->bind_param("ssiiii", $description, $experience, $etudes, $sexe, $competences, $id_user);
    $stmt->execute();
    $stmt->close();
    header("Location: vous.php"); // Redirige vers la page vous.php après la mise à jour
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
                <div class="col-sm-11" id="logos">
                    <nav>
                        <a href="accueil.php"><img src="logo/accueil.jpg" height="56" width="100" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau.jpg" height="56" width="100" alt="Réseau"></a>
                        <a href="vous.php"><img src="logo/vous2.jpg" height="56" width="100" alt="Vous"></a>
                        <a href="notifications.php"><img src="logo/notification.jpg" height="56" width="100" alt="Notifications"></a>
                        <a href="messagerie.php"><img src="logo/messagerie.jpg" height="56" width="100" alt="Messagerie"></a>
                        <a href="emploi.php"><img src="logo/emploi.jpg" height="56" width="100" alt="Emploi"></a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div id="leftcolumn">
        <h3> Mes compétences </h3>
        <textarea><?php echo htmlspecialchars($competences); ?></textarea>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
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
                <img src="logo/photoprofil.png" class="img-circle" alt="Photo profil">
            </div>
            <div class="media-body">
                <br><br><br><br>
                <!-- Affichez le nom et le prénom de l'utilisateur -->
                <h2 class="media-heading"><?php echo htmlspecialchars($prenom) . ' ' . htmlspecialchars($nom); ?></h2>
                <!-- Affichez le statut de l'utilisateur -->
                <p style="color: gray;"><?php echo htmlspecialchars($statut); ?></p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8 case" id="description">
                    <h3>Description</h3>
                    <!-- Formulaire pour la description et l'expérience -->
                    <form method="post" action="">
                        <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
                        <h3>Experience</h3>
                        <textarea name="experience"><?php echo htmlspecialchars($experience); ?></textarea>
                        <h3>Etudes</h3>
                        <select name= "etudes">
                            <option value="0" <?php if ($etudes == '0') echo 'selected'; ?>>Bac</option>
                            <option value="1" <?php if ($etudes == '1') echo 'selected'; ?>>Bac +1</option>
                            <option value="2" <?php if ($etudes == '2') echo 'selected'; ?>>Bac +2</option>
                            <option value="3" <?php if ($etudes == '3') echo 'selected'; ?>>Bac +3</option>
                            <option value="4" <?php if ($etudes == '4') echo 'selected'; ?>>Bac +4</option>
                            <option value="5" <?php if ($etudes == '5') echo 'selected'; ?>>Bac +5</option>
                            <option value="6" <?php if ($etudes == '6') echo 'selected'; ?>>Bac +6</option>
                        <?php echo htmlspecialchars($etudes); ?>" min="0" max="6">
                        <br>
                        <h3>Sexe</h3>
                        <label><input type="radio" name="sexe" value="0" <?php if ($sexe == '0') echo 'checked'; ?>>Homme</label>
                        <label><input type="radio" name="sexe" value="1" <?php if ($sexe == '1') echo 'checked'; ?>>Femme</label>
                        <label><input type="radio" name="sexe" value="2" <?php if ($sexe == '2') echo 'checked'; ?>>Autre</label>
                        <br>
                        <h3>Compétences</h3>
                        <textarea name="competences"><?php echo htmlspecialchars($competences); ?></textarea>
                        <br>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
                <div class="col-sm-3 case">
                    <h3>Informations Personnelles</h3>
                    <!-- Affichez la date de naissance de l'utilisateur -->
                    <p>Date de naissance: <?php echo htmlspecialchars($date_naissance); ?></p>
                    <!-- Affichez l'email de l'utilisateur -->
                    <p>Email: <?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

    <div id="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-8 case" id="description">
                    <h3>Description</h3>
                    <!-- Formulaire pour la description et l'expérience -->
                    <form method="post" action="">
                        <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
                        <h3>Experience</h3>
                        <textarea name="experience"><?php echo htmlspecialchars($experience); ?></textarea>
                        <h3>Etudes</h3>
                        <select name= "etudes">
                            <option value="0" <?php if ($etudes == '0') echo 'selected'; ?>>Bac</option>
                            <option value="1" <?php if ($etudes == '1') echo 'selected'; ?>>Bac +1</option>
                            <option value="2" <?php if ($etudes == '2') echo 'selected'; ?>>Bac +2</option>
                            <option value="3" <?php if ($etudes == '3') echo 'selected'; ?>>Bac +3</option>
                            <option value="4" <?php if ($etudes == '4') echo 'selected'; ?>>Bac +4</option>
                            <option value="5" <?php if ($etudes == '5') echo 'selected'; ?>>Bac +5</option>
                            <option value="6" <?php if ($etudes == '6') echo 'selected'; ?>>Bac +6</option>
                        <?php echo htmlspecialchars($etudes); ?>" min="0" max="6">
                        <br>
                        <h3>Sexe</h3>
                        <label><input type="radio" name="sexe" value="0" <?php if ($sexe == '0') echo 'checked'; ?>>Homme</label>
                        <label><input type="radio" name="sexe" value="1" <?php if ($sexe == '1') echo 'checked'; ?>>Femme</label>
                        <label><input type="radio" name="sexe" value="2" <?php if ($sexe == '2') echo 'checked'; ?>>Autre</label>
                        <br>
                        <h3>Compétences</h3>
                        <textarea name="competences"><?php echo htmlspecialchars($competences); ?></textarea>
                        <br>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
                <div class="col-sm-3 case">
                    <h3>Generer mon CV</h3>
                    <!-- Affichez la date de naissance de l'utilisateur -->
                    <p>Date de naissance: <?php echo htmlspecialchars($date_naissance); ?></p>
                    <!-- Affichez l'email de l'utilisateur -->
                    <p>Email: <?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
        </div>
    </div>
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
