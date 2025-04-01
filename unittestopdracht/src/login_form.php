<?php
require __DIR__ . '/../vendor/autoload.php';
use classes\User;
    if (isset($_POST['login-btn'])) {
        
        $user = new User();
        $conn = $user->ConnectDb();

        $user->username = $_POST['username'];
        $user->SetPassword($_POST['password']);

        if ($user->LoginUser($conn)) {
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Login mislukt: onjuiste gegevens.');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<body>
	<h3>PHP - PDO Login and Registration</h3>
	<hr/>
    <form action="" method="POST">    
        <label>Username</label>
        <input type="text" name="username" required />
        <br>
        <label>Password</label>
        <input type="password" name="password" required />
        <br>
        <button type="submit" name="login-btn">Login</button>
        <br>
        <a href="register_form.php">Registreren</a>
    </form>
</body>
</html>
