<?php
session_start();
require_once "config.php";

$servername = "localhost";
$username = "xsefcik";
$password = "totojenajlepsieheslo1";
$database = "zaverecne_zadanie";

$dsn = "mysql:host=$servername;dbname=$database;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(array("message" => "Database connection failed."));
    exit;
}

// Nastavenie hlavičiek pre správne spracovanie JSON
header('Content-Type: application/json');

try {

    $data = json_decode(file_get_contents("php://input"), true);

    // Overenie, či 'question_id' a 'note' sú nastavené
    if (!isset($data["question_id"]) || !isset($data["note"])) {
        throw new Exception("Missing question_id or note.");
    }

    $question_id = $data["question_id"];
    $note = $data["note"];

    // Logovanie prijatých údajov
    error_log("Received question_id: " . $question_id);
    error_log("Received note: " . $note);

    // Začiatok transakcie
    $pdo->beginTransaction();

    // Zistenie typu otázky a email používateľa
    $stmt_question_info = $pdo->prepare("SELECT question_type, user_email FROM questions WHERE id = :question_id");
    $stmt_question_info->bindParam(":question_id", $question_id, PDO::PARAM_STR);
    $stmt_question_info->execute();
    $question_info = $stmt_question_info->fetch(PDO::FETCH_ASSOC);

    if (!$question_info) {
        throw new Exception("Question not found for question_id: " . $question_id);
    }

    $question_type = $question_info['question_type'];
    $user_email = $question_info['user_email'];

    // Vkladanie záznamov do answers_archive na základe typu otázky
    if ($question_type === 'open') {
        $sql_insert = "INSERT INTO answers_archive (question_id, user_email, answer, created_at, note, count) 
                        SELECT answer_to_question, :user_email, answer_text, NOW(), :note, count 
                        FROM open_answers 
                        WHERE answer_to_question = :question_id";
    } elseif ($question_type === 'defined-answers') {
        $sql_insert = "INSERT INTO answers_archive (question_id, user_email, answer, created_at, note, count) 
        SELECT answer_to_question, :user_email, answer, NOW(), :note, count 
        FROM defined_answers 
        WHERE answer_to_question = :question_id";
    } else {
        throw new Exception("Unknown question type: " . $question_type);
    }

    // Vykonanie vkladacieho dotazu
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->bindParam(":question_id", $question_id, PDO::PARAM_STR);
    $stmt_insert->bindParam(":user_email", $user_email, PDO::PARAM_STR);
    $stmt_insert->bindParam(":note", $note, PDO::PARAM_STR);
    $stmt_insert->execute();


    // Odstránenie údajov z príslušnej tabuľky odpovedí
    if ($question_type === 'open') {
        $stmt_delete = $pdo->prepare("DELETE FROM open_answers WHERE answer_to_question = :question_id");
    } elseif ($question_type === 'defined-answers') {
        //$stmt_delete = $pdo->prepare("DELETE FROM defined_answers WHERE answer_to_question = :question_id");
        $stmt_delete = $pdo->prepare("UPDATE defined_answers SET count = 0 WHERE answer_to_question = :question_id");
    }

    $stmt_delete->bindParam(":question_id", $question_id, PDO::PARAM_STR);
    $stmt_delete->execute();

    // Potvrdenie transakcie
    $pdo->commit();

    echo json_encode(array("message" => "Voting closed successfully."));
} catch (Exception $e) {
    // Rollback the transaction if something went wrong
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(array("message" => "Error closing voting: " . $e->getMessage()));
}
?>