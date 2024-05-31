<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ece_in";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_user_id = $_SESSION['id_user'];

$sql = "SELECT id_user, nom, prenom FROM utilisateur WHERE id_user != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>


</head>
<body>
    <div id="container">
    <div id="wrapper">
        <div id="nav">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-2" id="logo">
                        <h1><img src="logo/logo_ece.png" height="80" width="146" alt="Logo"></h1>
                    </div>
                    <div class="col-sm-9" id="logos">
                        <nav>
                            <a href="accueil.php"><img src="logo/accueil.jpg" height="70" width="125" alt="Accueil"></a>
                            <a href="monreseau.php"><img src="logo/reseau.jpg" height="70" width="125" alt="Réseau"></a>
                            <a href="vous.php"><img src="logo/vous.jpg" height="70" width="125" alt="Vous"></a>
                            <a href="notifications.php"><img src="logo/notification.jpg" height="70" width="125" alt="Notifications"></a>
                            <a href="messagerie.php"><img src="logo/messagerie2.jpg" height="70" width="125" alt="Messagerie"></a>
                            <a href="emploi.php"><img src="logo/emploi.jpg" height="70" width="125" alt="Emploi"></a>
                            <a href="../backend/connexion/connexion.html"><img src="logo/deconnexion.jpg" height="70" width="125" alt="Deconnexion"></a>
                        </nav>
                    </div>

                </div>
            </div>
        </div>
        <div id="leftcolumn">
            <h3>Discussions Récentes</h3>
            <ul>
                <?php foreach ($friends as $friend) { ?>
                    <li class="friend" data-id="<?php echo $friend['id_user']; ?>">
                        <?php echo $friend['nom'] . " " . $friend['prenom']; ?>
                    </li>
                <?php } ?>
            </ul>
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
            <h1>Conversation</h1>
            <div id="chatbox"></div>
            <form id="message_form">
                <input name="usermsg" type="text" id="usermsg" />
                <input type="submit" value="Envoyer" />
                <input type="hidden" id="recipient_id" />
            </form>
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

    <script>
        document.querySelectorAll('.friend').forEach(friend => {
            friend.addEventListener('click', function() {
                document.getElementById('recipient_id').value = this.getAttribute('data-id');
                loadMessages();
            });
        });

        document.getElementById('message_form').addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        function loadMessages() {
            const recipientId = document.getElementById('recipient_id').value;
            if (!recipientId) return;

            fetch(`load_messages.php?friend_id=${recipientId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('chatbox').innerHTML = data;
                });
        }

        function sendMessage() {
            const recipientId = document.getElementById('recipient_id').value;
            const usermsg = document.getElementById('usermsg').value;

            if (!recipientId || !usermsg) return;

            const formData = new FormData();
            formData.append('message', usermsg);
            formData.append('recipient_id', recipientId);

            fetch('post.php', {
                method: 'POST',
                body: formData
            })
            .then(() => {
                document.getElementById('usermsg').value = '';
                loadMessages();
            });
        }
    </script>
</body>
</html>
