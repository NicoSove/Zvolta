<?php
session_start(); // Inizia una nuova sessione o riprende una sessione esistente

// Funzione per generare un CAPTCHA alfanumerico casuale
function generateCaptcha($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Caratteri utilizzabili nel CAPTCHA
    $captcha = ''; // Inizializza la stringa CAPTCHA
    for ($i = 0; $i < $length; $i++) {
        // Aggiunge un carattere casuale alla stringa CAPTCHA
        $captcha .= $characters[rand(0, strlen($characters) - 1)];
    }
    $_SESSION['captcha'] = $captcha; // Memorizza il CAPTCHA nella sessione
    return $captcha; // Restituisce il CAPTCHA generato
}

// Funzione per creare un'immagine CAPTCHA
function createCaptchaImage($captcha) {
    $width = 200; // Larghezza dell'immagine
    $height = 70; // Altezza dell'immagine
    $image = imagecreatetruecolor($width, $height); // Crea un'immagine vuota

    // Colori
    $backgroundColor = imagecolorallocate($image, 255, 255, 255); // Colore di sfondo (bianco)
    $textColor = imagecolorallocate($image, 0, 0, 0); // Colore del testo (nero)
    $lineColor = imagecolorallocate($image, 30, 30, 30); // Colore delle linee (grigio molto scuro)

    // Riempie lo sfondo dell'immagine
    imagefill($image, 0, 0, $backgroundColor);

    // Aggiunge delle linee all'immagine per rendere il CAPTCHA più difficile da decifrare
    for ($i = 0; $i < 8; $i++) { // Aumenta il numero di linee
        imagesetthickness($image, 2.3); // Imposta lo spessore della linea (più sottile)
        imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor); // Disegna la linea
    }

    // Aggiunge il testo CAPTCHA all'immagine
    $fontSize = 30; // Dimensione del font
    $x = rand(10, 50); // Posizione X casuale per il testo
    $y = rand(40, 60); // Posizione Y casuale per il testo
    imagestring($image, 5, $x, $y - 20, $captcha, $textColor); // Usa un font di sistema per scrivere il CAPTCHA

    // Output dell'immagine
    header('Content-Type: image/png'); // Imposta l'intestazione per l'immagine PNG
    imagepng($image); // Genera l'immagine PNG
    imagedestroy($image); // Libera la memoria associata all'immagine
}

// Gestione dell'invio del modulo
if ($_POST) {
    // Connessione al database
    $conn = new mysqli("localhost", "root", "", "zvolta");
    if ($conn->connect_error) {
        echo "Errore di connessione: " . $conn->connect_error; // Mostra errore di connessione
        die(); // Termina lo script
    } else {
        // Prendo i parametri dalla richiesta
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Validazione CAPTCHA
        if ($_POST['captcha'] !== $_SESSION['captcha']) {
            echo '<div class ="captcha">CAPTCHA errato Riprova.</div>'; // Messaggio di errore se il CAPTCHA non è corretto
        } else {
            // Preparo la query SQL per cercare l'utente nel database
            $stmt = $conn->prepare("SELECT password_utente FROM utente WHERE username = ?");
            $stmt->bind_param("s", $username); // Associa il parametro
            $stmt->execute(); // Esegue la query
            $result = $stmt->get_result(); // Ottiene il risultato della query

            if ($result->num_rows === 0) {
                echo '<div class="captcha">Username errato</div>'; // Messaggio se l'username non esiste
            } else {
                $row = $result->fetch_assoc(); // Recupera i dati dell'utente
                $pwddb = $row["password_utente"]; // Ottiene la password memorizzata

                // Verifica se la password inserita corrisponde a quella memorizzata
                if (password_verify($password, $pwddb)) {
                    $_SESSION["username"] = $username; // Memorizza l'username nella sessione

                    // Recupero il ruolo dell'utente per il redirect
                    $stmtRole = $conn->prepare("SELECT ruolo_utente FROM utente WHERE username = ?");
                    $stmtRole->bind_param("s", $username);
                    $stmtRole->execute();
                    $resultRole = $stmtRole->get_result();
                    $rowRole = $resultRole->fetch_assoc();
                    $ruoloUtente = $rowRole['ruolo_utente'];

                    // Redirect in base al ruolo
                    if ($ruoloUtente === 'utente_base') {
                        header("Location: ../utenteBase/home.php");
                    } elseif ($ruoloUtente === 'admin') {
                        header("Location: ../manager/home.php");
                    } elseif ($ruoloUtente === 'coordinatore') {
                        header("Location: ../coordinatore/home.php");
                    } else {
                        // Default redirect se ruolo non riconosciuto
                        header("Location: ../home.php");
                    }
                    exit(); // Termina lo script
                } else {
                    echo '<div class="captcha">Password errata</div>'; // Messaggio di errore se la password è errata
                }
            }
            // Chiudo la dichiarazione e la connessione
            $stmt->close();
            $conn->close();
        }
    }
}

// Se l'URL contiene '?captcha=1', genera l'immagine del CAPTCHA
if (isset($_GET['captcha']) && $_GET['captcha'] == 1) {
    $captchaString = generateCaptcha(); // Genera un nuovo CAPTCHA
    createCaptchaImage($captchaString); // Crea l'immagine del CAPTCHA
    exit(); // Esci dopo aver generato l'immagine
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> 
    <style>
        .captcha {
            color: white; /* Cambia il colore del testo */
            font-size: 24px; /* Cambia la dimensione del testo */
            font-weight: bold; /* Rende il testo in grassetto */
            text-align: center; /* Centra il testo */
            margin-top: 35%; /* Aggiunge un margine superiore */
            position: absolute;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>LOGIN</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <input type="text" name="username" placeholder="Username" required> <br>
            <input type="password" name="password" placeholder="Password" required> <br>
            <label for="captcha">CAPTCHA:</label><br>
            <img src="<?php echo $_SERVER['PHP_SELF'] . '?captcha=1'; ?>" alt="CAPTCHA Image" id="captchaImage"><br>    
            <input type="button" value="↻" onclick="refreshCaptcha()">
            <input type="text" name="captcha" placeholder="Inserisci il CAPTCHA" required> <br>
            <button type="submit" id="login">Login</button>

            
        </form>
        <a href="#" class="forgot-password" onclick="openMailApp(event)">Forgot your password?</a>
        <script>
            // Funzione per aprire l'app di posta predefinita con solo l'indirizzo email del manager
            function openMailApp(event) {
                event.preventDefault();
                // Recupera l'email del manager tramite fetch
                fetch('forgot_password.php')
                    .then(response => response.text())
                    .then(email => {
                        if(email.includes('Email inviata al manager:')) {
                            // Estrae l'email dal messaggio
                            const emailAddress = email.replace('Email inviata al manager: ', '').trim();
                            // Costruisce il link mailto senza subject e body
                            const mailtoLink = `mailto:${emailAddress}`;
                            // Apre il client di posta predefinito
                            window.location.href = mailtoLink;
                        } else {
                            alert(email);
                        }
                    })
                    .catch(error => alert('Errore nella richiesta: ' + error));
            }
        </script>
    </div>
    

    <script>
        function refreshCaptcha() {
            // Cambia la sorgente dell'immagine CAPTCHA per forzare il ricaricamento
            document.getElementById('captchaImage').src = '<?php echo $_SERVER['PHP_SELF']; ?>?captcha=1&rand=' + Math.random();
        }
    </script>
</body>
</html>