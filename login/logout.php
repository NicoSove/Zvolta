<?php
session_start(); // Inizia la sessione
session_destroy(); // Distrugge la sessione
header("Location: ../home.php"); // Reindirizza alla homepage o a un'altra pagina
exit(); // Termina lo script
?>