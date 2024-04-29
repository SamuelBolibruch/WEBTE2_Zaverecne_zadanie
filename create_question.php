<?php
session_start(); // Začatie relácie

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$email = $_SESSION["email"]; // Načítanie emailu z relácie

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/create_question.css">
    <link rel="stylesheet" href="css/navbar.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- <a class="navbar-brand" href="#">Navbar</a> -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="main_page.php">Otázky<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="create_question.php">Vytvoriť otázku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" href="#">Disabled</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <div class="nav-link"><?php echo $email; ?></div> <!-- Zobrazenie emailu -->
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="change_password.php">Zmena hesla</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger" href="logout.php">Odhlásiť sa</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Selectbox -->
        <select class="custom-select" id="questionType" name="questionType">
            <option value="1">Otázka s výberom správnej odpovede</option>
            <option value="2">Otázka s otvorenou krátkou odpoveďou</option>
        </select>

        <div id="answerQuestionContainer" class="choose-answer-question-container add-question-container">
            <form id="closedQuestionForm">
                <label for="question1" class='bold'>Otázka:</label>
                <input type="text" id="question1" name="question1" required>

                <label for="subject1" class='bold'>Skratka predmetu:</label>
                <input type="text" id="subject1" name="subject1" required>

                <!-- Skryté vstupné pole pre dátum vytvorenia -->
                <input type="hidden" id="creationDate1" name="creationDate1" value="">

                <!-- Skryté vstupné pole pre typ otázky -->
                <input type="hidden" id="questionType1" name="questionType1" value="defined-answers">

                <!-- Skryté vstupné pole pre otvorenosť otázky -->
                <input type="hidden" id="isOpen1" name="isOpen1" value="true">

                <input type="hidden" id="numOfAnswers1" name="numOfAnswers1" value="2">

                <!-- Možnosti odpovedí -->
                <div class='bold'>Odpovede:</div>
                <div id="answerOptions">
                    <div class="answer-option">
                        <label for="answer1">Možnosť 1</label>
                        <input type="text" id="answer1" name="answer1" required>
                        <input type="checkbox" id="correctAnswer1" name="correctAnswer1">
                        <label for="correctAnswer1">Správna možnosť</label>
                    </div>
                    <div class="answer-option">
                        <label for="answer2">Možnosť 2</label>
                        <input type="text" id="answer2" name="answer2" required>
                        <input type="checkbox" id="correctAnswer2" name="correctAnswer2">
                        <label for="correctAnswer2">Správna možnosť</label>
                    </div>
                </div>

                <!-- Tlačidlo na pridanie ďalšej možnosti -->
                <button type="button" id="addOptionButton"
                    style="background-color: transparent; border: none; color: gray; cursor: pointer;">Pridať ďalšiu
                    možnosť</button>

                <!-- Tlačidlo na odoslanie formulára -->
                <input type="submit" value="Vytvoriť otázku">
            </form>

        </div>


        <div id="openQuestionContainer" class="open-question-container add-question-container">
            <form id="openQuestionForm">
                <!-- Vstup pre otázku -->
                <label for="question" class='bold'>Otázka:</label>
                <input type="text" id="question" name="question" required>

                <label for="subject" class='bold'>Skratka predmetu:</label>
                <input type="text" id="subject" name="subject" required>

                <!-- Skryté vstupné pole pre dátum vytvorenia -->
                <input type="hidden" id="creationDate" name="creationDate" value="">

                <input type="hidden" id="questionType" name="questionType" value="open">

                <input type="hidden" id="isOpen" name="isOpen" value="true">

                <!-- Typ otázky -->
                <div class="show-type-div">
                    <div class='bold'>Zobrazovanie odpovedí:</div>

                    <div class="item">
                        <input type="radio" id="list" name="answerDisplay" value="list" required>
                        <label for="list">Položky zoznamu</label>
                    </div>

                    <div class="item">
                        <input type="radio" id="wordCloud" name="answerDisplay" value="wordCloud">
                        <label for="wordCloud">Word Cloud</label>
                    </div>
                </div>

                <!-- Tlačidlo na odoslanie formulára -->
                <input type="submit" value="Vytvoriť otázku">
            </form>
        </div>

        <div class='add-question-container' id='message-div'>
            <h6 id='message'></h6>
        </div>

    </div>

    <script src='scripts/create_question.js'></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>

</html>