<?php
session_start(); // Začatie relácie
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(401);
    exit;
}

if (isset($_GET['id'])) {
    $questionId = $_GET['id'];
} else {
    header("location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM questions WHERE id=:questionId");
$stmt->bindParam(':questionId', $questionId);
$stmt->execute();

// Získanie hodnôt stĺpcov do premenných
$stmt->bindColumn('question', $question);
$stmt->bindColumn('subject', $subject);
$stmt->bindColumn('question_type', $questionType);
$stmt->bindColumn('is_active', $isActive);
$stmt->bindColumn('answers_display', $answersDisplay);
$stmt->bindColumn('user_email', $user_email);
$stmt->bindColumn('right_answers', $numOfCorrectAnswers);

$stmt->fetch();

$currentDate = new DateTime();
$creationDate = $currentDate->format('Y-m-d');

// OPEN ANSWERS QUESTION
if($questionType === "open"){
    function generateUniqueCode($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCharacter = $characters[rand(0, $charactersLength - 1)];
            $code .= $randomCharacter;
        }
        return $code;
    }

    function isUniqueCodeInDatabase($uniqueCode, $connection)
    {
        try {
            // Príprava príkazu SELECT
            $stmt = $connection->prepare("SELECT * FROM questions WHERE id=:id");

            // Bindovanie parametrov
            $stmt->bindParam(':id', $uniqueCode);

            // Vykonanie príkazu
            $stmt->execute();

            // Získanie počtu riadkov výsledku
            $rowCount = $stmt->rowCount();

            // Ak je počet riadkov viac ako 0, záznam existuje
            if ($rowCount > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Chyba pri vyhľadávaní záznamu: " . $e->getMessage();
            return false; // Ak nastane chyba, vrátime false
        }
    }

    try {
        do {
            // Generovanie unikátneho kódu
            $uniqueID = generateUniqueCode();

            // Kontrola, či taký kód už existuje v databáze
            $codeExist = isUniqueCodeInDatabase($uniqueID, $conn);
        } while ($codeExist); // Opakovať, pokiaľ kód existuje v databáze

        // Príprava príkazu INSERT
        $stmt = $conn->prepare("INSERT INTO questions (id, question, subject, creation_date, question_type, is_active, answers_display, user_email)
                               VALUES (:uniqueID, :question, :subject, :creationDate, :questionType, :isActive, :answersDisplay, :email)");

        // Bindovanie parametrov
        $stmt->bindParam(':uniqueID', $uniqueID);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':creationDate', $creationDate);
        $stmt->bindParam(':questionType', $questionType);
        $stmt->bindParam(':isActive', $isActive);
        $stmt->bindParam(':answersDisplay', $answersDisplay);
        $stmt->bindParam(':email', $user_email);

        // Vykonanie príkazu
        $stmt->execute();

        header("location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Chyba pri vkladaní záznamu: " . $e->getMessage();
    }

}
// DEFINED ANSWERS QUESTION
else{
    function generateUniqueCode($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCharacter = $characters[rand(0, $charactersLength - 1)];
            $code .= $randomCharacter;
        }
        return $code;
    }

    function isUniqueCodeInDatabase($uniqueCode, $connection)
    {
        try {
            // Príprava príkazu SELECT
            $stmt = $connection->prepare("SELECT * FROM questions WHERE id=:id");

            // Bindovanie parametrov
            $stmt->bindParam(':id', $uniqueCode);

            // Vykonanie príkazu
            $stmt->execute();

            // Získanie počtu riadkov výsledku
            $rowCount = $stmt->rowCount();

            // Ak je počet riadkov viac ako 0, záznam existuje
            if ($rowCount > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Chyba pri vyhľadávaní záznamu: " . $e->getMessage();
            return false; // Ak nastane chyba, vrátime false
        }
    }

    try {
        do {
            // Generovanie unikátneho kódu
            $uniqueID = generateUniqueCode();

            // Kontrola, či taký kód už existuje v databáze
            $codeExist = isUniqueCodeInDatabase($uniqueID, $conn);
        } while ($codeExist); // Opakovať, pokiaľ kód existuje v databáze

        // Príprava príkazu INSERT
        $stmt = $conn->prepare("INSERT INTO questions (id, question, subject, creation_date, question_type, is_active, answers_display, user_email, right_answers)
                               VALUES (:uniqueID, :question, :subject, :creationDate, :questionType, :isActive, :answersDisplay, :email, :numOfCorrectAnswers)");

        // Bindovanie parametrov
        $stmt->bindParam(':uniqueID', $uniqueID);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':creationDate', $creationDate);
        $stmt->bindParam(':questionType', $questionType);
        $stmt->bindParam(':isActive', $isActive);
        $stmt->bindParam(':answersDisplay', $answersDisplay);
        $stmt->bindParam(':email', $user_email);
        $stmt->bindParam(':numOfCorrectAnswers', $numOfCorrectAnswers);

        // Vykonanie príkazu
        $stmt->execute();


        $result = $conn->prepare("SELECT answer, is_right FROM defined_answers WHERE answer_to_question =:questionId");
        $result->bindParam(':questionId', $questionId);
        $result->execute();


        $stmt = $conn->prepare("INSERT INTO defined_answers (answer, answer_to_question, is_right, count) VALUES (:answer, :questionID, :isRight, 0)");

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $answer = $row["answer"];
            $isRight = $row["is_right"];

            // Bindování parametrů pro odpověď
            $stmt->bindParam(':answer', $answer);
            $stmt->bindParam(':questionID', $uniqueID);
            $stmt->bindParam(':isRight', $isRight);

            // Vykonání příkazu pro odpověď
            $stmt->execute();
        }

        header("location: index.php");
        exit;

    } catch (PDOException $e) {
        echo "Chyba pri vkladaní záznamu: " . $e->getMessage();
    }

}


