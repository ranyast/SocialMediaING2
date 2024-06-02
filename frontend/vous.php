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

    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] == 0) {
        $photo_profil = 'uploads/' . basename($_FILES['photo_profil']['name']);
        if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $photo_profil)) {
            // Update the profile photo only if the upload is successful
            $stmt = $conn->prepare("UPDATE utilisateur SET description = ?, experience = ?, formation = ?, etudes = ?, sexe = ?, competences = ?, photo_profil = ? WHERE id_user = ?");
            $stmt->bind_param("ssssissi", $description, $experience, $formation, $etudes, $sexe, $competences, $photo_profil, $id_user);
        } else {
            // Handle the error if the file was not uploaded
            echo "Error uploading the file.";
            exit();
        }
    } else {
        // Update the profile without changing the profile photo
        $stmt = $conn->prepare("UPDATE utilisateur SET description = ?, experience = ?, formation = ?, etudes = ?, sexe = ?, competences = ? WHERE id_user = ?");
        $stmt->bind_param("ssssssi", $description, $experience, $formation, $etudes, $sexe, $competences, $id_user);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: vous.php");
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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
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
                <div class="col-sm-2" id="logo">
                    <h1><img src="logo/logo_ece.png" height="80" width="146" alt="Logo"></h1>
                </div>
                <div class="col-sm-8" id="logos">
                    <nav>
                        <a href="accueil.php"><img src="logo/accueil.jpg" height="70" width="125" alt="Accueil"></a>
                        <a href="monreseau.php"><img src="logo/reseau.jpg" height="70" width="125" alt="Réseau"></a>
                        <a href="vous.php"><img src="logo/vous2.jpg" height="70" width="125" alt="Vous"></a>
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
               
        <a href="https://www.opodo.fr/travel/secure/#details/16847456699/" target="_blank">
            <img src="https://th.bing.com/th/id/OIP.sMy6FwWi6JbSqdSyp6BDKAHaD4?rs=1&pid=ImgDetMain" alt="Publicité Ryanair" style="max-width: 100%; height: auto;">
        </a>
        
        <br><br>
        
        <a href="https://www.aldi.fr/offres-et-bons-plans.html#2024-05-28" target="_blank">
            <img src="evenements/aldi.jpg" alt="Publicité Ryanair" style="max-width: 100%; height: auto;">
        </a>
    </div>

    <div id="rightcolumn">
        <h3><font size="5">A Propos de nous:</font></h3>
        <font size="3">
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
        </font>

        <p><font size="-1">Fait par: STITOU Ranya, SENOUSSI Ambrine, PUTOD Anna et DEROUICH Shaïma</font></p>
    </div>

    <div id="section">
        <div class="media">
            <div class="media-left">
                <?php if (!empty($photo_profil)): ?>
                    <img src="<?php echo htmlspecialchars($photo_profil); ?>" class="img-circle" alt="Photo profil">
                <?php else: ?>
                    <img src="frontend/logo/photoprofil.png" class="img-circle" alt="Photo profil">
                <?php endif; ?>
            </div>
            <div class="media-body">
                <br><br><br><br>
                <h2 class="media-heading"><font size="6"> <?php echo htmlspecialchars($prenom ?? '') . ' ' . htmlspecialchars($nom ?? ''); ?></font></h2>
                <p style="color: gray;"><font size="3"><?php echo htmlspecialchars($statut ?? ''); ?></font></p>
            </div>
        </div>
        <br>
        <br>
        <div class="container-fluid" >
            <div class="row">
                <font size="3">
                <form method="post" action="">
                    <div id="infoprofil">
                        <div class="col-sm-8 case3" id="description">
                            <h3>Description</h3>
                            <p><?php echo htmlspecialchars($description ?? ''); ?></p>

                        </div>
                        <div class="col-sm-8 case3">
                            <h3>Informations Personnelles</h3>
                            <p>Sexe: <?php echo htmlspecialchars($sexe ?? ''); ?></p>
                            <p>Niveau d'études: <?php echo htmlspecialchars($etudes ?? ''); ?></p>
                            <p>Date de naissance: <?php echo htmlspecialchars($date_naissance ?? ''); ?></p>
                            <p>Email: <?php echo htmlspecialchars($email ?? ''); ?></p>
                        </div>
                        <div class="col-sm-12 case" id="experience">
                            <h3>Expérience</h3>
                            <p><?php echo htmlspecialchars($experience ?? ''); ?></p>
                        </div>
                        <div class="col-sm-12 case" id="formation">
                            <h3>Formation</h3>
                            <p><?php echo htmlspecialchars($formation ?? ''); ?></p>
                        </div>
                        <div class="col-sm-12 case" id="competences">
                            <h3>Compétences</h3>
                            <p><?php echo htmlspecialchars($competences ?? ''); ?></p>
                        </div>
                    </div>
                </form>
            </div>
            <br>
            <div class="col-sm-12 case2" id="modifprofil">
                <h1 style="text-align: center;">Modification</h1>
                <br>
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="col-sm-7 case" id="description_modif" required>
                        <h3>Description</h3>
                        <textarea name="description"></textarea>
                    </div>
                    <div class="col-sm-4 case" id="photo_profil_modif">
                        <h3>Photo de profil</h3>
                        <input type="file" name="photo_profil" required>
                    </div>

                    <div class="col-sm-7 case" id="experience_modif">
                        <h3>Expérience</h3>
                        <textarea name="experience"></textarea>
                    </div>
                    <div class="col-sm-4 case" id="etudes_modif">
                        <h3>Études</h3>
                        <select name="etudes" required>
                            <option value="0" <?php if ($etudes == '0') echo 'selected'; ?>>Bac</option>
                            <option value="1" <?php if ($etudes == '1') echo 'selected'; ?>>Bac +1</option>
                            <option value="2" <?php if ($etudes == '2') echo 'selected'; ?>>Bac +2</option>
                            <option value="3" <?php if ($etudes == '3') echo 'selected'; ?>>Bac +3</option>
                            <option value="4" <?php if ($etudes == '4') echo 'selected'; ?>>Bac +4</option>
                            <option value="5" <?php if ($etudes == '5') echo 'selected'; ?>>Bac +5</option>
                            <option value="6" <?php if ($etudes == '6') echo 'selected'; ?>>Autre</option>
                        </select>
                    </div>

                    <div class="col-sm-7 case" id="formation_modif">
                        <h3>Formation</h3>
                        <textarea name="formation" required></textarea>
                    </div>
                    <div class="col-sm-4 case" id="sexe_modif" required>
                        <h3>Sexe</h3>
                        <label><input type="radio" name="sexe" value="0" <?php if ($sexe == '0') echo 'checked'; ?>> Homme</label>
                        <label><input type="radio" name="sexe" value="1" <?php if ($sexe == '1') echo 'checked'; ?>> Femme</label>
                        <label><input type="radio" name="sexe" value="2" <?php if ($sexe == '2') echo 'checked'; ?>> Autre</label>
                    </div>

                    <div class="col-sm-7 case" id="competences_modif">
                        <h3>Compétences</h3>
                        <textarea name="competences" required></textarea>
                    </div>
                    <div class="col-sm-4">
                        <br>
                    </div>
                    <br>
                    </font>
                    <button type="submit" style="background-color: #028E98; border: none" class="btn btn-primary" value="validation"><font size="3"> Enregistrer</font></button>
                </form>
            </div>
            <div class="col-sm-12" style="text-align: right">
                <form method="post" action="cv/generercv.php">
                    <button type="submit" style="background-color: #028E98; border: none" class="btn btn-primary"><font size="3"> Générer mon CV</font></button>
                </form>
            </div>

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
</div>

</body>
</html>
