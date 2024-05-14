<?php
require_once 'tcpdf/tcpdf.php'; // Cesta k TCPDF knižnici

// Inicializace TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Nastavení informací o dokumentu
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Návod');
$pdf->SetSubject('Návod');
$pdf->SetKeywords('TCPDF, PDF, návod');

// Nastavení písma
$pdf->SetFont('dejavusans', '', 12);

// Dodání obsahu pro generování PDF
$pdf->AddPage();

// Načtení obsahu z manual_container
$manual_content = '<h3>' . $lang["manual"] . '</h3>' . $_POST['content'];

// Vložení obsahu do PDF
$pdf->writeHTML($manual_content, true, false, true, false, '');

// Uložení PDF do proměnné
$filename = 'manual_' . date('YmdHis') . '.pdf';
$pdf->Output($filename, 'F');

// Návrat názvu souboru pro stahování
echo $filename;
?>
