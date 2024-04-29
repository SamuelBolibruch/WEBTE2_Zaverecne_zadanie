<?php
session_start(); // Začatie relácie

require_once '../config.php';

// Skontrolujte, či užívateľ je prihlásený
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    
    // Získanie emailu prihláseného používateľa
    $email = $_SESSION["email"];
    
    // Získanie id z parametra
    $questionId = $_GET['id'];

    try {
        // Príprava príkazu DELETE
        $stmt = $conn->prepare("DELETE FROM questions WHERE id = :questionId AND user_email = :email");
        
        // Bindovanie parametrov
        $stmt->bindParam(':questionId', $questionId);
        $stmt->bindParam(':email', $email);
    
        // Vykonanie príkazu
        $stmt->execute();
    
        echo "Záznam bol úspešne vymazaný z databázy.";
    } catch(PDOException $e) {
        echo "Chyba pri vymazávaní záznamu: " . $e->getMessage();
    }

}
?>
