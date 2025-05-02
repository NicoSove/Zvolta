<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="registrazione.css"> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <script>
        // Funzione per mostrare o nascondere il campo coordinatore
        function mostraCoordinatore() {
            var ruolo = document.getElementById("ruolo").value;
            var divCoordinatore = document.getElementById("divCoordinatore");

            if (ruolo === "utente_base") {
                divCoordinatore.style.display = "block";
            } else {
                divCoordinatore.style.display = "none";
            }
        }

        
        function validaPassword() {
            var password = document.getElementsByName("password")[0].value;
            
            var lunghezzaMinima = 8; 
            var conteggioMinuscole = 0; 
            var conteggioMaiuscole = 0; 
            var conteggioNumeri = 0; 

            
            for (var i = 0; i < password.length; i++) {
                var carattere = password.charAt(i);

                if (carattere >= 'a' && carattere <= 'z') {
                    conteggioMinuscole++;
                } else if (carattere >= 'A' && carattere <= 'Z') {
                    conteggioMaiuscole++;
                } else if (carattere >= '0' && carattere <= '9') {
                    conteggioNumeri++;
                }
            }
            if (password.length < lunghezzaMinima) {
                alert("La password deve essere lunga almeno " + lunghezzaMinima + " caratteri.");
                return false; 
            }
            if (conteggioMinuscole == 0) {
                alert("La password deve contenere almeno una lettera minuscola.");
                return false; 
            }
            if (conteggioMaiuscole == 0) {
                alert("La password deve contenere almeno una lettera maiuscola.");
                return false; 
            }
            if (conteggioNumeri == 0) {
                alert("La password deve contenere almeno un numero.");
                return false; 
            }

            return true; 
        }
    </script>
</head>
<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return validaPassword()">
        <div class="login-container">
        <h2>REGISTRAZIONE</h2>
            <input type="text" name="username" placeholder="Username" required> <br>
            <input type="text" name="nome" placeholder="Nome"required> <br>
            <input type="text" name="cognome" placeholder="Cognome"required> <br>
            <input type="email" name="email" placeholder="Email"required> <br>
            <input type="password" name="password" placeholder="Password"required> <br>
            <input type="number" name="telefono" placeholder="Telefono"required> <br>

            Ruolo 
            <select name="ruolo" id="ruolo" onchange="mostraCoordinatore()" required>
                <option value="admin">Admin</option>
                <option value="coordinatore">Coordinatore</option>
                <option value="utente_base">Utente base</option>
            </select> <br>

            <div id="divCoordinatore" style="display: none;">
                Coordinatore di riferimento: <input type="text" name="coordinatore"> <br>
            </div>

            <button type="submit">Registra</button>
        </div>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Connessione al database
        $host = "localhost";
        $username = "root";
        $password = "";
        $db_name = "zvolta";
        $conn = new mysqli($host, $username, $password, $db_name);

        if ($conn->connect_error) {
            die("Errore connessione al db");
        }

        // Acquisizione dati dal form
        $username = $_POST["username"];
        $nome = $_POST["nome"];
        $cognome = $_POST["cognome"];
        $email = $_POST["email"];
        $telefono = $_POST["telefono"];
        $ruolo = $_POST["ruolo"];
        $coordinatore = isset($_POST["coordinatore"]) ? $_POST["coordinatore"] : NULL;
        $pwd = password_hash($_POST["password"], PASSWORD_DEFAULT);

        // Se il ruolo è "utente_base", verificare che il coordinatore esista
        if ($ruolo === "utente_base") {
            $check_coordinatore_sql = "SELECT * FROM utente WHERE username = '$coordinatore' AND ruolo_utente = 'coordinatore'";
            $coordinatore_result = $conn->query($check_coordinatore_sql);

            if ($coordinatore_result->num_rows === 0) {
                echo "<p style='color: red;'>Errore: Il coordinatore specificato non esiste!</p>";
                $conn->close();
                exit(); // Interrompiamo il codice qui per non procedere con l'inserimento
            }
        }

        // Controllo se l'username esiste già
        $check_sql = "SELECT * FROM utente WHERE username = '$username'";
        $result = $conn->query($check_sql);

        if ($result->num_rows > 0) {
            echo "<p style='color: red;'>Errore: Username già esistente!</p>";
        } else {
            // Se il coordinatore esiste (o non è richiesto), procediamo con l'inserimento
            $sql = "INSERT INTO utente (username, nome_utente, cognome_utente, mail_utente, password_utente, telefono_utente, ruolo_utente, ID_coordinatore) 
                    VALUES ('$username', '$nome', '$cognome', '$email', '$pwd', '$telefono', '$ruolo', " . ($coordinatore ? "'$coordinatore'" : "NULL") . ")";

            if ($conn->query($sql)) {
                echo "<p style='color: green;'>Utente registrato correttamente</p>";
            } else {
                echo "<p style='color: red;'>Errore registrazione: " . $conn->error . "</p>";
            }
        }

        $conn->close();
    }
    ?>
</body>
</html>
