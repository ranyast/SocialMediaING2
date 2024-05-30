<!DOCTYPE html>
<html>
<head>
    <title>ECE In Messagerie</title>
    <meta charset="utf-8"/>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">

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
                        <a href="vous.php"><img src="logo/vous.jpg" height="56" width="100" alt="Vous"></a>
                        <a href="notifications.php"><img src="logo/notification.jpg" height="56" width="100" alt="Notifications"></a>
                        <a href="messagerie.php"><img src="logo/messagerie2.jpg" height="56" width="100" alt="Messagerie"></a>
                        <a href="emploi.php"><img src="logo/emploi.jpg" height="56" width="100" alt="Emploi"></a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div id="leftcolumn">
        <footer>
            ICI LES DISCUSSIONS DE L'UTILISATEUR
        </footer>
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
        <p>
            Carrousel avec les événements futurs de l'école
        </p>
        <?php
        session_start();

        if (isset($_GET['logout'])) {
            $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>" .
                $_SESSION['name'] . "</b> a quitté la session de chat.</span><br></div>";

            $myfile = fopen(__DIR__. "/log.html", "a") or die("Impossible d'ouvrir le fichier!" . __DIR__ . "/log.html");
            fwrite($myfile, $logout_message);
            fclose($myfile);
            session_destroy();
            sleep(1);
            header("Location: messagerie.php");
        }

        if (isset($_POST['enter'])) {
            if ($_POST['name'] != "") {
                $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
            } else {
                echo '<span class="error">Veuillez saisir votre nom</span>';
            }
        }

        function loginForm()
        {
            echo
                '<div id="loginform">
                <p>Veuillez saisir votre nom pour continuer!</p>
                <form action="messagerie.php" method="post">
                <label for="name">Nom: </label>
                <input type="text" name="name" id="name" />
                <input type="submit" name="enter" id="enter" value="Soumettre" />
                </form>
                </div>';
        }

        if (!isset($_SESSION['name'])) {
            loginForm();
        } else {
            ?>
            <div id="wrapper2">
                <div id="menu">
                    <p class="welcome">Bienvenue, <b><?php echo $_SESSION['name']; ?></b></p>
                    <p class="logout"><a id="exit" href="#">Quitter la conversation</a></p>
                </div>
                <div id="chatbox">
                    <?php
                    if (file_exists("log.html") && filesize("log.html") > 0) {
                        $contents = file_get_contents("log.html");
                        echo $contents;
                    }
                    ?>
                </div>
                <form name="message" action="">
                    <input name="usermsg" type="text" id="usermsg" />
                    <input name="submitmsg" type="submit" id="submitmsg" value="Envoyer" />
                </form>
            </div>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script type="text/javascript">
                $(document).ready(function () {
                    $("#submitmsg").click(function () {
                        var clientmsg = $("#usermsg").val();
                        $.post("post.php", { text: clientmsg });
                        $("#usermsg").val("");
                        return false;
                    });

                    function loadLog() {
                        var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20;
                        $.ajax({
                            url: "log.html",
                            cache: false,
                            success: function (html) {
                                $("#chatbox").html(html);
                                var newscrollHeight = $("#chatbox")[0].scrollHeight - 20;
                                if (newscrollHeight > oldscrollHeight) {
                                    $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal');
                                }
                            }
                        });
                    }

                    setInterval(loadLog, 1000);

                    $("#exit").click(function () {
                        var exit = confirm("Voulez-vous vraiment mettre fin à la session ?");
                        if (exit == true) {
                            window.location = "messagerie.php?logout=true";
                        }
                    });
                });
            </script>
        <?php
        }
        ?>
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
</body>
</html>