-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 08 déc. 2020 à 17:37
-- Version du serveur :  5.7.31
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `figure`
--

-- --------------------------------------------------------

--
-- Structure de la table `calcul`
--

DROP TABLE IF EXISTS `calcul`;
CREATE TABLE IF NOT EXISTS `calcul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `losange`
--

DROP TABLE IF EXISTS `losange`;
CREATE TABLE IF NOT EXISTS `losange` (
  `idcalcul` int(11) NOT NULL,
  `pD` float NOT NULL,
  `gD` float NOT NULL,
  `aire` float NOT NULL,
  PRIMARY KEY (`idcalcul`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `trapeze`
--

DROP TABLE IF EXISTS `trapeze`;
CREATE TABLE IF NOT EXISTS `trapeze` (
  `idcalcul` int(11) NOT NULL,
  `sB` float NOT NULL,
  `b` float NOT NULL,
  `h` float NOT NULL,
  `aire` float NOT NULL,
  PRIMARY KEY (`idcalcul`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `losange`
--
ALTER TABLE `losange`
  ADD CONSTRAINT `lo_calcul_fk` FOREIGN KEY (`idcalcul`) REFERENCES `calcul` (`id`);

--
-- Contraintes pour la table `trapeze`
--
ALTER TABLE `trapeze`
  ADD CONSTRAINT `tr_calcul_fk` FOREIGN KEY (`idcalcul`) REFERENCES `calcul` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
