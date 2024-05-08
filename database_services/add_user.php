<?php
session_start();

require_once '../config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk'; // Predvolený jazyk
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require '../languages/' . $_SESSION['lang'] . '.php';


// Redirect to login if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['managing_users']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/change_password.css">
    <link rel="stylesheet" href="../css/navbar.css">
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
            <a href="?lang=sk"><img src="../images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 30px"></a>
            ./.
            <a href="?lang=en"><img src="../images/Flag_of_the_United_Kingdom.png" alt="EN"
                                    style="height: 20px; width: 30px"></a>
            <li class="nav-item">
                <a class="nav-link" href="../main_page.php"><?php echo $lang['questions']; ?><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../create_question.php"><?php echo $lang['create_question']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><?php echo $lang['pricing']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#"><?php echo $lang['disabled']; ?></a>
            </li>
        </ul>

        <ul class="navbar-nav">

            <?php
            if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
                echo '<li class="nav-item">
            <a class="nav-link" href="../manage_users.php">' . $lang['manage_users'] . '</a>
          </li>';
            }
            ?>
            <li class="nav-item">
                <a class="nav-link" href="../change_password.php"><?php echo $lang['change_password']; ?></a>
            </li>
            <li class="nav-item">
                <a class="btn btn-danger" href="../logout.php"><?php echo $lang['logout']; ?></a>
            </li>
        </ul>
    </div>
</nav>


<form method="post" action="save_user.php">
    <input type="hidden" name="id" value="<?php echo $user_id; ?>">
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required class="form-control">
    </div>
    <div class="form-group">
        <label>Heslo</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="form-group">
        <label>Role</label>
        <input type="text" name="role" required class="form-control">
    </div>
    <div class="form-group">
        <label>Admin</label>
        <input type="checkbox" name="is_admin" value="1">
    </div>
    <button type="submit" class="btn btn-success">Uložiť</button>
</form>


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