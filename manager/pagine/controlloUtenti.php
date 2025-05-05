<?php
session_start();
include 'connessione.php';

$isLoggedIn = isset($_SESSION['username']);

// Controllo se l'utente è loggato, altrimenti reindirizzo al login
if (!isset($_SESSION['username'])) {
    header("Location:  ../../login/login.php");
    exit();
}

$username = $_SESSION['username'];

// Recupero il ruolo dell'utente loggato
$query = "SELECT ruolo_utente FROM utente WHERE username = ?";
$stmt = $conn->prepare($query);
if (!$stmt) die("Errore prepare: " . $conn->error);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Se l'utente non esiste, reindirizzo al login
if ($result->num_rows === 0) {
    header("Location: ../../login/login.php");
    exit();
}
$row = $result->fetch_assoc();
$ruolo = $row['ruolo_utente'];

// Se l'utente non è admin, reindirizzo al login
if ($ruolo !== 'admin') {
    header("Location:  ../../login/login.php");
    exit();
}

// Gestione modifica utente
if (isset($_POST['modifica'])) {
    $userToEdit = $_POST['userToEdit'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $ruoloUtente = $_POST['ruolo'];
    // Recupero il coordinatore se presente (per utente_base)
    $coordinatore = isset($_POST['coordinatore']) ? $_POST['coordinatore'] : NULL;
    // Recupero la nuova password se inserita
    $nuovaPassword = isset($_POST['password']) ? $_POST['password'] : '';

    // Non permettere di modificare l'admin stesso
    if ($userToEdit === $username) {
        $messaggio = "Non puoi modificare il tuo profilo da questa pagina.";
    } else {
        // Se è stata inserita una nuova password, la cripto e la aggiungo alla query
        if (!empty($nuovaPassword)) {
            $passwordHash = password_hash($nuovaPassword, PASSWORD_DEFAULT);
            if ($ruoloUtente === 'utente_base') {
                $updateQuery = "UPDATE utente SET nome_utente = ?, cognome_utente = ?, mail_utente = ?, telefono_utente = ?, ruolo_utente = ?, ID_coordinatore = ?, password_utente = ? WHERE username = ?";
                $stmt = $conn->prepare($updateQuery);
                if (!$stmt) {
                    $messaggio = "Errore prepare update: " . $conn->error;
                } else {
                    // Gestione coordinatore: se NULL, passare stringa vuota
                    $coordinatoreParam = $coordinatore === NULL ? '' : $coordinatore;
                    $stmt->bind_param("ssssssss", $nome, $cognome, $email, $telefono, $ruoloUtente, $coordinatoreParam, $passwordHash, $userToEdit);
                    if ($stmt->execute()) {
                        $messaggio = "Utente modificato con successo.";
                    } else {
                        $messaggio = "Errore durante la modifica: " . $stmt->error;
                    }
                }
            } else {
                $updateQuery = "UPDATE utente SET nome_utente = ?, cognome_utente = ?, mail_utente = ?, telefono_utente = ?, ruolo_utente = ?, ID_coordinatore = NULL, password_utente = ? WHERE username = ?";
                $stmt = $conn->prepare($updateQuery);
                if (!$stmt) {
                    $messaggio = "Errore prepare update: " . $conn->error;
                } else {
                    $stmt->bind_param("sssssss", $nome, $cognome, $email, $telefono, $ruoloUtente, $passwordHash, $userToEdit);
                    if ($stmt->execute()) {
                        $messaggio = "Utente modificato con successo.";
                    } else {
                        $messaggio = "Errore durante la modifica: " . $stmt->error;
                    }
                }
            }
        } else {
            // Se non è stata inserita la password, aggiorno senza modificarla
            if ($ruoloUtente === 'utente_base') {
                $updateQuery = "UPDATE utente SET nome_utente = ?, cognome_utente = ?, mail_utente = ?, telefono_utente = ?, ruolo_utente = ?, ID_coordinatore = ? WHERE username = ?";
                $stmt = $conn->prepare($updateQuery);
                if (!$stmt) {
                    $messaggio = "Errore prepare update: " . $conn->error;
                } else {
                    // Gestione coordinatore: se NULL, passare stringa vuota
                    $coordinatoreParam = $coordinatore === NULL ? '' : $coordinatore;
                    $stmt->bind_param("sssssss", $nome, $cognome, $email, $telefono, $ruoloUtente, $coordinatoreParam, $userToEdit);
                    if ($stmt->execute()) {
                        $messaggio = "Utente modificato con successo.";
                    } else {
                        $messaggio = "Errore durante la modifica: " . $stmt->error;
                    }
                }
            } else {
                $updateQuery = "UPDATE utente SET nome_utente = ?, cognome_utente = ?, mail_utente = ?, telefono_utente = ?, ruolo_utente = ?, ID_coordinatore = NULL WHERE username = ?";
                $stmt = $conn->prepare($updateQuery);
                if (!$stmt) {
                    $messaggio = "Errore prepare update: " . $conn->error;
                } else {
                    $stmt->bind_param("ssssss", $nome, $cognome, $email, $telefono, $ruoloUtente, $userToEdit);
                    if ($stmt->execute()) {
                        $messaggio = "Utente modificato con successo.";
                    } else {
                        $messaggio = "Errore durante la modifica: " . $stmt->error;
                    }
                }
            }
        }
    }
}

// Gestione eliminazione utente
if (isset($_POST['elimina'])) {
    $userToDelete = $_POST['userToDelete'];

    // Non permettere di eliminare l'admin stesso
    if ($userToDelete === $username) {
        $messaggio = "Non puoi eliminare il tuo profilo.";
    } else {
        $deleteQuery = "DELETE FROM utente WHERE username = ?";
        $stmt = $conn->prepare($deleteQuery);
        if (!$stmt) {
            $messaggio = "Errore prepare delete: " . $conn->error;
        } else {
            $stmt->bind_param("s", $userToDelete);
            if ($stmt->execute()) {
                $messaggio = "Utente eliminato con successo.";
            } else {
                $messaggio = "Errore durante l'eliminazione: " . $stmt->error;
            }
        }
    }
}

// Recupera tutti gli utenti tranne l'admin loggato
$queryUsers = "SELECT username, nome_utente, cognome_utente, mail_utente, telefono_utente, ruolo_utente, ID_coordinatore FROM utente WHERE username != ?";
$stmt = $conn->prepare($queryUsers);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultUsers = $stmt->get_result();
$users = $resultUsers->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Controllo Utenti - Admin</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
        }
        form.inline {
            display: inline;
        }
        input[type="text"], input[type="email"], input[type="tel"], select {
            width: 100%;
            box-sizing: border-box;
        }
        .message {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="controllo.css"> 
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
                    <a href="../../login/logout.php" class="login-button">LOGOUT</a>
                <?php else: ?>
                    <a href="../../login/login.php" class="login-button">LOGIN</a>
                <?php endif; ?>
                <div class="user-icon">
                    <img src="../extra/placeholder.png" alt="Foto">
                </div>
            </nav>
        </div>
    </header>

<?php if (isset($messaggio)) echo "<p class='message'>$messaggio</p>"; ?>
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Nome</th>
            <th>Cognome</th>
            <th>Email</th>
            <th>Telefono</th>
            <th>Ruolo</th>
            <th>Coordinatore</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <form method="post">
                <td><?php echo htmlspecialchars($user['username']); ?>
                    <input type="hidden" name="userToEdit" value="<?php echo htmlspecialchars($user['username']); ?>">
                </td>
                <td><input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome_utente']); ?>" required></td>
                <td><input type="text" name="cognome" value="<?php echo htmlspecialchars($user['cognome_utente']); ?>" required></td>
                <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['mail_utente']); ?>" required></td>
                <td><input type="tel" name="telefono" value="<?php echo htmlspecialchars($user['telefono_utente']); ?>" required></td>
                <td>
                    <select name="ruolo" required>
                        <option value="admin" <?php if ($user['ruolo_utente'] === 'admin') echo 'selected'; ?>>Admin</option>
                        <option value="coordinatore" <?php if ($user['ruolo_utente'] === 'coordinatore') echo 'selected'; ?>>Coordinatore</option>
                        <option value="utente_base" <?php if ($user['ruolo_utente'] === 'utente_base') echo 'selected'; ?>>Utente base</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="coordinatore" value="<?php echo htmlspecialchars($user['ID_coordinatore']); ?>" <?php if ($user['ruolo_utente'] !== 'utente_base') echo 'disabled'; ?>>
                </td>
                <td>
                    <input type="password" name="password" placeholder="Nuova password (opzionale)">
                    <button type="submit" name="modifica">Modifica</button>
            </form>
            <form method="post" onsubmit="return confirm('Sei sicuro di voler eliminare questo utente?');" class="inline">
                <input type="hidden" name="userToDelete" value="<?php echo htmlspecialchars($user['username']); ?>">
                <button type="submit" name="elimina">Elimina</button>
            </form>
                </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script>
    // Disabilita il campo coordinatore se il ruolo non è utente_base
    document.querySelectorAll('select[name="ruolo"]').forEach(function(select) {
        select.addEventListener('change', function() {
            var coordinatoreInput = this.closest('tr').querySelector('input[name="coordinatore"]');
            if (this.value === 'utente_base') {
                coordinatoreInput.disabled = false;
            } else {
                coordinatoreInput.disabled = true;
                coordinatoreInput.value = '';
            }
        });
    });
</script>
</body>
</html>
