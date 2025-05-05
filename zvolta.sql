-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 05, 2025 alle 18:33
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
('sd', '$2y$10$/cbqQmMk91z1OvGb0M4SnOhYCCrUf5MpqtQU/HmTBDupNvxRcskiu', 'efefv@fvf.og', 'esfv', 1234567890, 'coordinatore', 'coor', NULL),
('coor', '$2y$10$SpARVqJq82czwViUXme97u5P2pttXrCFwo0OzkOqYO3m/3Z/rhLzW', 'prova@gmail.com', 'coord', 987654321, 'coordinatore', 'coordinatore1', NULL),
('Damiano', '$2y$10$/mV/Zrr6C93BH4Vpng.OlOiK4TGWoVCyHAg2h6Hj5fXJyCyl1T0KS', 'mia@gmail.com', 'Coccia', 1234567890, 'coordinatore', 'ErFaina', NULL),
('fv', '$2y$10$346d10YYEronyUl1qrIcX.xh0xPq3Q5EAFScgvUB4R8FJVuyF53wm', 'sfdv@sdfdfv', 'sdfv', 1323134423, 'admin', 'menager', NULL),
('sadfvf', '$2y$10$TkH7gaJ8W0vRkCfOT3IHxuesfhiDIwpN9JX44fv3w5J0KxwhSEfMS', 'sfdv@fgtgtt', 'dfv', 234567890, 'admin', 'prova1', NULL),
('erf', '$2y$10$jLpBfwGNkS2y6yQ4hPbPrOk68F9itFKAQu0O1flsvoLorB8lPBdA6', 'eweffg@fvrtrv', 'ewrg', 2345, 'utente_base', 'UtCoor', 'coor'),
('utente', '$2y$10$9kop6vTI1Odgu2ZA30Enruw9ENzcBJbgOnhMlwyeaST/m2IDmX0Cm', 'prova@gmail.com', 'ut', 1234567890, 'utente_base', 'utente1', 'coordinatore1'),
('utente', '$2y$10$2qq4Dg9UKYvWzsO.JemLWOcBrPy0BanhruKupnhsaHG/.A5cnx8H2', 'sdffvsdf@dsfv.it', 'ut', 1234567890, 'utente_base', 'utente2.0', 'coordinatore1\r\n'),
('locatelli', '$2y$10$2uCvK0CJUi/SGVK2f6il7OnzvKNegKv7APY24x/4qwWyZ0nSTVMYS', 'sdffgdasg@dfgsdffg', 't', 1234567890, 'utente_base', 'utente3.0', 'coordinatore1');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`username`),
  ADD KEY `coordinare_matricola_utente` (`ID_coordinatore`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `utente`
--
ALTER TABLE `utente`
  ADD CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`ID_coordinatore`) REFERENCES `utente` (`username`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
