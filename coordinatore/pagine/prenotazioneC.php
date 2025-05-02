<?php 
session_start();
include 'connessione.php'; // Connessione al database

$isLoggedIn = isset($_SESSION['username']);

if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Recupero il ruolo dell'utente
$query = "SELECT ruolo_utente, ID_coordinatore FROM utente WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php");
    exit();
}

$row = $result->fetch_assoc();
$ruolo = $row['ruolo_utente'];
$idCoordinatore = $row['ID_coordinatore'] ?? null;

// Imposta la data di default a oggi
$dataPrenotazione = date('Y-m-d');
if (isset($_POST['data'])) {
    $dataPrenotazione = $_POST['data'];
}

// Recupera le prenotazioni visibili all'utente
$prenotazioni = [];
$luoghi = ['A1', 'A2', 'MR', 'PARCHEGGIO'];

// Se coordinatore, mostra anche prenotazioni degli utenti base
if ($ruolo === 'coordinatore') {
    $queryUtentiBase = "SELECT username FROM utente WHERE ID_coordinatore = (SELECT ID_utente FROM utente WHERE username = ?)";
    $stmt = $conn->prepare($queryUtentiBase);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultUtentiBase = $stmt->get_result();

    $utenti = [$username];
    while ($rowUtente = $resultUtentiBase->fetch_assoc()) {
        $utenti[] = $rowUtente['username'];
    }
} else {
    $utenti = [$username];
}

foreach ($luoghi as $luogo) {
    foreach ($utenti as $utenteConsiderato) {
        $queryPrenotazioni = "SELECT posto, luogo, contModifiche, Data, username FROM prenotazione WHERE luogo = ? AND Data = ? AND username = ?";
        $stmt = $conn->prepare($queryPrenotazioni);
        $stmt->bind_param("sss", $luogo, $dataPrenotazione, $utenteConsiderato);
        $stmt->execute();
        $resultPrenotazioni = $stmt->get_result();

        while ($rowPrenotazione = $resultPrenotazioni->fetch_assoc()) {
            $prenotazioni[] = $rowPrenotazione;
        }
    }
}

// Gestione modifica
if (isset($_POST['modifica'])) {
    $posto = $_POST['posto'];
    $nuovaData = $_POST['nuovaData'];

    if ($nuovaData <= date('Y-m-d')) {
        $messaggio = "Non puoi modificare per oggi o per date passate.";
    } else {
        $queryControllo = "SELECT * FROM prenotazione WHERE posto = ? AND luogo = ? AND Data = ?";
        $stmt = $conn->prepare($queryControllo);
        $stmt->bind_param("sss", $posto, $_POST['luogo'], $nuovaData);
        $stmt->execute();
        $resultControllo = $stmt->get_result();

        $queryControlloModifiche = "SELECT contModifiche FROM prenotazione WHERE posto = ? AND username = ?";
        $stmt = $conn->prepare($queryControlloModifiche);
        $stmt->bind_param("ss", $posto, $username);
        $stmt->execute();
        $resultControlloModifiche = $stmt->get_result();
        $rowControlloModifiche = $resultControlloModifiche->fetch_assoc();

        if ($resultControllo->num_rows > 0) {
            $messaggio = "Prenotazione già esistente per nuova data.";
        } elseif ($rowControlloModifiche['contModifiche'] >= 2) {
            $messaggio = "Limite modifiche raggiunto.";
        } else {
            $queryModifica = "UPDATE prenotazione SET Data = ?, contModifiche = contModifiche + 1 WHERE posto = ? AND username = ?";
            $stmt = $conn->prepare($queryModifica);
            $stmt->bind_param("sss", $nuovaData, $posto, $username);
            $stmt->execute();
            $messaggio = "Modificata con successo.";
            header("Location: visualizzazione.php?data=" . urlencode($nuovaData));
            exit();
        }
    }
}

// Gestione eliminazione
if (isset($_POST['elimina'])) {
    $posto = $_POST['posto'];
    $queryElimina = "DELETE FROM prenotazione WHERE posto = ? AND username = ?";
    $stmt = $conn->prepare($queryElimina);
    $stmt->bind_param("ss", $posto, $username);
    $stmt->execute();
    $messaggio = "Prenotazione eliminata.";
}

$oggi = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le tue prenotazioni</title>
    <link rel="stylesheet" href="vis.css">
</head>
<body>
<header>
    <div class="top-bar">
        <div class="logo">
            <a href="../home.php"><img src="../extra/logo.png" alt="ZVOLTA Logo"></a>
        </div>
        <nav>
            <?php if ($isLoggedIn): ?>
                <a href="../login/logout.php" class="login-button">LOGOUT</a>
            <?php else: ?>
                <a href="../login/login.php" class="login-button">LOGIN</a>
            <?php endif; ?>
            <div class="user-icon"><img src="../extra/placeholder.png" alt="Foto"></div>
        </nav>
    </div>
</header>

<div class="headers">
    <h1>Prenotazioni per il <?php echo htmlspecialchars($dataPrenotazione); ?></h1>
    <form method="post">
        <label for="data">Seleziona una data:</label>
        <input type="date" id="data" name="data" value="<?php echo htmlspecialchars($dataPrenotazione); ?>" required>
        <button type="submit">Visualizza</button>
    </form>
</div>

<?php if (isset($messaggio)): ?>
    <p><?php echo htmlspecialchars($messaggio); ?></p>
<?php endif; ?>

<?php if (count($prenotazioni) > 0): ?>
    <table border="1">
        <thead>
            <tr>
                <th>Utente</th>
                <th>Posto</th>
                <th>Luogo</th>
                <th>Data</th>
                <?php if ($ruolo === 'coordinatore' || array_filter($prenotazioni, fn($p) => $p['username'] === $username && $p['Data'] > $oggi)): ?>
                    <th>Modifica</th>
                    <th>Elimina</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prenotazioni as $prenotazione): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prenotazione['username']); ?></td>
                    <td><?php echo htmlspecialchars($prenotazione['posto']); ?></td>
                    <td><?php echo htmlspecialchars($prenotazione['luogo']); ?></td>
                    <td><?php echo htmlspecialchars($prenotazione['Data']); ?></td>
                    <?php if ($prenotazione['username'] === $username && $prenotazione['Data'] > $oggi): ?>
                        <td>
                            <form method="post">
                                <input type="hidden" name="posto" value="<?php echo htmlspecialchars($prenotazione['posto']); ?>">
                                <input type="hidden" name="luogo" value="<?php echo htmlspecialchars($prenotazione['luogo']); ?>">
                                <input type="date" name="nuovaData" required>
                                <button type="submit" name="modifica">Modifica</button>
                            </form>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="posto" value="<?php echo htmlspecialchars($prenotazione['posto']); ?>">
                                <button type="submit" name="elimina" onclick="return confirm('Confermi eliminazione?');">Elimina</button>
                            </form>
                        </td>
                    <?php elseif ($ruolo === 'coordinatore'): ?>
                        <td colspan="2">Solo visualizzazione</td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="headers"><p>Nessuna prenotazione trovata per questa data.</p></div>
<?php endif; ?>
</body>
</html>