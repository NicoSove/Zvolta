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

// Imposta la data di default a oggi
$dataPrenotazione = date('Y-m-d');

// Controlla se è stata inviata una data
if (isset($_POST['data'])) {
    $dataPrenotazione = $_POST['data'];
}

// Recupera tutte le prenotazioni per i luoghi specificati e per la data selezionata
$prenotazioni = [];

// Query per il luogo A1
$queryPrenotatiA1 = "SELECT posto, luogo, contModifiche FROM prenotazione WHERE luogo = ? AND Data = ? AND username = ?";
$stmt = $conn->prepare($queryPrenotatiA1);
$luogoA1 = 'A1'; // Imposta il valore del luogo a A1
$stmt->bind_param("sss", $luogoA1, $dataPrenotazione, $username);
$stmt->execute();
$resultPrenotatiA1 = $stmt->get_result();

while ($rowPrenotati = $resultPrenotatiA1->fetch_assoc()) {
    $prenotazioni[] = $rowPrenotati; // Aggiungi le prenotazioni di A1
}

// Query per il luogo A2
$queryPrenotatiA2 = "SELECT posto, luogo, contModifiche FROM prenotazione WHERE luogo = ? AND Data = ? AND username = ?";
$stmt = $conn->prepare($queryPrenotatiA2);
$luogoA2 = 'A2'; // Imposta il valore del luogo a A2
$stmt->bind_param("sss", $luogoA2, $dataPrenotazione, $username);
$stmt->execute();
$resultPrenotatiA2 = $stmt->get_result();

while ($rowPrenotati = $resultPrenotatiA2->fetch_assoc()) {
    $prenotazioni[] = $rowPrenotati; // Aggiungi le prenotazioni di A2
}

// Gestione della modifica della prenotazione
if (isset($_POST['modifica'])) {
    $posto = $_POST['posto'];
    $nuovaData = $_POST['nuovaData'];

    // Controlla se esiste già una prenotazione per il posto e il luogo nella nuova data
    $queryControllo = "SELECT * FROM prenotazione WHERE posto = ? AND luogo = ? AND Data = ?";
    $stmt = $conn->prepare($queryControllo);
    $stmt->bind_param("sss", $posto, $luogoA1, $nuovaData); // Controlla per A1
    $stmt->execute();
    $resultControllo = $stmt->get_result();

    // Controlla il numero di modifiche
    $queryControlloModifiche = "SELECT contModifiche FROM prenotazione WHERE posto = ? AND username = ?";
    $stmt = $conn->prepare($queryControlloModifiche);
    $stmt->bind_param("ss", $posto, $username);
    $stmt->execute();
    $resultControlloModifiche = $stmt->get_result();
    $rowControlloModifiche = $resultControlloModifiche->fetch_assoc();

    if ($resultControllo->num_rows > 0) {
        $messaggio = "Esiste già una prenotazione per questo posto e luogo nella nuova data.";
    } elseif ($rowControlloModifiche['contModifiche'] >= 2) {
        $messaggio = "Hai raggiunto il limite di modifiche per questa prenotazione.";
    } else {
        // Procedi con la modifica
        $queryModifica = "UPDATE prenotazione SET Data = ?, contModifiche = contModifiche + 1 WHERE posto = ? AND username = ?";
        $stmt = $conn->prepare($queryModifica);
        $stmt->bind_param("sss", $nuovaData, $posto, $username);
        $stmt->execute();
        $messaggio = "Prenotazione modificata con successo.";
    }
}

// Gestione dell'eliminazione della prenotazione
if (isset($_POST['elimina'])) {
    $posto = $_POST['posto'];

    // Esegui la query per eliminare la prenotazione
    $queryElimina = " DELETE FROM prenotazione WHERE posto = ? AND username = ?";
    $stmt = $conn->prepare($queryElimina);
    $stmt->bind_param("ss", $posto, $username);
    $stmt->execute();
    $messaggio = "Prenotazione eliminata con successo.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le tue prenotazioni</title>
</head>
<body>
    <h1>Prenotazioni per la data selezionata (<?php echo htmlspecialchars($dataPrenotazione); ?>)</h1>
    
    <form method="post">
        <label for="data">Seleziona una data:</label>
        <input type="date" id="data" name="data" value="<?php echo htmlspecialchars($dataPrenotazione); ?>" required>
        <button type="submit">Visualizza Prenotazioni</button>
    </form> <br>

    <?php if (isset($messaggio)): ?>
        <p><?php echo htmlspecialchars($messaggio); ?></p>
    <?php endif; ?>
    
    <?php if (count($prenotazioni) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Posto</th>
                    <th>Luogo</th>
                    <th>Modifica Data</th>
                    <th>Elimina</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prenotazioni as $prenotazione): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prenotazione['posto']); ?></td>
                        <td><?php echo htmlspecialchars($prenotazione['luogo']); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="posto" value="<?php echo htmlspecialchars($prenotazione['posto']); ?>">
                                <input type="date" name="nuovaData" required>
                                <button type="submit" name="modifica">Modifica</button>
                            </form>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="posto" value="<?php echo htmlspecialchars($prenotazione['posto']); ?>">
                                <button type="submit" name="elimina" onclick="return confirm('Sei sicuro di voler eliminare questa prenotazione?');">Elimina</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Non ci sono prenotazioni per la data selezionata.</p>
    <?php endif; ?>
    
</body>
</html>