<?php
require __DIR__ . '/../vendor/autoload.php';
use classes\User;

// Is de register button aangeklikt?
if(isset($_POST['register-btn'])){
	$user = new User();
	$errors=[];

	$user->username = $_POST['username'];
	$user->email = $_POST['email']; // Email toevoegen
	$user->SetPassword($_POST['password']);
	$user->role = 0; // Standaard rol op 0 (normale gebruiker)

	$user->ShowUser();

	// Databaseverbinding maken
	$conn = $user->ConnectDb();

	// Validatie gegevens
	$errors = $user->ValidateUser();

	if(count($errors) == 0){
		// Register user met databaseverbinding
		$errors = $user->RegisterUser($conn);
	}
	
	if(count($errors) > 0){
		$message = "";
		foreach ($errors as $error) {
			$message .= $error . "\n";
		}
		
		echo "<script>alert('$message');</script>";
	} else {
		echo "<script>alert('Registratie succesvol!'); window.location.href='login_form.php';</script>";
	}
}

?>

<!DOCTYPE html>
<html lang="en">
	<head>
	</head>
<body>

	<h3>PHP - PDO Login and Registration</h3>
	<hr/>
	
	<form action="" method="POST">	
		<h4>Register here...</h4>
		<hr>
		
		<label>Username</label>
		<input type="text" name="username" />
		<br>
		<label>E-mail:</label>
    	<input type="email" name="email" required>
		<br>
		<label>Password</label>
		<input type="password" name="password" />
		<br>
		<button type="submit" name="register-btn">Register</button>
		
		
	</form>
		
</body>
</html>