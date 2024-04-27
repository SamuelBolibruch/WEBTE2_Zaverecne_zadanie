<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/registration.css">
</head>

<body>
    <form action="#" method="post">
        <h2>Registrácia</h2>
        <label for="username">Meno používateľa:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Heslo:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Potvrdenie hesla:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <input type="submit" value="Registrovať sa">
        <a href="index.html" class="login-link">Späť na prihlásenie</a>
    </form>
</body>

</html>