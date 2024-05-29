<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$database = "social_media";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

if (!$db_handle) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}


$nom = isset($_POST["nom"]) ? trim($_POST["nom"]) : "";
$prenom = isset($_POST["prenom"]) ? trim($_POST["prenom"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$mot_de_passe = isset($_POST["mot_de_passe"]) ? trim($_POST["mot_de_passe"]) : "";
$date_naissance = isset($_POST["date_naissance"]) ? trim($_POST["date_naissance"]) : "";
$code_postal = isset($_POST["code_postal"]) ? trim($_POST["code_postal"]) : "";
$telephone = isset($_POST["telephone"]) ? trim($_POST["telephone"]) : "";
$niveau_etudes = isset($_POST["niveau_etudes"]) ? trim($_POST["niveau_etudes"]) : "";
$roles = isset($_POST["role"]) ? $_POST["role"] : [];

$erreur = false;
$messageErreur = "";

if ($db_found) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if ($nom == "" || $prenom == "" || $email == "" || $mot_de_passe == "" || $date_naissance == "" || $code_postal == "" || $telephone == "" || $niveau_etudes == "" || empty($roles)) {
            $erreur = true;
            $messageErreur .= "<p>Tous les champs sont obligatoires.</p>";
        } else {

            if (empty($roles)) {
                $erreur = true;
                $messageErreur .= "<p>Veuillez sélectionner au moins un rôle.</p>";
            } else {

                if (strpos($roles, ',') !== false) {
                    $erreur = true;
                    $messageErreur .= "<p>Veuillez sélectionner un seul rôle.</p>";

                    $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, date_naissance, code_postal, telephone, terminale, niveau_etudes, roles) VALUES ('$nom','$prenom','$email','$mot_de_passe','$date_naissance','$code_postal','$telephone','$niveau_etudes','$roles')";

                    switch ($niveau_etudes) {
                        case 'terminale':
                            $sql .= "1, 0, 0, 0, 0, 0, 0";
                            break;
                        case 'bac_plus_1':
                            $sql .= "0, 1, 0, 0, 0, 0, 0";
                            break;
                        case 'bac_plus_2':
                            $sql .= "0, 0, 1, 0, 0, 0, 0";
                            break;
                        case 'bac_plus_3':
                            $sql .= "0, 0, 0, 1, 0, 0, 0";
                            break;
                        case 'bac_plus_4':
                            $sql .= "0, 0, 0, 0, 1, 0, 0";
                            break;
                        case 'bac_plus_5':
                            $sql .= "0, 0, 0, 0, 0, 1, 0";
                            break;
                        case 'autre':
                            $sql .= "0, 0, 0, 0, 0, 0, 1";
                            break;
                        default:
                            $sql .= "0, 0, 0, 0, 0, 0, 0";
                            break;
                    }

                    $sql .= ", ";
                    if (in_array("admin", $roles)) {
                        $sql .= "1, 0, 0";
                    } elseif (in_array("prof", $roles)) {
                        $sql .= "0, 1, 0";
                    } elseif (in_array("eleve", $roles)) {
                        $sql .= "0, 0, 1";
                    }

                    $sql .= ")";

                    // Exécuter la requête
                    if (mysqli_query($db_handle, $sql)) {
                        echo "Enregistrement réussi.";
                    } else {
                        echo "Erreur: " . $sql . "<br>" . mysqli_error($db_handle);
                    }
                }
            }
        }
    }
}
    else {
    echo "Database not found";
}
?>
