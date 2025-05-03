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
    header("Location: ../login/login.php");
    exit();
}

$row = $result->fetch_assoc();

// Se il ruolo non è 'coordinatore', reindirizza
if ($row['ruolo_utente'] !== 'coordinatore') {
    header("Location: ../login/login.php");
    exit();
}

// Recupera tutte le prenotazioni per il luogo specificato e per la data +1
$prenotazioni = [];
$nextDay = date('Y-m-d', strtotime('+1 day')); // Calcola la data di domani
$queryPrenotati = "SELECT posto, username FROM prenotazione WHERE luogo = ? AND Data = ?";
$stmt = $conn->prepare($queryPrenotati);
$luogo = 'PARCHEGGIO'; // Imposta il valore del luogo
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
    $luogo = "PARCHEGGIO"; // Imposta il valore del luogo

    // Verifica se il posto è già prenotato per il luogo specificato e per la data +1
    $stmt = $conn->prepare("SELECT * FROM prenotazione WHERE posto = ? AND luogo = ? AND Data = ?");
    $stmt->bind_param("sss", $posto, $luogo, $nextDay);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Il posto è già prenotato per il luogo specificato e per la data +1
        echo "<script>alert('Il posto $posto nel luogo $luogo è già prenotato per il giorno successivo. Scegli un altro posto.');</script>";
    } else {
        // Controlla quante prenotazioni ha già l'utente per la stessa data
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM prenotazione WHERE username = ? AND Data = ?");
        $stmt->bind_param("ss", $username, $nextDay);
        $stmt->execute();
        $resultCount = $stmt->get_result();
        $rowCount = $resultCount->fetch_assoc();

        // Permetti fino a 3 prenotazioni per il 'coordinatore'
        if ($rowCount['count'] >= 3) {
            echo "<script>alert('Come coordinatore, puoi prenotare solo fino a 3 posti contemporaneamente.');</script>";
        } else {
            // Inserisce la nuova prenotazione
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
    <title>Tabella di Prenotazione - Parcheggio</title>
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

    <div class="bg-gray-300 p-8 rounded-lg border-4 border-purple-300 w-3/4 lg:w-1/2">
        <table class="w-full border-collapse text-center">
            <tr>
                <td colspan="16" class="bg-gray-500 text-white font-semibold text-xl py-3 rounded-t">GROUP (C):</td>
            </tr>
            <tr>
                <td class="<?= getClassePosto('A1', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer rounded-l-lg" onclick="makeReservation('A1')">A1</td>
                <td class="<?= getClassePosto('A5', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('A5')">A5</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('B1', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('B1')">B1</td>
                <td class="<?= getClassePosto('B5', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('B5')">B5</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('C1', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('C1')">C1</td>
                <td class="<?= getClassePosto('C5', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('C5')">C5</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('D1', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('D1')">D1</td>
                <td class="<?= getClassePosto('D5', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('D5')">D5</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('E1', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('E1')">E1</td>
                <td class="<?= getClassePosto('E5', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer" onclick="makeReservation('E5')">E5</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('F1', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer rounded-r-lg" onclick="makeReservation('F1')">F1</td>
                <td class="<?= getClassePosto('F5', $prenotazioni, $username) ?> p-4 border border-gray-400 cursor-pointer rounded-r-lg" onclick="makeReservation('F5')">F5</td>
            </tr>
            <tr>
                <td class="<?= getClassePosto('A2', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('A2')">A2</td>
                <td class="<?= getClassePosto('A6', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('A6')">A6</td>
                <td><?= '' ?></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('B2', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('B2')">B2</td>
                <td class="<?= getClassePosto('B6', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('B6')">B6</td>
                <td><?= '' ?></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('C2', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('C2')">C2</td>
                <td class="<?= getClassePosto('C5', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('C5')">C5</td>
                <td><?= '' ?></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('D2', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('D2')">D2</td>
                <td class="<?= getClassePosto('D6', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('D6')">D6</td>
                <td><?= '' ?></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('E2', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('E2')">E2</td>
                <td class="<?= getClassePosto('E6', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('E6')">E6</td>
                <td><?= '' ?></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('F2', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('F2')">F2</td>
                <td class="<?= getClassePosto('F6', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('F6')">F6</td>
            </tr>
            <tr>
                <td class="<?= getClassePosto('A3', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('A3')">A3</td>
                <td class="<?= getClassePosto('A7', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('A7')">A7</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('B3', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('B3')">B3</td>
                <td class="<?= getClassePosto('B7', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('B7')">B7</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('C3', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('C3')">C3</td>
                <td class="<?= getClassePosto('C7', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('C7')">C7</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('D3', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('D3')">D3</td>
                <td class="<?= getClassePosto('D7', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('D7')">D7</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('E3', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('E3')">E3</td>
                <td class="<?= getClassePosto('E7', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('E7')">E7</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('F3', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('F3')">F3</td>
                <td class="<?= getClassePosto('F7', $prenotazioni, $username) ?> p-3 border border-gray-400 cursor-pointer" onclick="makeReservation('F7')">F7</td>
            </tr>
            <tr>
                <td class="<?= getClassePosto('A4', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer rounded-l-lg" onclick="makeReservation('A4')">A4</td>
                <td class="<?= getClassePosto('A8', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('A8')">A8</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('B4', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('B4')">B4</td>
                <td class="<?= getClassePosto('B8', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('B8')">B8</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('C4', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('C4')">C4</td>
                <td class="<?= getClassePosto('C8', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('C8')">C8</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('D4', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('D4')">D4</td>
                <td class="<?= getClassePosto('D8', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('D8')">D8</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('E4', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('E4')">E4</td>
                <td class="<?= getClassePosto('E8', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('E8')">E8</td>
                <td class="w-6"></td> <!-- piccolo spazio -->
                <td class="<?= getClassePosto('F4', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer" onclick="makeReservation('F4')">F4</td>
                <td class="<?= getClassePosto('F8', $prenotazioni, $username) ?> p-6 w-16 h-16 border border-gray-400 cursor-pointer rounded-r-lg" onclick="makeReservation('F8')">F8</td>
            </tr>
        </table>
        <div class="flex justify-around mt-6">
            <div class="flex items-center bg-gray-500 px-3 py-2 rounded">
                <div class="bg-red-400 w-6 h-6 rounded mr-3"></div>
                <p class="font-bold text-white">NON DISPONIBILE</p>
            </div>
            <div class="flex items-center bg-gray-500 px-3 py-2 rounded">
                <div class="bg-teal-600 w-6 h-6 rounded mr-3"></div>
                <p class="font-bold text-white">LIBERO</p>
            </div>
            <div class="flex items-center bg-gray-500 px-3 py-2 rounded">
                <div class="bg-blue-600 w-6 h-6 rounded mr-3"></div>
                <p class="font-bold text-white">PRENOTATO</p>
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
    if (isset($_SESSION['prenOK']) && $_SESSION['prenOK'] == 1) {
        echo "<div id='successMessage' class='mt-8'>
        <p class='text-green-500 font-bold'>Prenotazione effettuata con successo!</p>
        </div>";
        $_SESSION['prenOK'] = 0;
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
