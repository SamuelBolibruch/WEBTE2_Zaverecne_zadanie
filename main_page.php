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
    <link href="https://cdn.datatables.net/v/dt/dt-2.0.1/r-3.0.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main_page.css">
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
                <li class="nav-item active">
                    <a class="nav-link" href="#">Otázky<span class="sr-only">(current)</span></a>
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
        <h3>Definované otázky</h3>

        <table id='questionsTable'>
            <thead>
                <tr>
                    <th>Otázka</th>
                    <th>Kód otázky</th>
                    <th>Aktívna</th>
                    <th>Typ otázky</th>
                    <th>Dátum vytvorenia</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <div id="qrCodeModal" class="modal">
        <div class="modal-content">
            <!-- Obsah modálneho okna -->
            <p id='questionCode' class='modalText'></p>
            <p id='questionAdress' class='modalText'></p>
            <div id="questionQrCode"></div>

        </div>
    </div>

    
    <!-- Váš vlastný JS kód -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.0.1/r-3.0.0/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/qrcode.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.js'></script>
    <script src='scripts/main_page.js'></script>
</body>

</html>