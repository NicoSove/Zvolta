<?php
session_start();
include '../pagine/connessione.php'; // Assicurati che questo file contenga la connessione al database

// Controllo se l'utente è loggato
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
    <h1>Profilo Utente</h1>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
    <p><strong>Nome:</strong> <?php echo htmlspecialchars($userData['nome_utente']); ?></p>
    <p><strong>Cognome:</strong> <?php echo htmlspecialchars($userData['cognome_utente']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['mail_utente']); ?></p>

    <?php if ($userData['ruolo_utente'] === 'utente_base'): ?>
        <p><strong>ID Coordinatore:</strong> <?php echo htmlspecialchars($userData['ID_coordinatore']); ?></p>
    <?php endif; ?>

    <h2>Modifica Password</h2>
    <button onclick="togglePasswordForm()">Cambia Password</button>
    
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
</body>
</html>