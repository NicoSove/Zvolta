<?php
session_start();
// Corretto il percorso di inclusione del file connessione.php
include 'connessione.php'; // Connessione al database

// Controlla che la richiesta sia POST o GET (dipende da come vogliamo implementare)
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Recupera l'email del manager dal database
    if ($conn->connect_error) {
        http_response_code(500);
        echo "Errore di connessione al database.";
        exit();
    }

    // Query per trovare l'email del admin
    $query = "SELECT mail_utente FROM utente WHERE ruolo_utente = 'admin' LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Corretto l'indice per recuperare l'email
        $emailManager = $row['mail_utente'];

        // Simulazione invio mail (in produzione usare mail() o libreria)
        $to = $emailManager;
        $subject = "Richiesta reset password";
        $message = "Un utente ha richiesto il reset della password. Controlla il sistema per procedere.";
        $headers = "From: no-reply@zvolta.com";

        // Per ora simuliamo l'invio con un messaggio di conferma
        // mail($to, $subject, $message, $headers);

        echo "Email inviata al manager: $emailManager";
    } else {
        echo "Manager non trovato.";
    }

    $conn->close();
} else {
    http_response_code(405);
    echo "Metodo non consentito.";
}
?>
