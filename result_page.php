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

$question_id = isset($_POST['question_id']) ? $_POST['question_id'] : (isset($_GET['question_id']) ? $_GET['question_id'] : '');
$question_text = '';
$question_type = '';

if (!empty($question_id)) {
    try {
        // Získání textu otázky a typu otázky
        $stmt = $conn->prepare("SELECT question, question_type FROM questions WHERE id = :id");
        $stmt->bindParam(':id', $question_id, PDO::PARAM_STR);
        $stmt->execute();
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($question) {
            $question_text = $question['question'];
            $question_type = $question['question_type'];
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($question_id)) {
    $answer = $_POST['answer'];

    try {
        if ($question_type == 'open') {
            $stmt = $conn->prepare("INSERT INTO open_answers (answer_to_question, answer_text) VALUES (:question_id, :answer)");
        } elseif ($question_type == 'defined-answers') {
            $stmt = $conn->prepare("INSERT INTO defined_answers (answer_to_question, answer, is_right) VALUES (:question_id, :answer, 'false')");
        }
        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
        $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        http_response_code(500);
        echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
    }
}

function getFontSize($count, $maxCount) {
    $minFontSize = 12;
    $maxFontSize = 48;
    if ($maxCount == 0) {
        return $minFontSize;
    }
    $size = ($count / $maxCount) * ($maxFontSize - $minFontSize) + $minFontSize;
    return round($size);
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
    <script>
        function toggleView(view) {
            document.getElementById('listView').style.display = (view === 'list') ? 'block' : 'none';
            document.getElementById('wordCloudView').style.display = (view === 'cloud') ? 'block' : 'none';
            document.getElementById('listButton').classList.toggle('active', view === 'list');
            document.getElementById('cloudButton').classList.toggle('active', view === 'cloud');
        }
    </script>
</head>
<body>
<header>
    <a href="?lang=sk&question_id=<?php echo $question_id; ?>"><img src="../WEBTE2_Zaverecne_zadanie/images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 40px"></a> /
    <a href="?lang=en&question_id=<?php echo $question_id; ?>"><img src="../WEBTE2_Zaverecne_zadanie/images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 40px"></a>
</header>
<div class="container">
    <h1><?php echo $lang['results']; ?></h1>

    <?php if (!empty($question_text)): ?>
        <h2><?php echo $lang['question']; ?>: <?php echo htmlspecialchars($question_text); ?></h2>
    <?php endif; ?>
    <?php
    if (!empty($question_id)) {
        try {
            if ($question_type == 'open') {
                $stmt = $conn->prepare("SELECT answer_text as answer, COUNT(*) as count FROM open_answers WHERE answer_to_question = :question_id GROUP BY answer_text");
            } elseif ($question_type == 'defined-answers') {
                $stmt = $conn->prepare("SELECT answer, COUNT(*) as count FROM defined_answers WHERE answer_to_question = :question_id GROUP BY answer");
            }
            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                $maxCount = max(array_column($results, 'count'));
                echo "<div class='view-toggle'>";
                echo "<button id='listButton' onclick='toggleView(\"list\")' class='active'>" . $lang['listview'] . "</button>";
                echo "<button id='cloudButton' onclick='toggleView(\"cloud\")'>" . $lang['word_cloud_view'] . "</button>";
                echo "</div>";

                echo "<div id='listView' class='results'>";
                echo "<h2>" . $lang['results_for'] . ": $question_id</h2>";
                echo "<ul>";
                foreach ($results as $result) {
                    echo "<li class='result-item'>" . htmlspecialchars($result['answer']) . ": " . htmlspecialchars($result['count']) . "</li>";
                }
                echo "</ul>";
                echo "</div>";

                echo "<div id='wordCloudView' class='word-cloud' style='display: none;'>";
                foreach ($results as $result) {
                    $fontSize = getFontSize($result['count'], $maxCount);
                    echo "<span style='font-size: {$fontSize}px'>" . htmlspecialchars($result['answer']) . "</span>";
                }
                echo "</div>";
            } else {
                echo "<p>" . $lang['no_answers'] . ": $question_id</p>";
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>" . $lang['no_answers'] . "</p>";
    }
    ?>
    <a href="index.php"><?php echo $lang['main_page']; ?></a>
</div>
</body>
</html>