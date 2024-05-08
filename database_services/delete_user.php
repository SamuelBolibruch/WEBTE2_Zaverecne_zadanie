<?php
session_start();

require_once '../config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
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

// Získanie ID používateľa z URL
$userEmail = isset($_GET['email']) ? $_GET['email'] : null; // Toto používajte len ak ste istí, že vstup je bezpečný, inak je lepšie použiť napr. filter_var()

if ($userEmail) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE email = :email");
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();

        // Presmerovanie späť na stránku so zoznamom po úspešnom vymazaní
        header("location: ../manage_users.php?status=success_deleted");
    } catch (PDOException $e) {
        header("location: ../manage_users.php?status=error&message=" . urlencode($e->getMessage()));
    }
} else {
    header("location: ../manage_users.php?status=error&message=InvalidEmail");
}

?>