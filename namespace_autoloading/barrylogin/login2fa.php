<?php
//een sessie is een manier op gegevens op te slaan tussen de server en de client
session_start();

//database verbinding
$dsn = 'mysql:host=localhost;dbname=untitled';
$user = 'root';
$pass = '';

//dit zorgt ervoor dat de PDO exeptions gooit  als er een fout is
$option = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,];

include_once "GoogleAuthenticator.php";
//gebruikt de google authenticator class
use PHPGangsta\GoogleAuthenticator;;
//hier word de secret aangemaakt
$ga = new GoogleAuthenticator();
//maakt 2 variabelen aan namelijk qrcodeurl en secret
$qrCodeUrl = '';
$secret = '';

//maak een if die kijkt of de request method een POST is
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash(password: $_POST['password'], algo: PASSWORD_DEFAULT);
    $secret = $ga->createSecret();
}

//verbind de database
$pdo  = new PDO($dsn, $user, $pass, $option);

//voeg de gebruiker toe aan de database
$stmt = $pdo->prepare("INSERT INTO users (username, password, 2de_secret) values (?, ?, ?)");
$stmt->execute([$username, $password, $secret]);

// genereer de QR-code URL
$qrCodeUrl = $ga->getQRCodeGoogleUrl('TCRHELDEN', $secret);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
<h1>Register</h1>
<form action="" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Password:</label>
    <input type="text" id="password" name="password" required><br>
    <button type="submit">Register</button>
</form>

<?php if ($qrCodeUrl): ?>

<h3>Registratie succesvol scan de qr code</h3>
<img src="<?php echo $qrCodeUrl; ?>" alt="QR code"><br>
<p>Sla de geheime sleutel op: <?php echo $secret; ?></p>

<?php endif; ?>

<a href="login.php">login</a>
</body>
</html>
