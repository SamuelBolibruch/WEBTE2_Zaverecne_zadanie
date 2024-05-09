<?php
session_start(); // Začatie relácie

require_once '../config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk'; // Predvolený jazyk
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require '../languages/' . $_SESSION['lang'] . '.php';

// Skontrolujte, či užívateľ je prihlásený
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

    $email = $_GET['email'];

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

        echo $lang['record_deleted_success'];
    } catch(PDOException $e) {
        echo $lang['delete_error'] . $e->getMessage();
    }

}
?>
