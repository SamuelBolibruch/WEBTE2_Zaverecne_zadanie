<?php
$servername = "localhost";
$username = "xsefcik";
$password = "totojenajlepsieheslo1";
$database = "zaverecne_zadanie";

try {
    // Pripojenie k databáze pomocou PDO
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Nastavenie režimu výnimiek pre PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage(); // V prípade chyby vypíš chybové hlásenie
}
?>
