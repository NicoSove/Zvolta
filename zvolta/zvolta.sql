-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 25, 2025 alle 23:20
-- Versione del server: 5.7.17
-- Versione PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zvolta`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `parcheggio`
--

CREATE TABLE `parcheggio` (
  `Posto` int(11) NOT NULL,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `prenotazione`
--

CREATE TABLE `prenotazione` (
  `ID_prenotazione` int(11) NOT NULL,
  `Data` date NOT NULL,
  `contModifiche` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `posto` varchar(100) DEFAULT NULL,
  `luogo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `prenotazione`
--

INSERT INTO `prenotazione` (`ID_prenotazione`, `Data`, `contModifiche`, `username`, `posto`, `luogo`) VALUES
(22, '2025-02-21', 0, 'utente3.0', 'C10', NULL),
(23, '2025-02-21', 0, 'utente3.0', 'C15', NULL),
(26, '2025-02-22', 0, 'utente2.0', 'C7', NULL),
(43, '2025-02-22', 0, 'utente2.0', 'C20', NULL),
(45, '2025-02-22', 0, 'utente2.0', 'D2', 'A2'),
(46, '2025-02-25', 0, 'utente2.0', 'C23', 'A2'),
(48, '2025-02-25', 0, 'utente2.0', 'C15', 'A2'),
(49, '2025-02-25', 0, 'utente2.0', 'A6', 'A1'),
(50, '2025-02-25', 0, 'utente2.0', 'A11', 'A1'),
(51, '2025-02-25', 0, 'utente2.0', 'C7', 'A2'),
(52, '2025-02-25', 0, 'utente2.0', 'C18', 'A2'),
(53, '2025-02-25', 0, 'utente3.0', 'D5', 'A2'),
(54, '2025-02-25', 0, 'utente3.0', 'A14', 'A1'),
(55, '2025-02-25', 0, 'utente3.0', 'A16', 'A1');

-- --------------------------------------------------------

--
-- Struttura della tabella `salariunioni`
--

