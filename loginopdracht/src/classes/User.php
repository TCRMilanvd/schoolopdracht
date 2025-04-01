<?php
    namespace classes;

    use PDO;
    use PDOException;

    class User {
        public $username;
        public $email;
        public $role;
        private $password;

        function SetPassword($password){
            $this->password = $password;
        }

        function GetPassword(){
            return $this->password;
        }

        public function ShowUser() {
            echo "<br>Username: $this->username<br>";
            echo "<br>Email: $this->email<br>";
            echo "<br>Role: $this->role<br>";
        }

        public function ConnectDb() {
            try {
                return new PDO("mysql:host=localhost;dbname=klastweelogin", "root", "", [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        public function RegisterUser($conn) {
            $errors = $this->ValidateUser();
        
            if (count($errors) > 0) {
                return $errors;
            }

            // Controleer of de gebruikersnaam al bestaat
            $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE username = ?");
            $stmt->execute([$this->username]);

            if ($stmt->fetch()) {
                array_push($errors, "Gebruikersnaam bestaat al.");
            } else {
                $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO gebruikers (username, password, email, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$this->username, $hashedPassword, $this->email, 0]); // Rol altijd 0 bij registratie
            }
        
            return $errors;
        }

        function ValidateUser(){
            $errors = [];
        
            if (empty($this->username)) {
                array_push($errors, "Gebruikersnaam mag niet leeg zijn.");
            } elseif (strlen($this->username) < 3 || strlen($this->username) > 50) {
                array_push($errors, "Gebruikersnaam moet tussen 3 en 50 tekens lang zijn.");
            }
        
            if (empty($this->password)) {
                array_push($errors, "Wachtwoord mag niet leeg zijn.");
            }

            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Ongeldig e-mailadres.");
            }
        
            return $errors;
        }

        public function LoginUser($conn) {
            $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE username = ?");
            $stmt->execute([$this->username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($this->password, $user['password'])) {
                session_start();
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                return true;
            } else {
                return false;
            }
        }

        public function IsLoggedIn() {
            return isset($_SESSION['username']);
        }

        public function GetUser($conn, $username){
            $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($user) {
                $this->username = $user['username'];
                $this->email = $user['email'];
                $this->role = $user['role'];
                return $user;
            } else {
                return null;
            }
        }

        public function Logout(){
            session_start();
            session_unset();
            session_destroy();
            header('location: index.php');
            exit();
        }
    }
?>

