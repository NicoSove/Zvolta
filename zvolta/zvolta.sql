-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2025 alle 13:02
-- Versione del server: 5.6.15-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zvolta`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `parcheggio`
--

CREATE TABLE IF NOT EXISTS `parcheggio` (
  `Posto` int(11) NOT NULL AUTO_INCREMENT,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL,
  PRIMARY KEY (`Posto`),
  KEY `ID_prenotazione` (`ID_prenotazione`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `prenotazione`
--

CREATE TABLE IF NOT EXISTS `prenotazione` (
  `ID_prenotazione` int(11) NOT NULL AUTO_INCREMENT,
  `Data` date NOT NULL,
  `contModifiche` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `posto` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID_prenotazione`),
  KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;

--
-- Dump dei dati per la tabella `prenotazione`
--

INSERT INTO `prenotazione` (`ID_prenotazione`, `Data`, `contModifiche`, `username`, `posto`) VALUES
(22, '2025-02-21', 0, 'utente3.0', 'C10'),
(23, '2025-02-21', 0, 'utente3.0', 'C15'),
(26, '2025-02-22', 0, 'utente2.0', 'C7'),
(43, '2025-02-22', 0, 'utente2.0', 'C20'),
(45, '2025-02-22', 0, 'utente2.0', 'D2');

-- --------------------------------------------------------

--
-- Struttura della tabella `salariunioni`
--

CREATE TABLE IF NOT EXISTS `salariunioni` (
  `ID_salaRiunioni` int(11) NOT NULL AUTO_INCREMENT,
  `Info` text NOT NULL,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL,
  PRIMARY KEY (`ID_salaRiunioni`),
  KEY `ID_prenotazione` (`ID_prenotazione`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `scrivania a1`
--

CREATE TABLE IF NOT EXISTS `scrivania a1` (
  `Posto` int(11) NOT NULL AUTO_INCREMENT,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL,
  PRIMARY KEY (`Posto`),
  KEY `ID_prenotazione` (`ID_prenotazione`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `scrivania a2`
--

CREATE TABLE IF NOT EXISTS `scrivania a2` (
  `Posto` int(11) NOT NULL AUTO_INCREMENT,
  `Stato` varchar(50) NOT NULL,
  `ID_prenotazione` int(11) NOT NULL,
  PRIMARY KEY (`Posto`),
  KEY `ID_prenotazione` (`ID_prenotazione`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE IF NOT EXISTS `utente` (
  `nome_utente` varchar(30) NOT NULL,
  `password_utente` varchar(255) DEFAULT NULL,
  `mail_utente` varchar(30) NOT NULL,
  `cognome_utente` varchar(30) NOT NULL,
  `telefono_utente` int(10) NOT NULL,
  `ruolo_utente` varchar(30) NOT NULL,
  `username` varchar(20) NOT NULL,
  `ID_coordinatore` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`username`),
  KEY `coordinare_matricola_utente` (`ID_coordinatore`)
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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
