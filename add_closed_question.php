<?php
session_start(); // Začatie relácie

require_once 'config.php';

function generateUniqueCode($length = 5) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $randomCharacter = $characters[rand(0, $charactersLength - 1)];
        $code .= $randomCharacter;
    }
    return $code;
}

function isUniqueCodeInDatabase($uniqueCode, $connection) {
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
    } catch(PDOException $e) {
        echo "Chyba pri vyhľadávaní záznamu: " . $e->getMessage();
        return false; // Ak nastane chyba, vrátime false
    }
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Získanie údajov z formulára
    $question = $_POST["question1"];
    $subject = $_POST["subject1"];
    $creationDate = $_POST["creationDate1"];
    $questionType = $_POST["questionType1"];
    $isOpen = $_POST["isOpen1"];
    $numOfAnswers = $_POST["numOfAnswers1"];

    $email = $_SESSION["email"];


    try {
        do {
            // Generovanie unikátneho kódu
            $uniqueID = generateUniqueCode();
    
            // Kontrola, či taký kód už existuje v databáze
            $codeExist = isUniqueCodeInDatabase($uniqueID, $conn);
        } while ($codeExist); // Opakovať, pokiaľ kód existuje v databáze
        
        // Príprava príkazu INSERT
        $stmt = $conn->prepare("INSERT INTO questions (id, question, subject, creation_date, question_type, is_active, user_email)
                               VALUES (:uniqueID, :question, :subject, :creationDate, :questionType, :isOpen, :email)");
        
        // Bindovanie parametrov
        $stmt->bindParam(':uniqueID', $uniqueID);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':creationDate', $creationDate);
        $stmt->bindParam(':questionType', $questionType);
        $stmt->bindParam(':isOpen', $isOpen);
        $stmt->bindParam(':email', $email);
    
        // Vykonanie príkazu
        $stmt->execute();
    
        echo "Záznam bol úspešne vložený do databázy.";

        $stmt = $conn->prepare("INSERT INTO defined_answers (answer, answer_to_question) VALUES (:answer, :questionID)");

        // Pre každú odpoveď vytvorte záznam
        for ($i = 1; $i <= $numOfAnswers; $i++) {
            $answer = $_POST["answer" . $i];

            // Bindovanie parametrov pre odpoveď
            $stmt->bindParam(':answer', $answer);
            $stmt->bindParam(':questionID', $uniqueID);

            // Vykonanie príkazu pre odpoveď
            $stmt->execute();
        }

        echo "Záznamy odpovedí boli úspešne vložené do databázy.";

    } catch(PDOException $e) {
        echo "Chyba pri vkladaní záznamu: " . $e->getMessage();
    }
    

    // Výpis všetkých možností odpovedí
    // for ($i = 1; $i <= $numOfAnswers; $i++) {
    //     $answer = $_POST["answer" . $i];
    //     echo "Možnosť " . $i . ": " . $answer . "<br>";
    // }
} else {
    // Ak formulár nie je odoslaný pomocou POST metódy, zobrazte chybu
    echo "Chyba: formulár nebol správne odoslaný.";
}
