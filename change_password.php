<?php
        session_start(); // Start session
        
        require_once 'config.php';

        // Redirect to login if not logged in
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            header("location: index.php");
            exit;
        }

        $is_post_request = $_SERVER["REQUEST_METHOD"] == "POST";

        $email = $_SESSION["email"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change your password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/change_password.css">
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
                <li class="nav-item">
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
                <li class="nav-item active">
                    <a class="nav-link" href="#">Zmena hesla</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger" href="logout.php">Odhlásiť sa</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id='change-password-form'>
            <h2>Zmena hesla</h2>
            <label for="oldPassword">Staré heslo:</label>
            <input type="password" id="oldPassword" name="oldPassword" required>
            <label for="newPassword">Nové heslo:</label>
            <input type="password" id="newPassword" name="newPassword" required>
            <label for="confirmNewPassword">Potvrď nové heslo:</label>
            <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
            <input type="submit" value="Zmeniť heslo">
        </form>

        <?php
        // Get email from session
        
        if ($is_post_request) {
            // Get form data
            $password = $_POST["oldPassword"];
            $newPassword = $_POST["newPassword"];
            $confirmNewPassword = $_POST["confirmNewPassword"];

            // Query to get hashed password from database
            $password_query = "SELECT password FROM users WHERE email=:email";
            $stmt = $conn->prepare($password_query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $hashed_password = $result['password'];

                // Verify old password
                if (password_verify($password, $hashed_password)) {
                    // Check if new password is different from old password
                    if ($password !== $newPassword) {
                        // Old password is correct, proceed with changing the password
                        // Make sure new password and confirm new password match
                        if ($newPassword === $confirmNewPassword) {
                            // Hash the new password
                            $hashed_new_password = password_hash($newPassword, PASSWORD_DEFAULT);

                            // Update password in the database
                            $update_query = "UPDATE users SET password=:password WHERE email=:email";
                            $update_stmt = $conn->prepare($update_query);
                            $update_stmt->bindParam(':password', $hashed_new_password);
                            $update_stmt->bindParam(':email', $email);

                            if ($update_stmt->execute()) {
                                // Password updated successfully
                                echo "Heslo bolo úspešne zmenené.";
                            } else {
                                // Error updating password
                                echo "Chyba pri zmene hesla.";
                            }
                        } else {
                            // New password and confirm new password do not match
                            echo "Potvrdzovacie heslo sa nezhoduje s novým heslom.";
                        }
                    } else {
                        // New password is the same as old password
                        echo "Nové heslo sa musí líšiť od starého hesla.";
                    }
                } else {
                    // Old password is incorrect
                    echo "Zadali ste chybné heslo.";
                }
            } else {
                // Error fetching password from database
                echo "Chyba pri práci s databázou.";
            }
        }
        ?>


    </div>

    <script src='scripts/change_password.js'></script>
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