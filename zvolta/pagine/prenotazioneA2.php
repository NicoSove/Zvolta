<?php
session_start();
include 'connessione.php'; // Assicurati che questo file contenga la connessione al database

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
$luogo = 'A2'; // Imposta il valore del luogo a A2
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
    $luogo = "A2"; // Imposta il valore del luogo a A2

    // Verifica se il posto è già prenotato per il luogo specificato e per la data +1
    $stmt = $conn->prepare("SELECT * FROM prenotazione WHERE posto = ? AND luogo = ? AND Data = ?");
    $stmt->bind_param("sss", $posto, $luogo, $nextDay);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Il posto è già prenotato per il luogo specificato e per la data +1
        echo "<script>alert('Il posto $posto nel luogo $luogo è già prenotato per il giorno successivo. Scegli un altro posto.');</script>";
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
    <style>
        
    </style>
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="logo">
                <img src="logo.png" alt="ZVOLTA Logo">
            </div>
            <nav>
                <a href="./pagine/login.php" class="login-button">LOGIN</a>
                <div class="user-icon">
                    <img src="placeholder.png" alt="Foto">
                </div>
            </nav>
        </div>
    </header>

    
    <table>
    <tr>
        <th colspan="4">GROUP (A2)</th>
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
        <td class="<?= getClassePosto('C1', $prenotazioni, $username) ?>" onclick="makeReservation('C1')">C1</td>
        <td class="<?= getClassePosto('C2', $prenotazioni, $username) ?>" onclick="makeReservation('C2')">C2</td>
        <td class="<?= getClassePosto('C3', $prenotazioni, $username) ?>" onclick="makeReservation('C3')">C3</td>
        <td class="<?= getClassePosto('C4', $prenotazioni, $username) ?>" onclick="makeReservation('C4')">C4</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('C5', $prenotazioni, $username) ?>" onclick="makeReservation('C5')">C5</td>
        <td class="<?= getClassePosto('C6', $prenotazioni, $username) ?>" onclick="makeReservation('C6')">C6</td>
        <td class="<?= getClassePosto('C7', $prenotazioni, $username) ?>" onclick="makeReservation('C7')">C7</td>
        <td class="<?= getClassePosto('C8', $prenotazioni, $username) ?>" onclick="makeReservation('C8')">C8</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('C9', $prenotazioni, $username) ?>" onclick="makeReservation('C9')">C9</td>
        <td class="<?= getClassePosto('C10', $prenotazioni, $username) ?>" onclick="makeReservation('C10')">C10</td>
        <td class="<?= getClassePosto('C11', $prenotazioni, $username) ?>" onclick="makeReservation('C11')">C11</td>
        <td class="<?= getClassePosto('C12', $prenotazioni, $username) ?>" onclick="makeReservation('C12')">C12</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('C13', $prenotazioni, $username) ?>" onclick="makeReservation('C13')">C13</td>
        <td class="<?= getClassePosto('C14', $prenotazioni, $username) ?>" onclick="makeReservation('C14')">C14</td>
        <td class="<?= getClassePosto('C15', $prenotazioni, $username) ?>" onclick="makeReservation('C15')">C15</td>
        <td class="<?= getClassePosto('C16', $prenotazioni, $username) ?>" onclick="makeReservation('C16')">C16</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('C17', $prenotazioni, $username) ?>" onclick="makeReservation('C17')">C17</td>
        <td class="<?= getClassePosto('C18', $prenotazioni, $username) ?>" onclick="makeReservation('C18')">C18</td>
        <td class="<?= getClassePosto('C19', $prenotazioni, $username) ?>" onclick="makeReservation('C19')">C19</td>
        <td class="<?= getClassePosto('C20', $prenotazioni, $username) ?>" onclick="makeReservation('C20')">C20</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('C21', $prenotazioni, $username) ?>" onclick="makeReservation('C21')">C21</td>
        <td class="<?= getClassePosto('C22', $prenotazioni, $username) ?>" onclick="makeReservation('C22')">C22</td>
        <td class="<?= getClassePosto('C23', $prenotazioni, $username) ?>" onclick="makeReservation('C23')">C23</td>
        <td class="<?= getClassePosto('C24', $prenotazioni, $username) ?>" onclick="makeReservation('C24')">C24</td>
    </tr>

    <tr class="empty-row">
        <td colspan="4"></td>
    </tr>

    <tr>
        <td class="<?= getClassePosto('D1', $prenotazioni, $username) ?>" onclick="makeReservation('D1')">D1</td>
        <td class="<?= getClassePosto('D2', $prenotazioni, $username) ?>" onclick="makeReservation('D2')">D2</td>
        <td class="<?= getClassePosto('D3', $prenotazioni, $username) ?>" onclick="makeReservation('D3')">D3</td>
        <td class="<?= getClassePosto('D4', $prenotazioni, $username) ?>" onclick="makeReservation('D4')">D4</td>
    </tr>
    <tr>
        <td class="<?= getClassePosto('D5', $prenotazioni, $username) ?>" onclick="makeReservation('D5')">D5</td>
        <td class="<?= getClassePosto('D6', $prenotazioni, $username) ?>" onclick="makeReservation('D6')">D6</td>
        <td class="<?= getClassePosto('D7', $prenotazioni, $username) ?>" onclick="makeReservation('D7')">D7</td>
        <td class="<?= getClassePosto('D8', $prenotazioni, $username) ?>" onclick="makeReservation('D8')">D8</td>
    </tr>
</table>


<div id="message">
    <p id="reservationText"></p>
    <form method="POST">
        <input type="hidden" id="selectedPosto" name="posto">
        <button type="submit">Invia</button>
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
    }
</script>



</body>
</html>
