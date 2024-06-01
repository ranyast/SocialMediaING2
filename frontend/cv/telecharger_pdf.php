<?php

$xml = simplexml_load_file('cv.xml');


require('fpdf.php');


$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Curriculum Vitae', 0, 1, 'C');
$pdf->Ln(10); 


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Informations Personnelles', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Nom: ' . $xml->nom, 0, 1);
$pdf->Cell(0, 10, 'Prénom: ' . $xml->prenom, 0, 1);
$pdf->Cell(0, 10, 'Date de Naissance: ' . $xml->date_naissance, 0, 1);
$pdf->Cell(0, 10, 'Email: ' . $xml->email, 0, 1);
$pdf->Cell(0, 10, 'Statut: ' . $xml->statut, 0, 1);
$pdf->Cell(0, 10, 'Sexe: ' . $xml->sexe, 0, 1);


$pdf->Cell(0, 10, 'Photo de Profil: ' . $xml->photo_profil, 0, 1);


$pdf->Ln(10); 
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Informations Supplementaires', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Description: ' . $xml->description, 0, 1);
$pdf->Cell(0, 10, 'Experience: ' . $xml->experience, 0, 1);
$pdf->Cell(0, 10, 'Formation: ' . $xml->formation, 0, 1);
$pdf->Cell(0, 10, 'Etudes: ' . $xml->etudes, 0, 1);
$pdf->Cell(0, 10, 'Compétences: ' . $xml->competences, 0, 1);


$pdf->Output('I', 'cv.pdf');
?>
