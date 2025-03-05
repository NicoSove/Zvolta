<?php
session_start();
include 'connessione.php'; // Assicurati che questo file contenga la connessione al database

// Controllo se l'utente è loggato
$isLoggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="prenotazione.css"> 
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
                    <img src="placeholder.png" alt="Foto">
                </div>
            </nav>
        </div>
    </header>
<h1> Prenotazioni</h1>
<div class="cards">
    <div class="card red" onclick="window.location='prenotazioneA1.php';">
        <p class="tip">Prenotazione A1</p>
        <p class="second-text"></p>
        <img src= "../images/desk2.png">
    </div>
    <div class="card blue" onclick="window.location='prenotazioneA2.php';">
        <p class="tip">Prenotazione A2</p>
        <p class="second-text"></p>
        <img src= "../images/desk1.png">
    </div>
    <div class="card green">
        <p class="tip">Hover Me</p>
        <p class="second-text"></p>
    </div>
</div>





</body>
</html>
