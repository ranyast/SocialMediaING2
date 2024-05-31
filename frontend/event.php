<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assurez-vous de bien valider et nettoyer les données reçues
    $date = $_POST['eventDate'];
    $description = $_POST['eventDescription'];

    $response = [
        'success' => true,
        'type' => 'Événement',
        'date' => $date,
        'description' => $description,
        'media' => null
    ];

    echo json_encode($response);
}
?>
