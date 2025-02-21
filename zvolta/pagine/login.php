<?php
    if($_POST) {
        // Connessione al database
        $conn = new mysqli("localhost", "root", "", "zvolta");

        if($conn->error) {
            echo "Errore di connessione: " . $conn->error;
            die();
        } else {
            // Prendo i parametri dalla richiesta
            $username = $_POST["username"];
            $password = $_POST["password"];
            $sql = "SELECT password_utente FROM utente WHERE username='$username'";
            $result = $conn->query($sql);
            
            if($result->num_rows === 0) {
                echo "Username non presente";
            } else {
                // Leggo la password del database
                $row = $result->fetch_assoc();
                $pwddb = $row["password_utente"];
                
                if(password_verify($password, $pwddb)) {
                    session_start();
                    $_SESSION["username"] = $username;
                    header("Location: prova.php");
                } else {
                    echo "Password errata";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> 
</head>
<body>
    <div class="login-container">
        <h2>LOGIN</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <input type="text" name="username" placeholder="Username" required> <br>
            <input type="password" name="password" placeholder="Password" required> <br>
            <button type="submit">Login</button>
        </form>
        <a href="#" class="forgot-password">Forgot your password?</a>
        <div class="recaptcha-container">
            <input type="checkbox"> I'm not a robot
        </div>
    </div>

</div>
</body>
</html>






