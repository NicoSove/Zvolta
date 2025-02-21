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

// Recupera tutte le prenotazioni
$prenotazioni = [];
$queryPrenotati = "SELECT posto, username FROM prenotazione";
$resultPrenotati = $conn->query($queryPrenotati);

while ($row = $resultPrenotati->fetch_assoc()) {
    $prenotazioni[$row['posto']] = $row['username'];
}

$prenotazioneSuccess = false;


// Gestione della prenotazione
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['posto'])) {
    $posto = $_POST['posto'];

    // Verifica se il posto è già prenotato
    $stmt = $conn->prepare("SELECT * FROM prenotazione WHERE posto = ?");
    $stmt->bind_param("s", $posto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Il posto è già prenotato
        echo "<script>alert('Il posto $posto è già prenotato. Scegli un altro posto.');</script>";
    } else {
        // Inserisce la nuova prenotazione basata sull'ultimo click
        $stmt = $conn->prepare("INSERT INTO prenotazione (Data, username, posto, contModifiche) VALUES (CURDATE() + INTERVAL 1 DAY, ?, ?, 0)");
        $stmt->bind_param("ss", $username, $posto);
        
        if ($stmt->execute()) {
            $prenotazioneSuccess = true;
            echo "<script>
                    document.getElementById('successMessage').style.display = 'block';
                    alert('Prenotazione effettuata con successo!');
                  </script>";
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
    <title>Tabella di Prenotazione</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            cursor: pointer;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .empty-row {
            height: 20px;
        }
        #message {
            margin-top: 20px;
            display: none;
            text-align: center;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #successMessage {
            /*display: <?= $prenotazioneSuccess ? 'block' : 'none' ?>;*/
            margin-top: 20px;
            color: green;
            font-size: 16px;
            text-align: center;
        }
        td.prenotato-altri {
            background-color: #ff4d4d !important;
            color: white;
            font-weight: bold;
        }
        td.prenotato-mio {
            background-color: #4CAF50 !important;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

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
        <button type="submit">Invia</button>
    </form>
</div>

<div id="successMessage">
    <p>Prenotazione effettuata con successo!</p>
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
