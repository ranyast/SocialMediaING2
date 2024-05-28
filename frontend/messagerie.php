<!DOCTYPE html>
<html>
<head>
    <title>ECE In Messagerie</title>
    <meta charset="utf-8"/>
    <link href="ECEIn.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="logo/logo_ece.ico" type="image/x-icon" />
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <style>
        #nav {}
        #footer {}
        #wrapper {}
        #section {}
    </style>
</head>
<body>
<div id="wrapper">
    <h1 class="logo">ECE In</h1>
    <div id="nav">
        <nav>
            <a href="accueil.html"><img src="logo/accueil.jpg" height="56" width="100"></a>
            <a href="monreseau.html"><img src="logo/reseau.jpg" height="56" width="100"></a>
            <a href="vous.html"><img src="logo/vous.jpg" height="56" width="100"></a>
            <a href="notifications.html"><img src="logo/notification.jpg" height="56" width="100"></a>
            <a href="messagerie.php"><img src="logo/messagerie2.jpg" height="56" width="100"></a>
            <a href="emploi.html"><img src="logo/emploi.jpg" height="56" width="100"></a>
        </nav>
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

    <div id="leftcolumn">
        <footer>
            ICI LES DISCUSSIONS DE L'UTILISATEUR
        </footer>
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

            $myfile = fopen(__DIR__ . "/log.html", "a") or die("Impossible d'ouvrir le fichier!" . __DIR__ . "/log.html");
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
            <div id="wrapper">
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

                    setInterval(loadLog, 2500);

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
</div>
</body>
</html>
