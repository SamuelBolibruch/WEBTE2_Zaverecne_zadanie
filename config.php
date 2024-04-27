<?php
$servername = "localhost";
$username = "xbolibruch";
$password = "heslo123456789";
$database = "nobel_prizes";

try {
    // Pripojenie k databáze pomocou PDO
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Nastavenie režimu výnimiek pre PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; // Pre kontrolu úspešného pripojenia
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage(); // V prípade chyby vypíš chybové hlásenie
}
?>
