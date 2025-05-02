<?php
session_start();
include 'connessione.php';
$isLoggedIn = isset($_SESSION['username']);

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$query = "SELECT ID_coordinatore, ruolo_utente FROM utente WHERE username = ?";
$stmt = $conn->prepare($query);
if (!$stmt) die("Errore prepare: " . $conn->error);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: login.php");
    exit();
}

$row = $result->fetch_assoc();
$ruolo = $row['ruolo_utente'];
$ID_coordinatore = $row['ID_coordinatore'];

// Se l'utente non è admin o coordinatore, redirigi al login
if ($ruolo !== 'admin' && $ruolo !== 'coordinatore') {
    header("Location: login.php");
    exit();
}

$dataPrenotazione = isset($_POST['data']) ? $_POST['data'] : date('Y-m-d');

// Per l'admin, mostra tutte le prenotazioni, incluse quelle del coordinatore
if ($ruolo === 'admin') {
    $queryPrenotazioni = "SELECT posto, luogo, contModifiche, Data, username FROM prenotazione WHERE Data = ?";
    $stmt = $conn->prepare($queryPrenotazioni);
    $stmt->bind_param("s", $dataPrenotazione);
    $stmt->execute();
    $result = $stmt->get_result();
    $prenotazioni = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Se è un coordinatore, mostra solo le sue prenotazioni e quelle degli utenti assegnati
    $queryUtentiBase = "SELECT username FROM utente WHERE ID_coordinatore = ?";
    $stmt = $conn->prepare($queryUtentiBase);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultUtentiBase = $stmt->get_result();

    $usernames = [$username];
    while ($rowUtente = $resultUtentiBase->fetch_assoc()) {
        $usernames[] = $rowUtente['username'];
    }

    $prenotazioni = [];
    $luoghi = ['A1', 'A2', 'MR', 'PARCHEGGIO'];
    foreach ($luoghi as $luogo) {
        $placeholders = implode(',', array_fill(0, count($usernames), '?'));
        $query = "SELECT posto, luogo, contModifiche, Data, username FROM prenotazione 
                  WHERE luogo = ? AND Data = ? AND username IN ($placeholders)";
        $stmt = $conn->prepare($query);
        $types = str_repeat('s', count($usernames) + 2);
        $params = array_merge([$luogo, $dataPrenotazione], $usernames);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $prenotazioni[] = $row;
        }
    }
}

if (isset($_POST['modifica'])) {
    $posto = $_POST['posto'];
    $nuovaData = $_POST['nuovaData'];

    if ($nuovaData <= date('Y-m-d')) {
        $messaggio = "Non puoi modificare la prenotazione per oggi o per una data passata.";
    } else {
        $queryLuogo = "SELECT luogo FROM prenotazione WHERE posto = ? AND username = ?";
        $stmt = $conn->prepare($queryLuogo);
        $stmt->bind_param("ss", $posto, $username);
        $stmt->execute();
        $resultLuogo = $stmt->get_result();
        $luogoResult = $resultLuogo->fetch_assoc();
        $luogoPrenotazione = $luogoResult['luogo'];

        $queryControllo = "SELECT * FROM prenotazione WHERE posto = ? AND luogo = ? AND Data = ?";
        $stmt = $conn->prepare($queryControllo);
        $stmt->bind_param("sss", $posto, $luogoPrenotazione, $nuovaData);
        $stmt->execute();
        $resultControllo = $stmt->get_result();

        if ($resultControllo->num_rows > 0) {
            $messaggio = "Esiste già una prenotazione per questo posto nella nuova data.";
        } else {
            $queryModifica = "UPDATE prenotazione SET Data = ? WHERE posto = ? AND username = ?";
            $stmt = $conn->prepare($queryModifica);
            $stmt->bind_param("sss", $nuovaData, $posto, $username);
            $stmt->execute();
            header("Location: visualizzazione.php?data=" . urlencode($nuovaData));
            exit();
        }
    }
}

if (isset($_POST['elimina'])) {
    $posto = $_POST['posto'];
    $queryElimina = "DELETE FROM prenotazione WHERE posto = ? AND username = ?";
    $stmt = $conn->prepare($queryElimina);
    $stmt->bind_param("ss", $posto, $username);
    $stmt->execute();
    $messaggio = 'Prenotazione eliminata con successo.';
}

$oggi = date('Y-m-d');

// Separare le prenotazioni
$prenotazioniProprie = [];
$prenotazioniAltrui = [];
foreach ($prenotazioni as $p) {
    if ($p['username'] === $username) {
        $prenotazioniProprie[] = $p;
    } else {
        $prenotazioniAltrui[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Le tue prenotazioni</title>
    <link rel="stylesheet" href="vis.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        form.inline {
            display: inline;
        }
    </style>
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
<h1>Prenotazioni del <?php echo htmlspecialchars($dataPrenotazione); ?></h1>
<form method="post">
    <label for="data">Seleziona una data:</label>
    <input type="date" name="data" value="<?php echo htmlspecialchars($dataPrenotazione); ?>" required>
    <button type="submit">Visualizza</button>
</form>
<?php if (isset($messaggio)) echo "<p>$messaggio</p>"; ?>

<h2>Le tue prenotazioni</h2>
<?php if (count($prenotazioniProprie) > 0): ?>
<ul>
    <?php foreach ($prenotazioniProprie as $p): ?>
        <li>
            <strong><?php echo htmlspecialchars($p['username']); ?></strong> - 
            Posto: <?php echo htmlspecialchars($p['posto']); ?> - 
            Luogo: <?php echo htmlspecialchars($p['luogo']); ?>
            <?php if ($p['Data'] > $oggi): ?>
                <form class="inline" method="post">
                    <input type="hidden" name="posto" value="<?php echo htmlspecialchars($p['posto']); ?>">
                    <input type="date" name="nuovaData" required>
                    <button type="submit" name="modifica">Modifica</button>
                </form>
                <form class="inline" method="post">
                    <input type="hidden" name="posto" value="<?php echo htmlspecialchars($p['posto']); ?>">
                    <button type="submit" name="elimina" onclick="return confirm('Sei sicuro?');">Elimina</button>
                </form>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p>Nessuna prenotazione trovata.</p>
<?php endif; ?>

<h2>Prenotazioni degli altri utenti</h2>
<?php if (count($prenotazioniAltrui) > 0): ?>
<ul>
    <?php foreach ($prenotazioniAltrui as $p): ?>
        <li>
            <strong><?php echo htmlspecialchars($p['username']); ?></strong> - 
            Posto: <?php echo htmlspecialchars($p['posto']); ?> - 
            Luogo: <?php echo htmlspecialchars($p['luogo']); ?>
            <?php if ($ruolo === 'admin' && $p['Data'] > $oggi): ?>
                <form class="inline" method="post">
                    <input type="hidden" name="posto" value="<?php echo htmlspecialchars($p['posto']); ?>">
                    <button type="submit" name="modifica">Modifica</button>
                </form>
                <form class="inline" method="post">
                    <input type="hidden" name="posto" value="<?php echo htmlspecialchars($p['posto']); ?>">
                    <button type="submit" name="elimina" onclick="return confirm('Sei sicuro?');">Elimina</button>
                </form>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p>Nessuna prenotazione trovata per gli altri utenti.</p>
<?php endif; ?>
</body>
</html>
