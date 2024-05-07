<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk'; // Predvolený jazyk
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require 'languages/' . $_SESSION['lang'] . '.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
<header>
    <a href="?lang=sk"><img src="images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 40px"></a> /
    <a href="?lang=en"><img src="images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 40px"></a>
</header>


    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <h2><?php echo $lang['login']; ?></h2>
        <label for="email"><?php echo $lang['username']; ?></label>
        <input type="text" id="email" name="email" required>
        <label for="password"><?php echo $lang['password']; ?></label>
        <input type="password" id="password" name="password" required>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="admin_login" id="admin_login"> <?php echo $lang['login_as_admin']; ?>
            </label>
        </div>

        <input type="submit" value="<?php echo $lang['submit']; ?>">
        <a href="registration.php" class="registration-link"><?php echo $lang['register']; ?></a>


    </form>

    <?php
    
    require_once 'config.php';

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("location: main_page.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $admin_login = isset($_POST['admin_login']);  // Kontrola, či bol začiarknutý checkbox

        $user_query = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($user_query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($admin_login && !$user['is_admin']) {
                    echo "Nemáte oprávnenia na prihlásenie ako administrátor.";
                } else {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["email"] = $email;
                    $_SESSION["is_admin"] = $user['is_admin'];  // Uloženie informácie o admin právach do session
                    header("location: main_page.php");
                }
            } else {
                echo "Zadané údaje nie sú správne.";
            }
        } else {
            echo "Používateľ s daným emailom nebol nájdený.";
        }
    }

    ?>

</body>

</html>