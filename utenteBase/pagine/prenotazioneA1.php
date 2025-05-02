<?php
session_start();
include 'connessione.php'; // Assicurati che questo file contenga la connessione al database
$isLoggedIn = isset($_SESSION['username']);
// Controllo se l'utente è loggato
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
$luogo = 'A1'; // Imposta il valore del luogo
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
    $luogo = "A1"; // Imposta il valore del luogo

    // Verifica se il posto è già prenotato per il luogo specificato e per la data +1
    $stmt = $conn->prepare("SELECT * FROM prenotazione WHERE posto = ? AND luogo = ? AND Data = ?");
    $stmt->bind_param("sss", $posto, $luogo, $nextDay);
    $stmt->execute();
    $result = $stmt->get_result();

    //$resultCount = $stmt->get_result();
    $rowCount = $result->fetch_assoc();


    if ($result->num_rows > 0) {
        // Il posto è già prenotato per il luogo specificato e per la data +1
        echo "<script>alert('Il posto $posto nel luogo $luogo è già prenotato per il giorno successivo. Scegli un altro posto.');</script>";
    } else {
        // Permetti fino a 3 prenotazioni per il 'coordinatore'
        if ($rowCount['count'] >= 1) {
            echo "<script>alert('Come utente base, puoi prenotare solo fino a un posto.');</script>";
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

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="prenotazione.css"> 
    <title>Tabella di Prenotazione</title>
    
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
    <table>
    <tr>
        <th colspan="4">GROUP (A1)</th>
    </tr>
    <?php
    function getClassePosto($posto, $prenotazioni, $username) {
        if (isset($prenotazioni[$posto])) {
            return ($prenotazioni[$posto] === $username) ? "prenotato-mio" : "prenotato-altri";
        }
        return "";
    }
    ?>

    <tr>
        <td class="<?= getClassePosto('A1', $prenotazioni, $username) ?>" onclick="makeReservation('A1')">A1</td>
        <td class="<?= getClassePosto('A2', $prenotazioni, $username) ?>" onclick="makeReservation('A2')">A2</td>
        <td class="<?= getClassePosto('A3', $prenotazioni, $username) ?>" onclick="makeReservation('A3')">A3</td>
        <td class="<?= getClassePosto('A4', $prenotazioni, $username) ?>" onclick="makeReservation('A4')">A4</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('A5', $prenotazioni, $username) ?>" onclick="makeReservation('A5')">A5</td>
        <td class="<?= getClassePosto('A6', $prenotazioni, $username) ?>" onclick="makeReservation('A6')">A6</td>
        <td class="<?= getClassePosto('A7', $prenotazioni, $username) ?>" onclick="makeReservation('A7')">A7</td>
        <td class="<?= getClassePosto('A8', $prenotazioni, $username) ?>" onclick="makeReservation('A8')">A8</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('A9', $prenotazioni, $username) ?>" onclick="makeReservation('A9')">A9</td>
        <td class="<?= getClassePosto('A10', $prenotazioni, $username) ?>" onclick="makeReservation('A10')">A10</td>
        <td class="<?= getClassePosto('A11', $prenotazioni, $username) ?>" onclick="makeReservation('A11')">A11</td>
        <td class="<?= getClassePosto('A12', $prenotazioni, $username) ?>" onclick="makeReservation('A12')">A12</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('A13', $prenotazioni, $username) ?>" onclick="makeReservation('A13')">A13</td>
        <td class="<?= getClassePosto('A14', $prenotazioni, $username) ?>" onclick="makeReservation('A14')">A14</td>
        <td class="<?= getClassePosto('A15', $prenotazioni, $username) ?>" onclick="makeReservation('A15')">A15</td>
        <td class="<?= getClassePosto('A16', $prenotazioni, $username) ?>" onclick="makeReservation('A16')">A16</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('A17', $prenotazioni, $username) ?>" onclick="makeReservation('A17')">A17</td>
        <td class="<?= getClassePosto('A18', $prenotazioni, $username) ?>" onclick="makeReservation('A18')">A18</td>
        <td class="<?= getClassePosto('A19', $prenotazioni, $username) ?>" onclick="makeReservation('A19')">A19</td>
        <td class="<?= getClassePosto('A20', $prenotazioni, $username) ?>" onclick="makeReservation('A20')">A20</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('A21', $prenotazioni, $username) ?>" onclick="makeReservation('A21')">A21</td>
        <td class="<?= getClassePosto('A22', $prenotazioni, $username) ?>" onclick="makeReservation('A22')">A22</td>
        <td class="<?= getClassePosto('A23', $prenotazioni, $username) ?>" onclick="makeReservation('A23')">A23</td>
        <td class="<?= getClassePosto('A24', $prenotazioni, $username) ?>" onclick="makeReservation('A24')">A24</td>
    </tr>

    <tr class="empty-row">
        <td colspan="4"></td>
    </tr>

    <tr>
        <td class="<?= getClassePosto('B1', $prenotazioni, $username) ?>" onclick="makeReservation('B1')">B1</td>
        <td class="<?= getClassePosto('B2', $prenotazioni, $username) ?>" onclick="makeReservation('B2')">B2</td>
        <td class="<?= getClassePosto('B3', $prenotazioni, $username) ?>" onclick="makeReservation('B3')">B3</td>
        <td class="<?= getClassePosto('B4', $prenotazioni, $username) ?>" onclick="makeReservation('B4')">B4</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('B5', $prenotazioni, $username) ?>" onclick="makeReservation('B5')">B5</td>
        <td class="<?= getClassePosto('B6', $prenotazioni, $username) ?>" onclick="makeReservation('B6')">B6</td>
        <td class="<?= getClassePosto('B7', $prenotazioni, $username) ?>" onclick="makeReservation('B7')">B7</td>
        <td class="<?= getClassePosto('B8', $prenotazioni, $username) ?>" onclick="makeReservation('B8')">B8</td>
    </tr>
</table>

<div id="message">
    <p id="reservationText"></p>
    <form method="POST">
        <input type="hidden" id="selectedPosto" name="posto">
        <button type="submit" id="submitButton" style="display: none; ">Invia</button>
    </form>
</div>


<?php
if(isset($_SESSION['prenOK']) && $_SESSION['prenOK']==1) {
    //echo $_SESSION['prenOK'];
    echo "<div id='successMessage'>
    <p>Prenotazione effettuata con successo!</p>
    </div>";
    $_SESSION['prenOK']=0;

}
?>
<script>
    function makeReservation(cell) {
    document.getElementById('reservationText').innerText = 'La tua prenotazione è ' + cell;
    document.getElementById('selectedPosto').value = cell;
    document.getElementById('message').style.display = 'block';
    document.getElementById('submitButton').style.display = 'block'; // Mostra il bottone di invio
}
</script>



</body>
</html>
