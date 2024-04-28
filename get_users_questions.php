<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(401);
    exit;
}

$email = $_SESSION["email"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Príprava príkazu SELECT na získanie otázok pre daného používateľa
        $stmt = $conn->prepare("SELECT * FROM questions WHERE user_email=:email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Získanie výsledkov z databázy
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Konvertovanie výsledkov na formát JSON
        $json_questions = json_encode($questions);

        // Nastavenie správneho HTTP hlavičky pre formát JSON
        header('Content-Type: application/json');

        // Vrátenie otázok vo formáte JSON
        echo $json_questions;
    } catch(PDOException $e) {
        // Ak nastane chyba pri prístupe k databáze, vráť chybové hlásenie
        http_response_code(500);
        echo json_encode(array("message" => "Chyba pri prístupe k databáze: " . $e->getMessage()));
    }
}
?>
