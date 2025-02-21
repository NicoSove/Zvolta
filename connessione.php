<?php
$servername = "localhost";  // Se il database è locale
$username = "root";         // Username di MySQL
$password = "";             // Password (vuota di default su EasyPHP)
$dbname = "zvolta";         // Nome del database

// Creazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Controlla se la connessione ha errori
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
