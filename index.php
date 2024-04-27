<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <h2>Prihlásenie</h2>
        <label for="email">Meno používateľa:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Heslo:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Prihlásiť sa">
        <a href="registration.php" class="registration-link">Chcem sa zaregistrovať</a>
    </form>

    <?php
    session_start(); // Začatie relácie
    
    require_once 'config.php';


    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("location: main_page.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $is_post_request = true;
    } else {
        $is_post_request = false;
    }

    if ($is_post_request) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Pripravenie dotazu na získanie používateľa z databázy podľa emailu
        $user_query = "SELECT * FROM users WHERE email=:email";

        // Príprava a vykonanie dotazu s použitím PDO
        $stmt = $conn->prepare($user_query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Získanie používateľa z databázy
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kontrola, či bol používateľ nájdený
        if ($user) {
            // Kontrola zhody hesiel
            if (password_verify($password, $user['password'])) {
                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $email;
                header("location: main_page.php");
            } else {
                // Heslo sa nezhoduje
                echo "Zadané údaje nie sú správne.";
            }
        } else {
            // Používateľ s daným emailom nebol nájdený
            echo "Zadané údaje nie sú správne.";
        }
    }

    ?>

</body>

</html>