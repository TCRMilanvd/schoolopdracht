<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <center>
        <h1></h1>Dashboard</h1>
        <p>Welkom, Gebruiker!</p>
        <p>(Ik wou ervoor zorgen dat het de gebruikersnaam liet zien, maar werkte niet :()</p>
        <p><a href="logout.php">Uitloggen</a></p>
        <p><a href="register.php">Registreren</a></p>
        <p><a href="login.php">Login</a></p>
    </center>
</body>
</html>