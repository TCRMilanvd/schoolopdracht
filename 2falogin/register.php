<?php

// Een sessie is een manier om gegevens op te slaan voor een gebruiker die de website bezoekt.
session_start();

// Database verbinding
$dsn = "mysql:host=localhost;dbname=2fabarry";
$user = "root";
$pass = "";

// Dit zorgt ervoor dat de PDO exceptions gooit als er een fout is
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false];

include "GoogleAuthenticator.php";

// Gebruik de GoogleAuthenticator class
use PHPGangsta\GoogleAuthenticator;

// Hier wordt de secret key aangemaakt
$ga = new GoogleAuthenticator();
$qrCodeUrl = "";
$secret = "";

// Maak een if die kijkt of de request method een POST is.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // De variabele $username haalt de waarde op van de post variabele username.
    $username = $_POST["username"];
    // Maak password_has om het wachtwoord te hashen
    $password = password_hash( $_POST["password"],  PASSWORD_DEFAULT);
    $secret = $ga->createSecret();

    // Verbind met de database
    $pdo = new PDO(dsn: $dsn, username: $user, password: $pass, options: $options);

    // Voeg de gebruiker aan de database
    $stmt = $pdo->prepare(query: "INSERT INTO users (username, password, 2fa_secret) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $secret]);

    // Genereer de QR-code URL
    $qrCodeUrl = $ga->getQRCodeGoogleUrl("wumpus.club :", $secret);
}


// Hier wordt de secret key aangemaakt
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wumpus.club</title>
    <h1>Register</h1>
    <form method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required> <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required> <br>
        <button type="submit">Register</button>
    </form>
    <!-- Maak een if functie die alleen verder als er een QR code is gegenereerd. -->
    <?php if ($qrCodeUrl): ?>

    <h3>Registratie succesvol! Scan deze QR code met Google Authenticator:</h3>
    <!-- Maak een afbeelding met de QR code -->
    <img src="<?php echo $qrCodeUrl; ?>" alt="QR code"><br>
    <!-- Laat de secret key zien -->
    <p>Sla de geheime sleutel op: <?php echo $secret; ?></p>

    <?php endif; ?>

    <a href="login.php">Login</a>
</head>
<body>
    
</body>
</html>