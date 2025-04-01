<?php
// Dit bestand bevat het formulier voor het inloggen.
// Het verwerken van het formulier staat in login_check.php.
// Dit bestand bevat ook een link naar register.php om een nieuw account aan te maken.
 
session_start();
 
// database verbiding
$dsn = 'mysql:host=localhost;dbname=2fabarry';
$user = 'root';
$pass = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,];
 
include 'GoogleAuthenticator.php';
 
use PHPGangsta\GoogleAuthenticator;
 
$ga = new GoogleAuthenticator();
 
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $code = $_POST['code'];
 
    // Verbind met de database
    $pdo = new PDO($dsn, $user, $pass, $options);
 
    // Controleer gebruikersnaam en wachtwoord
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
 
    if ($user && password_verify($password, $user['password'])) {
        // Controleer de 2FA code
        $secret = $user['2fa_secret'];
        if ($ga->verifyCode($secret, $code, 4)) { // 4 = 4*30sec clock tolerance
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid 2FA code.';
        }
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<br>
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="code">2FA Code:</label>
        <input type="text" id="code" name="code" required><br>
        <button type="submit">Login</button>
    </form></br>    
    <a href="register.php">Registeren</a>
</body>
</html>