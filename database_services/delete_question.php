<?php
/*
session_start();

require_once '../config.php';

// Nastavenie predvoleného jazyka
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}

// Zmena jazyka na základe GET požiadavky
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require '../languages/' . $_SESSION['lang'] . '.php';

// Skontrolovanie, či je používateľ prihlásený
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Kontrola, či je metóda DELETE
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Získanie údajov z DELETE požiadavky
    parse_str(file_get_contents("php://input"), $_DELETE);

    // Kontrola, či existujú kľúče v $_DELETE
    $email = isset($_DELETE['email']) ? $_DELETE['email'] : null;
    $questionId = isset($_DELETE['id']) ? $_DELETE['id'] : null;

    // Logovanie prijatých údajov
    error_log("Received DELETE request for question ID: $questionId and email: $email");

    // Inicializácia PDO
    try {
        $dsn = "mysql:host=$servername;dbname=$database;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        error_log("Database connection successful.");
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(array("message" => "Database connection failed."));
        exit;
    }

    // Kontrola, či email a questionId nie sú null
    if ($email && $questionId) {
        try {
            // Začiatok transakcie
            $pdo->beginTransaction();
            error_log("Transaction started.");

            // Vymazať závislé záznamy z answers_archive
            $stmt = $pdo->prepare("DELETE FROM answers_archive WHERE question_id = :questionId");
            $stmt->bindParam(':questionId', $questionId);
            $stmt->execute();
            error_log("Deleted from answers_archive for question ID: $questionId");

            // Vymazať závislé záznamy z defined_answers
            $stmt = $pdo->prepare("DELETE FROM defined_answers WHERE answer_to_question = :questionId");
            $stmt->bindParam(':questionId', $questionId);
            $stmt->execute();
            error_log("Deleted from defined_answers for question ID: $questionId");

            // Vymazať závislé záznamy z open_answers
            $stmt = $pdo->prepare("DELETE FROM open_answers WHERE answer_to_question = :questionId");
            $stmt->bindParam(':questionId', $questionId);
            $stmt->execute();
            error_log("Deleted from open_answers for question ID: $questionId");

            // Vymazať otázku z questions
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = :questionId AND user_email = :email");
            $stmt->bindParam(':questionId', $questionId);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            error_log("Deleted from questions for question ID: $questionId and email: $email");

            // Potvrdenie transakcie
            $pdo->commit();
            error_log("Transaction committed.");

            echo $lang['record_deleted_success'];
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Transaction rolled back. Error: " . $e->getMessage());
            echo $lang['delete_error'] . $e->getMessage();
        }
    } else {
        error_log("Missing email or question ID.");
        echo json_encode(array("message" => "Missing email or question ID."));
    }
}
*/?>

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
