<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

$id_user = $_SESSION['id_user'];

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "ece_in";

// Créer une connexion
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Préparer et exécuter la requête SQL
$stmt = $conn->prepare("SELECT u.nom, u.prenom, u.date_naissance, u.statut, p.photo_profil, p.description, p.etudes, p.sexe, p.competences 
                        FROM utilisateur u 
                        JOIN profil p ON u.id_user = p.id_user 
                        WHERE u.id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($nom, $prenom, $date_naissance, $statut, $photo_profil, $description, $etudes, $sexe, $competences);
$stmt->fetch();
$stmt->close();
$conn->close();

// Définir les rôles
$roles = ["Admin", "Prof", "Élève"];
$role_name = isset($statut) ? $roles[$statut] : 'Inconnu';

// Utiliser des valeurs par défaut si les variables sont nulles
$nom = $nom ?? '';
$prenom = $prenom ?? '';
$date_naissance = $date_naissance ?? '1970-01-01'; // date par défaut
$photo_profil = $photo_profil ?? 'path/to/default/profile/photo.png'; // chemin par défaut de l'image
$description = $description ?? '';
$etudes = $etudes ?? 0; // niveau d'études par défaut
$sexe = $sexe ?? 0; // sexe par défaut
?>

<!DOCTYPE html>
<html>
<head>
    <title>ECE In - Profil</title>
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
        <div class="row">
            <div class="col-4 col-sm-2">
                <img src="logo/logo_ece.PNG" alt="ECE In Logo" class="logo img-fluid">
            </div>
            <div class="col-4 col-sm-5"></div>
            <div class="col-4 col-sm-5" style="padding-top:15px;">
                <a href="vous.php" style="padding-right: 30px;"> Profil </a>
                <a href="reseau.html" style="padding-right: 30px;"> Réseau </a>
                <a href="emplois.html" style="padding-right: 30px;"> Emplois </a>
                <a href="messagerie.html" style="padding-right: 30px;"> Messagerie </a>
                <a href="notifications.html" style="padding-right: 30px;"> Notifications </a>
            </div>
        </div>
    </div>

    <div id="section" style="padding-left:10px;">
        <form method="POST" action="update_profile.php" enctype="multipart/form-data">
            <div class="row">
                <div class="col-sm-2" id="photo_profil">
                    <h3> Ma Photo de profil </h3>
                    <img src="<?php echo htmlspecialchars($photo_profil); ?>" alt="Photo de profil" width="150">
                    <input type="file" name="photo_profil" accept="image/*">
                </div>
                <div class="col-sm-10" id="description">
                    <h3> Description </h3>
                    <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="col-sm-11 case" id="Informations_generales">
                    <h3> Informations générales </h3>
                    <p> Nom: <input type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>"></p>
                    <p> Prénom: <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>"></p>
                    <p> Date de naissance: <input type="date" name="date_naissance" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($date_naissance))); ?>"></p>
                    <p> Niveau d'étude: 
                        <select name="etudes">
                            <option value="0" <?php echo $etudes == 0 ? 'selected' : ''; ?>>Niveau 0</option>
                            <option value="1" <?php echo $etudes == 1 ? 'selected' : ''; ?>>Niveau 1</option>
                            <option value="2" <?php echo $etudes == 2 ? 'selected' : ''; ?>>Niveau 2</option>
                            <option value="3" <?php echo $etudes == 3 ? 'selected' : ''; ?>>Niveau 3</option>
                            <option value="4" <?php echo $etudes == 4 ? 'selected' : ''; ?>>Niveau 4</option>
                            <option value="5" <?php echo $etudes == 5 ? 'selected' : ''; ?>>Niveau 5</option>
                            <option value="6" <?php echo $etudes == 6 ? 'selected' : ''; ?>>Niveau 6</option>
                        </select>
                    </p>
                    <p> Sexe: 
                        <select name="sexe">
                            <option value="0" <?php echo $sexe == 0 ? 'selected' : ''; ?>>Non spécifié</option>
                            <option value="1" <?php echo $sexe == 1 ? 'selected' : ''; ?>>Masculin</option>
                            <option value="2" <?php echo $sexe == 2 ? 'selected' : ''; ?>>Féminin</option>
                        </select>
                    </p>
                    <p> Statut: <?php echo htmlspecialchars($role_name); ?></p>
                </div>
                <div class="col-sm-11 case" id="Experience">
                    <h3> Expérience </h3>
                    <textarea name="experience">Écrivez votre expérience ici...</textarea>
                </div>
                <div class="col-sm-11 case" id="Formation">
                    <h3> Formation </h3>
                    <textarea name="formation">Écrivez votre formation ici...</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>

    <div id="footer">
        <footer>
            <h3>Nous Contacter: </h3>
            <table>
                <td style="padding-right:350px;padding-left:310px;">
                    <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/
            @48.851108,2.2859627,17z/data=!3m1!4b1!4m6!3m5!1s0x47e6701b486bb253:0x61e9cc6979f93fae!8m2!3d48.
            8511045!4d2.2885376!16s%2Fg%2F11bw3xcdpj?entry=ttu"><img src="logo/carte_map.PNG" width="500" height="280"></a>
                </td>

                <td style="font-size: 18px; text-align: right; padding :20px;">
                    <p>Par Mail: <a href="mailto : ECEIN@ece.fr"> ECEIN@ece.fr</a></p>
                    <p>Par Téléphone: <a href="tel:0144390600">01 44 39 06 00</a></p>
                    <p>En Nos Locaux: <a href="https://www.google.com/maps/place/10+Rue+Sextius+Michel,+75015+Paris/
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
