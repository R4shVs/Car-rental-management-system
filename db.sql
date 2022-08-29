-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gas_n_go
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `addetto`
--

DROP TABLE IF EXISTS `addetto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addetto` (
  `user_id` int(11) NOT NULL,
  `cf` char(16) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cognome` varchar(255) NOT NULL,
  `data_di_nascita` date NOT NULL,
  `domicilio` varchar(255) NOT NULL,
  `numero_di_telefono` varchar(15) NOT NULL,
  `data_di_assunzione` date NOT NULL,
  `data_scadenza_contratto` date DEFAULT NULL,
  `filiale` varchar(255) NOT NULL,
  PRIMARY KEY (`cf`),
  KEY `user_id` (`user_id`),
  KEY `filiale` (`filiale`),
  CONSTRAINT `addetto_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utente` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `addetto_ibfk_2` FOREIGN KEY (`filiale`) REFERENCES `filiale` (`nome`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addetto`
--

LOCK TABLES `addetto` WRITE;
/*!40000 ALTER TABLE `addetto` DISABLE KEYS */;
INSERT INTO `addetto` VALUES (30,'CRDRSI63H53H034D','Iris','Icardo','1963-06-13','Via Cenisio, 56','0461/649274','2022-07-05',NULL,'Super auto'),(19,'CSTCDD97T51F619I','Candida','Costarella','1997-12-11','Via I.Collino, 199','010/300397','2022-07-04','2022-08-08','Fast Cars'),(23,'DJLLDN59M69C800K','Loredana','D&#039;Ajello','1959-08-29','Via G.Whitaker, 185','0461/468210','2022-07-04',NULL,'Passione motori'),(15,'FRGDNG79L19E605C','Dionigi','Freguglia','1979-07-19','Via Talete, 238','059/226978','2022-07-01',NULL,'Agazzini'),(10,'MRCMLE01C12I511R','Emilio','Morchio','2001-03-12','Via L.Muratori, 139','0862/933851','2022-07-05','2022-08-01','Agazzini'),(8,'MSSFLR56D52A902G','Flora','Mussida','1990-04-12','Via Ciovassano, 203','0161/521276','2022-07-01',NULL,'Agazzini'),(13,'PSCLTT75S62D452H','Loretta','Pascuccio','1975-11-22','Via F.Croce, 9','075/474098','2022-07-03',NULL,'OnTheRoad'),(18,'PSTDNL44A66I641B','Daniela','Pistorino','1944-01-26','Via C.Matteucci, 181','035/1004762','2022-07-04',NULL,'Fast Cars'),(29,'ZZZRNT60B60A449B','Renata','Zazzeroni','1960-02-20','Via P.Gili, 285','0733/197867','2022-07-05',NULL,'Super auto');
/*!40000 ALTER TABLE `addetto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filiale`
--

DROP TABLE IF EXISTS `filiale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filiale` (
  `nome` varchar(255) NOT NULL,
  `data_di_apertura` date NOT NULL,
  `via` varchar(255) NOT NULL,
  `numero_di_telefono` varchar(15) NOT NULL,
  `manager` char(16) DEFAULT NULL,
  PRIMARY KEY (`nome`),
  KEY `manager` (`manager`),
  CONSTRAINT `filiale_ibfk_1` FOREIGN KEY (`manager`) REFERENCES `manager` (`cf`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filiale`
--

LOCK TABLES `filiale` WRITE;
/*!40000 ALTER TABLE `filiale` DISABLE KEYS */;
INSERT INTO `filiale` VALUES ('Agazzini','2022-07-01','Via R.Busacca, 61','0184/691674','GZZDAI00L66G336C'),('Fast Cars','2022-07-03','Via A.Ugo, 10','059/438878','NCRMGD64E63B674D'),('OnTheRoad','2022-07-03','Via S.Satta, 6','0173/103185','BRMCRO82B61L083J'),('Passione motori','2022-07-04','Via D.Riccardo, 280','0832/331212','DLBBTN84A19M177Y'),('Super auto','2022-07-01','Bastioni N.Porpora, 214','035/1009959','RVTNVS91D69C681J');
/*!40000 ALTER TABLE `filiale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager`
--

DROP TABLE IF EXISTS `manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager` (
  `user_id` int(11) NOT NULL,
  `cf` char(16) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cognome` varchar(255) NOT NULL,
  `data_di_nascita` date NOT NULL,
  `domicilio` varchar(255) NOT NULL,
  `numero_di_telefono` varchar(15) NOT NULL,
  PRIMARY KEY (`cf`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utente` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager`
--

LOCK TABLES `manager` WRITE;
/*!40000 ALTER TABLE `manager` DISABLE KEYS */;
INSERT INTO `manager` VALUES (4,'BRMCRO82B61L083J','Antonio','Brembati','1982-02-21','Via Privata L.Arsiero, 50/j','0173/103185'),(3,'DLBBTN84A19M177Y','Giovanni','De Alberti','1984-01-19','Via E.L.Morselli, 29','0832/331212'),(7,'GZZDAI00L66G336C','Aida','Agazzini','2000-07-26','Via A.Coari, 166/k','0184/691674'),(2,'NCRMGD64E63B674D','Laura','Nocerini','1980-03-12','Via E.Bellani, 70/h','059/438878'),(6,'RVTNVS91D69C681J','Nives','Rivetti','1991-04-29','Via P.Tacchini, 94','035/1009959');
/*!40000 ALTER TABLE `manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noleggio`
--

DROP TABLE IF EXISTS `noleggio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noleggio` (
  `codice_noleggio` int(11) NOT NULL AUTO_INCREMENT,
  `veicolo` char(7) NOT NULL,
  `filiale` varchar(255) NOT NULL,
  `socio` char(16) DEFAULT NULL,
  PRIMARY KEY (`codice_noleggio`),
  KEY `veicolo` (`veicolo`),
  KEY `filiale` (`filiale`),
  KEY `socio` (`socio`),
  CONSTRAINT `noleggio_ibfk_1` FOREIGN KEY (`veicolo`) REFERENCES `veicolo` (`targa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `noleggio_ibfk_2` FOREIGN KEY (`filiale`) REFERENCES `filiale` (`nome`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `noleggio_ibfk_3` FOREIGN KEY (`socio`) REFERENCES `socio` (`cf`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noleggio`
--

LOCK TABLES `noleggio` WRITE;
/*!40000 ALTER TABLE `noleggio` DISABLE KEYS */;
INSERT INTO `noleggio` VALUES (1,'AW100DR','Agazzini','CRNLVC90H45C408U'),(2,'BK283BD','Agazzini','GHISVS68M09H186X'),(3,'HN494XL','Agazzini','GRRNGL79D06F967U'),(4,'LL120XB','Agazzini','MRSMLN82A57D231B'),(5,'NX183HF','Agazzini','TRZLNZ92R01C681K'),(6,'LL120XB','Agazzini','CRNLVC90H45C408U'),(7,'VB039MN','Agazzini','FCCDRN71H65B824Z'),(8,'AW100DR','Agazzini','MRSMLN82A57D231B'),(9,'BK283BD','Agazzini','GHISVS68M09H186X'),(10,'NX183HF','Agazzini','GRRNGL79D06F967U'),(11,'HN494XL','Agazzini','CRNLVC90H45C408U'),(12,'DL075EH','Fast Cars','ZLIVRN64L01F295U'),(13,'BS396JA','Fast Cars','PTRLNE96M68H059N'),(14,'EG529TL','OnTheRoad','RCCBSL11C13D392Z'),(15,'TX091TB','Passione motori','RCCPLA91D27B788X'),(16,'VK201LM','Passione motori','MNTNMR73H49H827S'),(17,'XM172ZH','Passione motori','RSRSMN92D29I703M'),(18,'XY631KN','Passione motori','VCCMLA49S41B947G'),(19,'HJ093FN','Super auto','LSNTMC54E13B248O'),(20,'JS584LV','Super auto','NSNLLN95M46F158W'),(21,'EJ723ML','Passione motori','GHISVS68M09H186X'),(22,'JN996KY','Passione motori','MRSMLN82A57D231B'),(23,'RX534DC','Passione motori','MNTNMR73H49H827S');
/*!40000 ALTER TABLE `noleggio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noleggiocheckin`
--

DROP TABLE IF EXISTS `noleggiocheckin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noleggiocheckin` (
  `noleggio` int(11) NOT NULL,
  `addetto` char(16) NOT NULL,
  `data_operazione` datetime NOT NULL,
  PRIMARY KEY (`noleggio`,`addetto`),
  KEY `addetto` (`addetto`),
  CONSTRAINT `noleggiocheckin_ibfk_1` FOREIGN KEY (`noleggio`) REFERENCES `noleggio` (`codice_noleggio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `noleggiocheckin_ibfk_2` FOREIGN KEY (`addetto`) REFERENCES `addetto` (`cf`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noleggiocheckin`
--

LOCK TABLES `noleggiocheckin` WRITE;
/*!40000 ALTER TABLE `noleggiocheckin` DISABLE KEYS */;
INSERT INTO `noleggiocheckin` VALUES (1,'FRGDNG79L19E605C','2022-07-01 08:14:20'),(2,'MRCMLE01C12I511R','2022-07-01 08:14:25'),(3,'MSSFLR56D52A902G','2022-07-01 10:14:30'),(4,'MSSFLR56D52A902G','2022-07-01 15:14:35'),(5,'MSSFLR56D52A902G','2022-07-02 17:14:41'),(6,'FRGDNG79L19E605C','2022-07-03 20:26:02'),(7,'FRGDNG79L19E605C','2022-07-03 21:30:40'),(8,'FRGDNG79L19E605C','2022-07-04 21:42:18'),(9,'FRGDNG79L19E605C','2022-07-05 10:46:13'),(10,'FRGDNG79L19E605C','2022-07-05 10:48:10'),(11,'FRGDNG79L19E605C','2022-07-05 21:53:49'),(12,'CSTCDD97T51F619I','2022-07-03 22:17:46'),(13,'PSTDNL44A66I641B','2022-07-05 08:30:50'),(14,'PSCLTT75S62D452H','2022-07-03 09:41:06'),(15,'DJLLDN59M69C800K','2022-07-05 11:13:11'),(16,'DJLLDN59M69C800K','2022-07-05 10:40:31'),(17,'DJLLDN59M69C800K','2022-07-05 12:32:45'),(18,'DJLLDN59M69C800K','2022-07-05 14:48:20'),(19,'CRDRSI63H53H034D','2022-07-05 23:16:00'),(20,'CRDRSI63H53H034D','2022-07-05 23:16:22'),(21,'DJLLDN59M69C800K','2022-07-05 23:20:15'),(22,'DJLLDN59M69C800K','2022-07-05 23:20:28'),(23,'DJLLDN59M69C800K','2022-07-05 23:20:38');
/*!40000 ALTER TABLE `noleggiocheckin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noleggiocheckout`
--

DROP TABLE IF EXISTS `noleggiocheckout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noleggiocheckout` (
  `noleggio` int(11) NOT NULL,
  `addetto` char(16) NOT NULL,
  `data_operazione` datetime NOT NULL,
  `costo` int(11) NOT NULL,
  PRIMARY KEY (`noleggio`,`addetto`),
  KEY `addetto` (`addetto`),
  CONSTRAINT `noleggiocheckout_ibfk_1` FOREIGN KEY (`noleggio`) REFERENCES `noleggio` (`codice_noleggio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `noleggiocheckout_ibfk_2` FOREIGN KEY (`addetto`) REFERENCES `addetto` (`cf`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noleggiocheckout`
--

LOCK TABLES `noleggiocheckout` WRITE;
/*!40000 ALTER TABLE `noleggiocheckout` DISABLE KEYS */;
INSERT INTO `noleggiocheckout` VALUES (1,'FRGDNG79L19E605C','2022-07-02 15:16:31',18),(2,'MRCMLE01C12I511R','2022-07-03 12:16:36',42),(3,'FRGDNG79L19E605C','2022-07-03 10:16:22',30),(4,'MSSFLR56D52A902G','2022-07-02 06:16:33',16),(5,'MSSFLR56D52A902G','2022-07-05 08:16:26',48),(6,'MRCMLE01C12I511R','2022-07-04 17:28:11',16),(7,'FRGDNG79L19E605C','2022-07-05 21:43:22',36),(8,'FRGDNG79L19E605C','2022-07-05 21:43:26',18),(12,'CSTCDD97T51F619I','2022-07-05 22:17:54',33),(13,'PSTDNL44A66I641B','2022-07-05 22:30:53',8),(14,'PSCLTT75S62D452H','2022-07-05 22:41:46',27),(15,'DJLLDN59M69C800K','2022-07-05 22:48:29',7),(16,'DJLLDN59M69C800K','2022-07-05 22:48:26',11),(17,'DJLLDN59M69C800K','2022-07-05 22:52:01',12),(18,'DJLLDN59M69C800K','2022-07-05 22:48:23',8),(19,'ZZZRNT60B60A449B','2022-07-05 23:16:39',9),(20,'ZZZRNT60B60A449B','2022-07-05 23:16:35',9),(21,'DJLLDN59M69C800K','2022-07-05 23:20:47',5),(22,'DJLLDN59M69C800K','2022-07-05 23:20:44',15),(23,'DJLLDN59M69C800K','2022-07-05 23:20:40',8);
/*!40000 ALTER TABLE `noleggiocheckout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `socio`
--

DROP TABLE IF EXISTS `socio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio` (
  `user_id` int(11) NOT NULL,
  `cf` char(16) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cognome` varchar(255) NOT NULL,
  `data_di_nascita` date NOT NULL,
  `domicilio` varchar(255) NOT NULL,
  `numero_di_telefono` varchar(15) NOT NULL,
  `data_di_iscrizione` date NOT NULL,
  PRIMARY KEY (`cf`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `socio_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utente` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socio`
--

LOCK TABLES `socio` WRITE;
/*!40000 ALTER TABLE `socio` DISABLE KEYS */;
INSERT INTO `socio` VALUES (11,'CRNLVC90H45C408U','Ludovica','Carannante','1990-06-05','Via Pisa, 247/f','0472/168615','2022-07-01'),(17,'FCCDRN71H65B824Z','Adriana','Faccioli','1971-06-25','Alzaia F.Confalonieri, 66','099/327761','2022-07-03'),(9,'GHISVS68M09H186X','Silvestro','Ghio','1986-08-09','Via Privata della Cervia, 235','0131/488546','2022-07-01'),(14,'GRRNGL79D06F967U','Angelo','Guerrieri','1979-04-06','Via P.Veronese, 33','011/134695','2022-07-01'),(31,'LSNTMC54E13B248O','Telemaco','Alesina','1954-05-13','Via S.Camarrone, 102','095/157344','2022-07-05'),(25,'MNTNMR73H49H827S','Annamaria','Amantea','1973-06-09','Via Torchio, 248','0965/831289','2022-07-05'),(16,'MRSMLN82A57D231B','Milena','Morosini','1982-01-17','Via Fiume Volga, 201','0422/444437','2022-07-01'),(32,'NSNLLN95M46F158W','Liliana','Ansione','1995-08-06','Via F.Ciaccio, 203/j','0461/159995','2022-07-05'),(21,'PTRLNE96M68H059N','Eliana','Petroni','1996-08-28','Via G.Murat, 170','0161/567019','2022-07-05'),(22,'RCCBSL11C13D392Z','Basilio','Roccon','2011-03-13','Via P.Cambiasi, 219','0372/291993','2022-07-03'),(24,'RCCPLA91D27B788X','Paolo','Ricco','1991-04-27','Via A.Pacinotti, 233','0522/239875','2022-07-05'),(28,'RSRSMN92D29I703M','Simeone','Rosari','1992-04-29','Via V.Brocchi, 61','035/658915','2022-07-05'),(12,'TRZLNZ92R01C681K','Lorenzo','Terzoni','1992-10-01','Via Ferdinando di Savoia, 183/j','0165/870473','2022-07-02'),(27,'VCCMLA49S41B947G','Amalia','Vaccaroni','1949-11-01','Via L.Capuana, 267','0461/240308','2022-07-05'),(20,'ZLIVRN64L01F295U','Valeriano','Zilio','1964-07-01','Via Rospigliosi, 182','051/1082956','2022-07-03');
/*!40000 ALTER TABLE `socio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utente`
--

DROP TABLE IF EXISTS `utente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utente` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `ruolo` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utente`
--

LOCK TABLES `utente` WRITE;
/*!40000 ALTER TABLE `utente` DISABLE KEYS */;
INSERT INTO `utente` VALUES (1,'admin@a.com','$2y$10$bSTUQiWGsZ/lgCtNhPt9MundIRYTVn03XypBOPiFJMJiZoOW5bz8y',0),(2,'magda.nocerini@gmail.com','$2y$10$a06yumvvrXEba7XN8wXxDuV913DZRrzRf52svj306CcR8mukzeSC2',1),(3,'bastiano.dealberti@gmail.com','$2y$10$Hn3cJfgpd4R3H3qOcx3ituTYBEhhk/0FI.tMeFPfT731UUkSOwxfS',1),(4,'cora.brembati@gmail.com','$2y$10$nIHsOT.vH3/spEjQwqDimufTzwrL4n1oVdDotycueSxUSTsQf5j56',1),(6,'nives.rivetti@gmail.com','$2y$10$ZVP3sTHG0G2sLZq0KeytOe6TJ7DTn4Rw9BhnAovu0Rf5gEtkLL4a.',1),(7,'aida.agazzini@gmail.com','$2y$10$oX9py/V7pioiRBMOS5QlTOR35PBEqP2MaCNHfyN.vHZuHSCyqj/rS',1),(8,'flora.mussida@gmail.com','$2y$10$rsapBBh8Asmlx9WnyD/9J.03adRqL.r1bhbkPrgzfNKU6cXshW1GG',2),(9,'silvestro.ghio@gmail.com','$2y$10$FfNQTYQAzZj/cpjqGm1u8OuPDjxWzVfk9sxMl3lhoJIr9hXlaxlbW',3),(10,'emilio.morchio@gmail.com','$2y$10$XffoKKezHkff7pCsY1GMj.LuGQiUtMweG9m7HXLvNGlFzYEGwhiui',2),(11,'ludovica.carannante@gmail.com','$2y$10$Vq1efZqjUknPXSsmQpJkf.dzJdXUKFzEVOmlHePWIIbWpiD7TcsQq',3),(12,'lorenzo.terzoni@gmail.com','$2y$10$SOvidjwvFEmjk9H5fyfm4OYEXX4odw9RhrAq9/fotiP3bqcjXo1zK',3),(13,'lore.pasc@gmail.com','$2y$10$uIeuD2yCF.QvLgSibLaGMO/lMXjly2Xt3BSr0Fqv84qGn9FFQHYam',2),(14,'ange.guer@gmail.com','$2y$10$OpNo/x5OL3DkhIkmGfe80uHDj48mF4bXdmgwsOydBgiI9xDVf9zgm',3),(15,'dion.freg@gmail.com','$2y$10$nhIsYIa9he./knQoJ.2tQ.PLTmGGsOxVFgB9eCdlerXy7eIS.O4LC',2),(16,'milena.morosini@gmail.com','$2y$10$YQ43JtCyVdMJOtf5cujT5.g1BqLjWBS.dQ5vzCRnxRsmQ8a/xNVyS',3),(17,'abastiani@gmail.com','$2y$10$BbKDQEPel16N3TRVAOAOxuEDtn4du9DzMrLuPdVtS2dipDnQiZzqS',3),(18,'daniela.pistorino@gmail.com','$2y$10$CQ7BARFzkDYT.q6MDSziAukERHmqzZpx.S2zaPuf7tg9Z1yd5jesG',2),(19,'candida.costarella@gmail.com','$2y$10$MOcClJ9NqXa13EQrbKu8h.yyN7cjGbQnJCY0IoW8Ob2c3YTZ1RFI.',2),(20,'valeriano.zilio@gmail.com','$2y$10$O3VHVZjw/AUX02WQeZQYhuWU8Hvs4Cd8Ue/R3T87EO./iu.cVPnOS',3),(21,'eliana.petroni@gmail.com','$2y$10$HOmBCy4Xq0zkKkXj5V9J.eikaR1NPHf1LVHln0GglU8g8Io9lfKQC',3),(22,'basilio.roccon@gmail.com','$2y$10$l7oOxFKdToSfK/HBu5zgsOWQR01UiBGUoEGnc6dItyxVCrfRik/We',3),(23,'loredana.dajello@gmail.com','$2y$10$NQuUkltNodB0WV3ROhQTBu.hD1zWdZ13PLFxIdTfgs3iwmY1MmA1W',2),(24,'paol.ricc@gmail.com','$2y$10$kle4McWpZdxzz/6c9h.5Au/Yj61/gKQDmSZc1Kf079jsTW4gwX8jK',3),(25,'annamaria.amantea@gmail.com','$2y$10$h.DoVB9g9NwbgiMLy/aeVOFk6ncGBEJHfg0/8Pu3hJuZIk6L82.my',3),(27,'avaccaroni@gmail.com','$2y$10$b9IiE96B4JijDX0Wl31oHeVU/LT8Dfbr7MHTpMaBDnd6ArG3EFKr2',3),(28,'simeone.rosari@gmail.com','$2y$10$a.hy1J9mzj/2lH760ng5guauXPq5UN8pqw/P15414JEyinZ1pcNla',3),(29,'rena.zazz@gmail.com','$2y$10$uq/kF2WweYSJ99W7nBGRS.dqFJetwf.zCZzenLCdLaBn6Xit43N3u',2),(30,'iris.icardo@gmail.com','$2y$10$dOuvG8.EqIJ7IoDtPLQ.HOgU.gFq0KEl/3Cf400zRWXch/yz5/tSS',2),(31,'telemaco.alesina@gmail.com','$2y$10$zeH3P9gXoK9FJQFdAawzVe4d/2gzFzpZaXYXATJJ.OnO/UhFqaK7S',3),(32,'liliana.ansione@gmail.com','$2y$10$MqIg1RqK2uSvgaPE8LT0.ONvDIvR3rU.R3C1gZjAAh./eUlKGzqyy',3);
/*!40000 ALTER TABLE `utente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `veicolo`
--

DROP TABLE IF EXISTS `veicolo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `veicolo` (
  `targa` char(7) NOT NULL,
  `marca` varchar(255) NOT NULL,
  `modello` varchar(255) NOT NULL,
  `colore` varchar(255) NOT NULL,
  `costo_giornaliero` int(11) NOT NULL CHECK (`costo_giornaliero` >= 5),
  `stato` varchar(255) NOT NULL,
  `filiale` varchar(255) NOT NULL,
  PRIMARY KEY (`targa`),
  KEY `filiale` (`filiale`),
  CONSTRAINT `veicolo_ibfk_1` FOREIGN KEY (`filiale`) REFERENCES `filiale` (`nome`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `veicolo`
--

LOCK TABLES `veicolo` WRITE;
/*!40000 ALTER TABLE `veicolo` DISABLE KEYS */;
INSERT INTO `veicolo` VALUES ('AW100DR','Honda','Insight','Nera',9,'disponibile','Agazzini'),('BK283BD','Ford','Transit','Bianca',14,'in_noleggio','Agazzini'),('BS396JA','Honda','Integra','Nera',8,'disponibile','Fast Cars'),('DL075EH','Volkswagen','Golf Sportsvan','Grigia',11,'disponibile','Fast Cars'),('EG529TL','Suzuki','Celerio','Bianca',9,'disponibile','OnTheRoad'),('EJ723ML','Fiat','Uno','Nera',5,'disponibile','Passione motori'),('HJ093FN','Infiniti','G37','Bianca',9,'disponibile','Super auto'),('HN494XL','Kia','Optima','Rossa',10,'in_noleggio','Agazzini'),('JN996KY','Ford ','EcoSport','Nera',15,'disponibile','Passione motori'),('JS584LV','Seat','Leon','Gialla',9,'disponibile','Super auto'),('LL120XB','Skoda','Felicia','Nera',8,'disponibile','Agazzini'),('NX183HF','Opel','Corsa','Grigia',12,'in_noleggio','Agazzini'),('RX534DC','Peugeot ','107','Nera',8,'disponibile','Passione motori'),('TX091TB','Ford','Sierra','Nera',7,'disponibile','Passione motori'),('VB039MN','Rover','216','Grigia',12,'disponibile','Agazzini'),('VK201LM','Kia','Carens','Bianca',11,'disponibile','Passione motori'),('XM172ZH','BMW','518','Nera',12,'disponibile','Passione motori'),('XY631KN','Daewoo','Matiz','Nera',8,'disponibile','Passione motori');
/*!40000 ALTER TABLE `veicolo` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-07-06 17:31:28
