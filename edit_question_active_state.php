<?php
session_start(); // Začatie relácie

require_once 'config.php';

// Skontrolujte, či užívateľ je prihlásený
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Kontrola, či je požiadavka typu PUT
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Načítanie dát z tela požiadavky
    $email = $_SESSION["email"];
    $data = json_decode(file_get_contents("php://input"), true);

    // Ak dáta neboli úspešne dekódované
    if($data === null) {
        echo "Chyba: Neplatné dáta";
        http_response_code(400); // Bad Request
        exit;
    }

    // Načítanie parametrov z dát
    $questionId = $data['question_id'];
    $questionState = $data['question_state'];
    $questionStateByWord = '';
    if ($questionState === true || $questionState === "true") {
        $questionStateByWord = 'true';
    } else {
        $questionStateByWord = 'false';
    }

    try {
        // Aktualizácia stĺpca is_active v tabuľke questions
        $stmt = $conn->prepare("UPDATE questions SET is_active = :is_active WHERE id=:id AND user_email=:email");
        $stmt->bindParam(':id', $questionId);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':is_active', $questionStateByWord); // Aktualizujeme is_active podľa questionState
        $stmt->execute();

        // Ak chcete overiť, či bola aktualizácia úspešná, môžete vrátiť správu o úspechu
        echo json_encode(array("message" => "Stĺpec is_active bol úspešne aktualizovaný"));
        $conn->commit();

    } catch(PDOException $e) {
        // Ak nastane chyba pri prístupe k databáze, vráť chybové hlásenie
        http_response_code(500);
        echo json_encode(array("message" => "Chyba pri prístupe k databáze: " . $e->getMessage()));
    }
} else {
    // Ak metóda nie je PUT
    echo "Chyba: Neplatná metóda";
    http_response_code(405); // Method Not Allowed
}
?>
