<?php

require_once 'config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk'; // Predvolený jazyk
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require 'languages/' . $_SESSION['lang'] . '.php';

// Kontrola prítomnosti parametra v URL adrese
if (isset($_GET['parameter'])) {
    // Získanie hodnoty parametra z URL adresy
    $parameter = $_GET['parameter'];
    // Odstránenie začínajúceho lomítka z parametra
    $parameter = ltrim($parameter, '/');
    // Vypíš hodnotu parametra
    if ($parameter !== '') {
        try {
            // Príprava príkazu SELECT na získanie otázok pre daného používateľa
            $stmt = $conn->prepare("SELECT * FROM questions WHERE id=:id");
            $stmt->bindParam(':id', $parameter);
            $stmt->execute();
    
            // Získanie výsledkov z databázy
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Konvertovanie výsledkov na formát JSON
            $json_questions = json_encode($questions);
    
            // Vrátenie otázok vo formáte JSON
            echo $json_questions;
        } catch(PDOException $e) {
            // Ak nastane chyba pri prístupe k databáze, vráť chybové hlásenie
            http_response_code(500);
            echo json_encode(array("message" => "Chyba pri prístupe k databáze: " . $e->getMessage()));
        }

        echo "<h2>Parameter: $parameter</h2>";
    } else {
        echo "<h2>Parameter nie je prítomný.</h2>";
    }
} else {
    // Ak parameter nie je prítomný, vypíš správu
    echo "<h2>Parameter nie je prítomný.</h2>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo $lang['answer']; ?></title>
</head>

<body>
    <h1>Informácia zo servera:</h1>

</body>

</html>