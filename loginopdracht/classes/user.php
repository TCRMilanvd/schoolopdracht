<?php
    // Functie: classdefinitie User 
    // Auteur: Wigmans

    class User{

        // Eigenschappen 
        public $username;
        public $email;
        private $password;
        
        function SetPassword($password){
            $this->password = $password;
        }
        function GetPassword(){
            return $this->password;
        }

        public function ShowUser() {
            echo "<br>Username: $this->username<br>";
            echo "<br>Password: $this->password<br>";
            echo "<br>Email: $this->email<br>";
        }

        public function RegisterUser(){
            $status = false;
            $errors=[];
            if($this->username != "" || $this->password != ""){

                // Check user exist
                if(true){
                    array_push($errors, "Username bestaat al.");
                } else {
                    // username opslaan in tabel login
                    // INSERT INTO `user` (`username`, `password`, `role`) VALUES ('kjhasdasdkjhsak', 'asdasdasdasdas', '');
                    // Manier 1
                    
                    $status = true;
                }
                            
                
            }
            return $errors;
        }

        function ValidateUser(){
            $errors=[];

            if (empty($this->username)){
                array_push($errors, "Invalid username");
            } else if (empty($this->password)){
                array_push($errors, "Invalid password");
            }

            // Test username > 3 tekens en < 50 tekens
            
            return $errors;
        }

public function LoginUser(){
    session_start();

    define("DATABASE", "OOPlogin");
    define("SERVERNAME", "localhost");
    define("USERNAME", "root");
    define("PASSWORD", "");
    define("CRUD_TABLE", "users");

    try {
        // Maak de databaseverbinding
            $servername = SERVERNAME;
            $username = USERNAME;
            $password = PASSWORD;
            $dbname = DATABASE;
        
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                //echo "Connected successfully";
                return $conn;
            } 
            catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        
        // Haal de gebruiker op
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            // Controleer of het wachtwoord overeenkomt (voor plain text, gebruik liever password_hash())
            if ($user['password'] == $this->password) {  
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

    } catch(PDOException $e) {
        echo "Verbindingsfout: " . $e->getMessage();
    }

    return false;
}


        // Check if the user is already logged in
        public function IsLoggedin() {
            // Check if user session has been set
            
            return false;
        }

        public function GetUser($username){
            
		    // Doe SELECT * from user WHERE username = $username
            if (false){
                //Indien gevonden eigenschappen vullen met waarden uit de SELECT
                $this->username = 'Waarde uit de database';
            } else {
                return NULL;
            }   
        }

        public function Logout(){
            session_start();
            // remove all session variables
           

            // destroy the session
            
            header('location: index.php');
        }


    }

    

?>