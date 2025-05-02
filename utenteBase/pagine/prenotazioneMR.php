<?php
session_start();
include 'connessione.php'; // Assicurati che questo file contenga la connessione al database

// Controllo se l'utente è loggato
if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.php");
    exit();
}

$username = $_SESSION['username'];

// Recupero il ruolo dell'utente
$query = "SELECT ruolo_utente FROM utente WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php");
    exit();
}

$row = $result->fetch_assoc();

// Se il ruolo non è 'utente_base', reindirizza
if ($row['ruolo_utente'] !== 'utente_base') {
    header("Location: login.php");
    exit();
}

// Recupera tutte le prenotazioni per il luogo specificato e per la data +1
$prenotazioni = [];
$nextDay = date('Y-m-d', strtotime('+1 day')); // Calcola la data di domani
$queryPrenotati = "SELECT posto, username FROM prenotazione WHERE luogo = ? AND Data = ?";
$stmt = $conn->prepare($queryPrenotati);
$luogo = 'MR'; // Imposta il valore del luogo
$stmt->bind_param("ss", $luogo, $nextDay);
$stmt->execute();
$resultPrenotati = $stmt->get_result();

while ($row = $resultPrenotati->fetch_assoc()) {
    $prenotazioni[$row['posto']] = $row['username'];
}

$prenotazioneSuccess = false;

// Gestione della prenotazione
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['posto'])) {
    $posto = $_POST['posto'];
    $luogo = "MR"; // Imposta il valore del luogo

    // Verifica se il posto è già prenotato per il luogo specificato e per la data +1
    $stmt = $conn->prepare("SELECT * FROM prenotazione WHERE posto = ? AND luogo = ? AND Data = ?");
    $stmt->bind_param("sss", $posto, $luogo, $nextDay);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Il posto è già prenotato per il luogo specificato e per la data +1
        echo "<script>alert('Il posto $posto nel luogo $luogo è già prenotato per il giorno successivo. Scegli un altro posto.');</script>";
    } else {
        $rowCount = $resultCount->fetch_assoc();
        // Permetti fino a 3 prenotazioni per il 'coordinatore'
        if ($rowCount['count'] >= 3) {
            echo "<script>alert('Come coordinatore, puoi prenotare solo fino a 3 posti contemporaneamente.');</script>";
        } else {
        // Inserisce la nuova prenotazione basata sull'ultimo click
        $stmt = $conn->prepare("INSERT INTO prenotazione (Data, username, posto, contModifiche, luogo) VALUES (?, ?, ?, 0, ?)");
        $stmt->bind_param("ssss", $nextDay, $username, $posto, $luogo); // Aggiungi $luogo come parametro
        
        if ($stmt->execute()) {
            $prenotazioneSuccess = true;
            $_SESSION["prenOK"] = true;
            echo "<script>
                    document.getElementById('successMessage').style.display = 'block';
                    alert('Prenotazione effettuata con successo!');
                  </script>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Errore nella prenotazione: " . addslashes($conn->error) . "');</script>";
        }
    }
}
    $stmt->close();
}

// Funzione per determinare la classe del posto
function getClassePosto($posto, $prenotazioni, $username) {
    if (isset($prenotazioni[$posto])) {
        if ($prenotazioni[$posto] === $username) {
            return 'bg-blue-600'; // Posto prenotato dall'utente
        } else {
            return 'bg-red-400'; // Posto prenotato da altri
        }
    } else {
        return 'bg-teal-600'; // Posto libero
    }
}
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Tabella di Prenotazione</title>
</head>
<body class="bg-gray-200 flex flex-col items-center">
    <header class="w-full bg-white shadow-md py-4 mb-8">
            <div class="container mx-auto flex justify-between items-center px-4">
        <div class="logo">
            <a href="../home.php">
                <img src="../extra/logo.png" alt="ZVOLTA Logo" class="h-12 ml-0">
            </a>
        </div>
        <nav class="flex items-center">
            <a href="../login/logout.php" class="login-button text-blue-500 font-bold mr-4">LOGOUT</a>
            <div class="user-icon">
                <img src="../extra/placeholder.png" alt="Foto" class="h-10 w-10 rounded-full">
            </div>
        </nav>
    </div>
    </header>

    <div class="bg-gray-300 p-12 rounded-lg border-4 border-purple-300 w-3/4 lg:w-1/2">
        <div class="flex justify-around mb-12 space-x-8">
            <div class="text-center">
                <div class="<?= getClassePosto('MR1', $prenotazioni, $username) ?> w-32 h-40 rounded-lg mb-2" onclick="makeReservation('MR1')"></div>
                <p class="font-bold">MR 1</p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="<?= getClassePosto('MR2', $prenotazioni, $username) ?> w-32 h-40 rounded-lg mb-2" onclick="makeReservation('MR2')"></div>
                <p class="font-bold">MR 2</p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="<?= getClassePosto('MR3', $prenotazioni, $username) ?> w-32 h-40 rounded-lg mb-2" onclick="makeReservation('MR3')"></div>
                <p class="font-bold">MR 3</ p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="<?= getClassePosto('MR4', $prenotazioni, $username) ?> w-32 h-40 rounded-lg mb-2" onclick="makeReservation('MR4')"></div>
                <p class="font-bold">MR 4</p>
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-center">
                <div class="<?= getClassePosto('MR5', $prenotazioni, $username) ?> w-32 h-40 rounded-lg mb-2" onclick="makeReservation('MR5')"></div>
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
