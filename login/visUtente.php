<?php
session_start();
include '../pagine/connessione.php'; // Assicurati che questo file contenga la connessione al database

// Controllo se l'utente è loggato
$isLoggedIn = isset($_SESSION['username']);
if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.php");
    exit();
}

$username = $_SESSION['username'];

// Recupero i dati dell'utente
$query = "SELECT username, nome_utente, cognome_utente, mail_utente, ID_coordinatore, ruolo_utente, password_utente FROM utente WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php");
    exit();
}

// Recupero i dati dell'utente
$userData = $result->fetch_assoc();

// Gestione della modifica della password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verifica la vecchia password
    if (password_verify($oldPassword, $userData['password_utente'])) {
        // Controlla se le nuove password corrispondono
        if ($newPassword === $confirmPassword) {
            // Hash della nuova password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Aggiorna la password nel database
            $updateQuery = "UPDATE utente SET password_utente = ? WHERE username = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $hashedPassword, $username);
            if ($updateStmt->execute()) {
                echo "<p>Password modificata con successo!</p>";
            } else {
                echo "<p>Errore durante la modifica della password.</p>";
            }
        } else {
            echo "<p>Le nuove password non corrispondono.</p>";
        }
    } else {
        echo "<p>La vecchia password è errata.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Utente</title>
    <link rel="stylesheet" href="visUtente.css"> <!-- Aggiungi il tuo file CSS se necessario -->
    <style>
        #changePasswordForm {
            display: none; /* Nascondi il modulo di modifica password di default */
        }
    </style>
    <script>
        function togglePasswordForm() {
            var form = document.getElementById('changePasswordForm');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="logo">
                <a href="../home.php">
                <img src="../extra/logo.png" alt="ZVOLTA Logo">
                </a>
            </div>
            <nav>
                
                <?php if ($isLoggedIn): ?>
                    <a href="../login/logout.php" class="login-button">LOGOUT</a>
                <?php else: ?>
                    <a href="../login/login.php" class="login-button">LOGIN</a>
                <?php endif; ?>
                <div class="user-icon">
                    <img src="../extra/placeholder.png" alt="Foto">
                </div>
            </nav>
        </div>
    </header>

    <h1>Profilo Utente</h1> <!-- Spostato fuori dal riquadro -->
    
    <div class="user-data-container">
        <div class="user-data">
                <img src="../extra/placeholder.png" alt="Foto" style="
                        width: 300px;
                        height: 300px;
                        border-radius: 50%;
                        background: white;
                        "
                        >
            <div class="user-info">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($userData['nome_utente']); ?></p>
                <p><strong>Cognome:</strong> <?php echo htmlspecialchars($userData['cognome_utente']); ?></p>
                <div id="changePasswordForm">
            <form method="POST" action="">
                <label for="old_password">Vecchia Password:</label>
                <input type="password" name="old_password" required>
                <br>
                <label for="new_password">Nuova Password:</label>
                <input type="password" name="new_password" required>
                <br>
                <label for="confirm_password">Conferma Nuova Password:</label>
                <input type="password" name="confirm_password" required>
                <br>
                <button type="submit" name="change_password">Cambia Password</button>
            </form>
        </div>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['mail_utente']); ?></p>
                <?php if ($userData['ruolo_utente'] === 'utente_base'): ?>
                    <p><strong>ID Coordinatore:</strong> <?php echo htmlspecialchars($userData['ID_coordinatore']); ?></p>
                <?php endif; ?>
                <div class="matita">
                <p><strong>Password: **********</strong></p>
                    <img src="../images/pencil.png" onclick="togglePasswordForm()" style="width: 30px; height: auto; padding-left: 270px; margin-bottom:30px">
                </div>

                
            </div>
        </div>
        
      
    </div>

    <div class="home-button-container">
        <a href="../home.php" class="home-button">Home</a>
    </div>
</body>
</html>
