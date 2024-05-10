<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require 'languages/' . $_SESSION['lang'] . '.php';

function getFontSize($count, $maxCount) {
    $minFontSize = 12;
    $maxFontSize = 48;
    if ($maxCount == 0) {
        return $minFontSize;
    }
    $size = ($count / $maxCount) * ($maxCount - $minFontSize) + $minFontSize;
    return round($size);
}

function getArchivedResults($conn, $question_id) {
    $stmt = $conn->prepare("
        SELECT question_id, answer, count, YEAR(created_at) as year 
        FROM answers_archive 
        WHERE question_id = :question_id
    ");
    $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function displayResults($conn, $question) {
    $question_id = $question['id'];
    $question_text = htmlspecialchars($question['question']);
    $question_type = $question['question_type'];
    $answers_display = $question['answers_display'];

    echo "<h2>{$question_text}</h2>";

    // Display current results
    if ($question_type == 'open') {
        $stmt = $conn->prepare("SELECT answer_text as answer, count FROM open_answers WHERE answer_to_question = :question_id");
    } elseif ($question_type == 'defined-answers') {
        $stmt = $conn->prepare("SELECT answer, is_right, count FROM defined_answers WHERE answer_to_question = :question_id");
    }
    $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        $maxCount = max(array_column($results, 'count'));

        if ($answers_display == 'wordCloud') {
            echo "<div id='wordCloudView' class='word-cloud'>";
            foreach ($results as $result) {
                $fontSize = getFontSize($result['count'], $maxCount);
                $correctStyle = isset($result['is_right']) && $result['is_right'] === 'true' ? 'color: green;' : '';
                echo "<span style='font-size: {$fontSize}px; {$correctStyle}'>" . htmlspecialchars($result['answer']) . "</span> ";
            }
            echo "</div>";
        } else { // default to list
            echo "<div id='listView' class='results'>";
            echo "<h3>" . $lang['current_results'] . "</h3>";
            echo "<ul class='results-list'>";
            foreach ($results as $result) {
                $correctClass = isset($result['is_right']) && $result['is_right'] === 'true' ? 'correct' : '';
                echo "<li class='{$correctClass}'><span>" . htmlspecialchars($result['answer']) . "</span> " . htmlspecialchars($result['count']) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    } else {
        echo "<p>" . $lang['no_answers'] . ": $question_id</p>";
    }

    // Display archived results
    $archivedResults = getArchivedResults($conn, $question_id);
    if ($archivedResults) {
        echo "<h3>" . $lang['historical_results'] . "</h3>";
        echo "<table class='table'>";
        echo "<thead><tr><th>" . $lang['year'] . "</th><th>" . $lang['answer'] . "</th><th>" . $lang['count'] . "</th></tr></thead>";
        echo "<tbody>";
        foreach ($archivedResults as $archived) {
            echo "<tr><td>{$archived['year']}</td><td>" . htmlspecialchars($archived['answer']) . "</td><td>{$archived['count']}</td></tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>" . $lang['no_historical_data'] . "</p>";
    }
}

// Fetch all questions
try {
    $stmt = $conn->prepare("SELECT id, question, question_type, answers_display FROM questions");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
}

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['results']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../WEBTE2_Zaverecne_zadanie/css/result_page.css">
</head>
<body>
<header>
    <a href="?lang=sk&question_id=<?php echo $question_id; ?>"><img src="../WEBTE2_Zaverecne_zadanie/images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 40px"></a> /
    <a href="?lang=en&question_id=<?php echo $question_id; ?>"><img src="../WEBTE2_Zaverecne_zadanie/images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 40px"></a>
</header>
<div class="container">
    <h1><?php echo $lang['results']; ?></h1>
    <a href="backup.php" class="btn btn-warning">Run Backup</a>

    <?php
    if (!empty($questions)) {
        foreach ($questions as $question) {
            displayResults($conn, $question);
        }
    } else {
        echo "<p>" . $lang['no_questions'] . "</p>";
    }
    ?>
</div>
</body>
</html>
