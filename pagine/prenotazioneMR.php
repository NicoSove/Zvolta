<?php
session_start();
include './connessione.php'; // Assicurati che questo file contenga la connessione al database

// Controllo se l'utente è loggato
$isLoggedIn = isset($_SESSION['username']);
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Tabella di Prenotazione</title>
</head>
<body class="bg-gray-200 flex flex-col items-center justify-center min-h-screen">
    <header class="w-full bg-white shadow-md py-4 mb-8">
        <div class="container mx-auto flex justify-between items-center px-4">
            <div class="logo">
                <a href="../home.php">
                    <img src="../extra/logo.png" alt="ZVOLTA Logo" class="h-12">
                </a>
            </div>
            <nav class="flex items-center">
                <?php if ($isLoggedIn): ?>
                    <a href="../login/logout.php" class="login-button text-blue-500 font-bold mr-4">LOGOUT</a>
                <?php else: ?>
                    <a href="../login/login.php" class="login-button text-blue-500 font-bold mr-4">LOGIN</a>
                <?php endif; ?>
                <div class="user-icon">
                    <img src="../extra/placeholder.png" alt="Foto" class="h-10 w-10 rounded-full">
                </div>
            </nav>
        </div>
    </header>

    <div class="bg-gray-300 p-12 rounded-lg border-4 border-purple-300 w-3/4 lg:w-1/2">
        <div class="flex justify-around mb-12 space-x-8">
            <div class="text-center">
                <div class="bg-teal-600 w-32 h-40 rounded-lg mb-2" onclick="makeReservation('C1')"></div>
                <p class="font-bold">MR 1</p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="bg-red-400 w-32 h-40 rounded-lg mb-2" onclick="makeReservation('C2')"></div>
                <p class="font-bold">MR 2</p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="bg-teal-600 w-32 h-40 rounded-lg mb-2" onclick="makeReservation('C3')"></div>
                <p class="font-bold">MR 3</p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="bg-blue-600 w-32 h-40 rounded-lg mb-2" onclick="makeReservation('C4')"></div>
                <p class="font-bold">MR 4</p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="bg-blue-600 w-32 h-40 rounded-lg mb-2" onclick="makeReservation('C5')"></div>
                <p class="font-bold">MR 5</p>
                <i class="fas fa-info-circle"></i>
            </div>
        </div>
        <div class="flex justify-around">
            <div class="flex items-center">
                <div class="bg-red-400 w-8 h-4 rounded-full mr-2"></div>
                <p class="font-bold">NOT AVAILABLE</p>
            </div>
            <div class="flex items-center">
                <div class="bg-teal-600 w-8 h-4 rounded-full mr-2"></div>
                <p class="font-bold">FREE</p>
            </div>
            <div class="flex items-center">
                <div class="bg-blue-600 w-8 h-4 rounded-full mr-2"></div>
                <p class="font-bold">BOOKED</p>
            </div>
        </div>
    </div>

    <div id="message" class="hidden mt-8">
        <p id="reservationText" class="text-xl font-bold mb-4"></p>
        <form method="POST">
            <input type="hidden" id="selectedPosto" name="posto">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Invia</button>
        </form>
    </div>

    <?php
    if(isset($_SESSION['prenOK']) && $_SESSION['prenOK']==1) {
        echo "<div id='successMessage' class='mt-8'>
        <p class='text-green-500 font-bold'>Prenotazione effettuata con successo!</p>
        </div>";
        $_SESSION['prenOK']=0;
    }
    ?>

    <script>
        function makeReservation(cell) {
            document.getElementById('reservationText').innerText = 'La tua prenotazione è ' + cell;
            document.getElementById('selectedPosto').value = cell;
            document.getElementById('message').style.display = 'block';
        }
    </script>
</body>
</html>