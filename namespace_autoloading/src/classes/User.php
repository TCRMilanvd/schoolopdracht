<?php
namespace Login\classes;
use PDO;
// Functie: classdefinitie User
// Auteur: Wigmans

class User {

    // Eigenschappen
    public $username;
    public $email;
    public $role;
    private $password;

    // Database connectie functie
    private function connectDB() {
        try {
            $conn = new PDO("mysql:host=localhost;dbname=ooplogin", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $conn;
        } catch (PDOException $e) {
            die("Databaseverbinding mislukt: " . $e->getMessage());
        }
    }

    function SetPassword($password) {
        $this->password = $password;
    }

    function GetPassword() {
        return $this->password;
    }

    public function ShowUser() {
        echo "<br>Username: $this->username<br>";
        echo "<br>Password: $this->password<br>";
        echo "<br>Email: $this->email<br>";
    }

    public function RegisterUser() {
        $status = false;
        $errors = [];

        // Controleer of velden zijn ingevuld
        if (empty($this->username) || empty($this->password)) {
            array_push($errors, "Gebruikersnaam en wachtwoord zijn verplicht.");
            return $errors;
        }

        $conn = $this->connectDB();

        // Controleer of de gebruikersnaam al bestaat
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        if ($stmt->fetch()) {
            array_push($errors, "Gebruikersnaam bestaat al.");
            return $errors;
        }

        // Hash het wachtwoord voor veiligheid
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
        $role = "user"; // Standaardrol, kan later worden aangepast

        // SQL-query met prepared statement
        $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $conn->prepare($sql);

        // Bind parameters en voer de query uit
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $status = true;
        } else {
            array_push($errors, "Er is een fout opgetreden bij het registreren.");
        }

        return $status ? [] : $errors;
    }

    public function LoginUser() {
        session_start();
        $conn = $this->connectDB();

        // Haal de gebruiker op
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            // Controleer of het wachtwoord overeenkomt
            if (password_verify($this->password, $user['password'])) {
                $_SESSION['username'] = $user['username']; // Sessie instellen
                $_SESSION['role'] = $user['role'];

                // Redirect naar de homepage
                header("Location: index.php");
                exit();
            } else {
                echo "Fout: Onjuist wachtwoord.";
            }
        } else {
            echo "Fout: Gebruiker niet gevonden.";
        }

        return false;
    }

    public function IsLoggedin() {
        return isset($_SESSION['username']);
    }

    public function GetUser($username) {
        $conn = $this->connectDB();

        // Haal de gebruiker op
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            // Eigenschappen vullen met databasewaarden
            $this->username = $user['username'];
            $this->email = $user['email'];
        } else {
            return NULL;
        }
    }

    public function Logout() {
        session_start();
        session_unset();
        session_destroy();
        header('location: index.php');
    }

    public function ValidateUser() {
        $errors = [];

        // Controleer of de gebruikersnaam is ingevuld en voldoet aan de lengte-eisen
        if (empty($this->username)) {
            array_push($errors, "Gebruikersnaam is verplicht.");
        } elseif (strlen($this->username) < 3 || strlen($this->username) > 50) {
            array_push($errors, "Gebruikersnaam moet tussen 3 en 50 tekens lang zijn.");
        }

        // Controleer of het wachtwoord is ingevuld
        if (empty($this->password)) {
            array_push($errors, "Wachtwoord is verplicht.");
        } elseif (strlen($this->password) < 6) {
            array_push($errors, "Wachtwoord moet minimaal 6 tekens bevatten.");
        }

        return $errors;
    }

}
?>
