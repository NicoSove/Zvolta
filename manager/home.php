<?php
session_start();
include '../utenteBase/pagine/connessione.php'; // Assicurati che questo file contenga la connessione al database

// Controllo se l'utente è loggato
$isLoggedIn = isset($_SESSION['username']);

if ($row['ruolo_utente'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - ZVOLTA</title>
    <link rel="stylesheet" href="home.css"> <!-- Collegamento al file CSS -->
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="logo">
                <a href="home.php">
                    <img src="./extra/logo.png" alt="ZVOLTA Logo">
                </a>
            </div>
            <nav>
                <?php if ($isLoggedIn): ?>
                    <a href="../login/logout.php" class="login-button">LOGOUT</a>
                <?php else: ?>
                    <a href="../login/login.php" class="login-button">LOGIN</a>
                <?php endif; ?>
                <div class="user-icon">
                    <a href= "./pagine/visUtente.php">
                        <img src="../extra/placeholder.png" alt="Foto">
                    </a>
                </div>
            </nav>
        </div>
    </header>
    <div class="hero-background">
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>
                    <?php
                    if ($isLoggedIn && isset($_SESSION['username'])) {
                        echo "Benvenuto " . htmlspecialchars($_SESSION['username']);
                    } else {
                        echo "Benvenuto";
                    }
                    ?>
                </h1>
                <p>Cosa vuoi fare oggi?</p>
            </div>
        </section>
        
        <section class="buttons-container">
            <a href="./pagine/registrazione.php" class="button">CREAZIONE UTENTE ></a>
            <div class="divider"></div>
            <a href="./pagine/prenotazione.php" class="button">PRENOTAZIONI ></a><br><br>
            <a href="./pagine/controlloUtenti.php" class="button1">GESTIONE UTENTE ></a>
            <div class="divider"></div>
            <a href="./pagine/visualizzazione.php" class="button1">GESTIONE PRENOTAZIONI ></a>
        </section>
    </main>
    </div>
</body>
</html>