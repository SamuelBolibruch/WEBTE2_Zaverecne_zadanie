<?php
session_start(); // Začatie relácie

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: main_page.php");
    exit;
}

require_once 'config.php';

// Kontrola, či bola odoslaná POST požiadavka
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $is_post_request = true;
} else {
    $is_post_request = false;
}

if ($is_post_request) {
    // Získanie hodnôt z formulára
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Dotaz na overenie existencie používateľa s daným emailom v databáze
    $check_email_query = "SELECT * FROM users WHERE email=:email";

    // Príprava a vykonanie dotazu s použitím PDO
    $stmt = $conn->prepare($check_email_query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Počet riadkov vrátených dotazom
    $num_rows = $stmt->rowCount();

    // Ak bol nájdený používateľ s daným emailom
    if ($num_rows > 0) {
        echo "Používateľ s týmto emailom už existuje.";
    } else {
        // Príprava dotazu na vloženie nového záznamu
        $insert_user_query = "INSERT INTO users (email, password) VALUES (:email, :password)";

        // Príprava a vykonanie dotazu na vloženie nového používateľa s použitím PDO
        $stmt = $conn->prepare($insert_user_query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);

        // Vykonanie dotazu
        if ($stmt->execute()) {
            echo "Nový používateľ bol úspešne vytvorený.";
            $_SESSION["loggedin"] = true;
            $_SESSION["email"] = $email;
            header("location: main_page.php");
        } else {
            echo "Nastala chyba pri vytváraní nového používateľa.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/registration.css">
</head>

<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id='registration_form'>
        <h2>Registrácia</h2>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Heslo:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Potvrdenie hesla:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <input type="submit" value="Registrovať sa">
        <a href="index.php" class="login-link">Späť na prihlásenie</a>
    </form>
    <script src='scripts/registration.js'></script>
</body>

</html>