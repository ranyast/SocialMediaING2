<?php
session_start();

if (isset($_SESSION['name'])) {
    $text = $_POST['usermsg'];
    
    // Configurer le fuseau horaire
    date_default_timezone_set('Europe/Paris');
    
    // Sanitize user input
    $safe_text = htmlspecialchars(stripslashes($text));
    
    // Format the message
    $current_time = date("H:i");
    $username = htmlspecialchars($_SESSION['name']);
    $text_message = "<div class='msgln'><span class='chat-time'>$current_time</span> <b class='user-name'>$username</b>: $safe_text<br></div>";
    
    // Append the message to log.html
    $file_path = __DIR__ . "/log.html";
    $myfile = fopen($file_path, "a");
    
    if ($myfile) {
        fwrite($myfile, $text_message);
        fclose($myfile);
    } else {
        die("Impossible d'ouvrir le fichier $file_path");
    }
}
?>