CREATE TABLE `salariunioni` (
  `ID_salaRiunioni` int(11) NOT NULL,
  `Info` text NOT NULL,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `scrivania a1`
--

CREATE TABLE `scrivania a1` (
  `Posto` int(11) NOT NULL,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `scrivania a2`
--

CREATE TABLE `scrivania a2` (
  `Posto` int(11) NOT NULL,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `nome_utente` varchar(30) NOT NULL,
  `password_utente` varchar(255) DEFAULT NULL,
  `mail_utente` varchar(30) NOT NULL,
  `cognome_utente` varchar(30) NOT NULL,
  `telefono_utente` int(10) NOT NULL,
  `ruolo_utente` varchar(30) NOT NULL,
  `username` varchar(20) NOT NULL,
  `ID_coordinatore` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`nome_utente`, `password_utente`, `mail_utente`, `cognome_utente`, `telefono_utente`, `ruolo_utente`, `username`, `ID_coordinatore`) VALUES
('coor', '$2y$10$8sk2CtgUGtSauWZgU9nIKeSqdvwgMMIeX2FxCmBL2zudZleBiKUX.', 'prova@gmail.com', 'coord', 987654321, 'coordinatore', 'coordinatore1', NULL),
('Damiano', '$2y$10$/mV/Zrr6C93BH4Vpng.OlOiK4TGWoVCyHAg2h6Hj5fXJyCyl1T0KS', 'mia@gmail.com', 'Coccia', 1234567890, 'coordinatore', 'ErFaina', NULL),
('massimo', '$2y$10$iQdOU2ju4M66zNxcPc54AuRkm3C00MmcMy42xYQpp9KgBet921wsW', 'mia@gmail.com', 'greco', 1234567890, 'admin', 'max11', NULL),
('utente', '$2y$10$9kop6vTI1Odgu2ZA30Enruw9ENzcBJbgOnhMlwyeaST/m2IDmX0Cm', 'prova@gmail.com', 'ut', 1234567890, 'utente_base', 'utente1', 'coordinatore1'),
('utente', '$2y$10$2qq4Dg9UKYvWzsO.JemLWOcBrPy0BanhruKupnhsaHG/.A5cnx8H2', 'sdffvsdf@dsfv.it', 'ut', 1234567890, 'utente_base', 'utente2.0', 'coordinatore1'),
('u', '$2y$10$2uCvK0CJUi/SGVK2f6il7OnzvKNegKv7APY24x/4qwWyZ0nSTVMYS', 'sdffgdasg@dfgsdffg', 't', 1234567890, 'utente_base', 'utente3.0', 'coordinatore1');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `parcheggio`
--
ALTER TABLE `parcheggio`
  ADD PRIMARY KEY (`Posto`),
  ADD KEY `ID_prenotazione` (`ID_prenotazione`);

--
-- Indici per le tabelle `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD PRIMARY KEY (`ID_prenotazione`),
  ADD KEY `username` (`username`);

--
-- Indici per le tabelle `salariunioni`
--
ALTER TABLE `salariunioni`
  ADD PRIMARY KEY (`ID_salaRiunioni`),
  ADD KEY `ID_prenotazione` (`ID_prenotazione`);

--
-- Indici per le tabelle `scrivania a1`
--
ALTER TABLE `scrivania a1`
  ADD PRIMARY KEY (`Posto`),
  ADD KEY `ID_prenotazione` (`ID_prenotazione`);

--
-- Indici per le tabelle `scrivania a2`
--
ALTER TABLE `scrivania a2`
  ADD PRIMARY KEY (`Posto`),
  ADD KEY `ID_prenotazione` (`ID_prenotazione`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`username`),
  ADD KEY `coordinare_matricola_utente` (`ID_coordinatore`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `parcheggio`
--
ALTER TABLE `parcheggio`
  MODIFY `Posto` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `prenotazione`
--
ALTER TABLE `prenotazione`
  MODIFY `ID_prenotazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
--
-- AUTO_INCREMENT per la tabella `salariunioni`
--
ALTER TABLE `salariunioni`
  MODIFY `ID_salaRiunioni` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `scrivania a1`
--
ALTER TABLE `scrivania a1`
  MODIFY `Posto` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `scrivania a2`
--
ALTER TABLE `scrivania a2`
  MODIFY `Posto` int(11) NOT NULL AUTO_INCREMENT;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `parcheggio`
--
ALTER TABLE `parcheggio`
  ADD CONSTRAINT `parcheggio_ibfk_1` FOREIGN KEY (`ID_prenotazione`) REFERENCES `prenotazione` (`ID_prenotazione`);

--
-- Limiti per la tabella `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD CONSTRAINT `prenotazione_ibfk_1` FOREIGN KEY (`username`) REFERENCES `utente` (`username`);

--
-- Limiti per la tabella `salariunioni`
--
ALTER TABLE `salariunioni`
  ADD CONSTRAINT `salariunioni_ibfk_1` FOREIGN KEY (`ID_prenotazione`) REFERENCES `prenotazione` (`ID_prenotazione`);

--
-- Limiti per la tabella `scrivania a1`
--
ALTER TABLE `scrivania a1`
  ADD CONSTRAINT `scrivania a1_ibfk_1` FOREIGN KEY (`ID_prenotazione`) REFERENCES `prenotazione` (`ID_prenotazione`);

--
-- Limiti per la tabella `scrivania a2`
--
ALTER TABLE `scrivania a2`
  ADD CONSTRAINT `scrivania a2_ibfk_1` FOREIGN KEY (`ID_prenotazione`) REFERENCES `prenotazione` (`ID_prenotazione`);

--
-- Limiti per la tabella `utente`
--
ALTER TABLE `utente`
  ADD CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`ID_coordinatore`) REFERENCES `utente` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
