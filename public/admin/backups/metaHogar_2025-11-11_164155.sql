-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: metaHogar
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `agencia`
--

DROP TABLE IF EXISTS `agencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agencia` (
  `idAgencia` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(120) NOT NULL,
  `Contacto` varchar(120) DEFAULT NULL,
  `Telefono` varchar(25) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Direccion` varchar(255) DEFAULT NULL,
  `EstadoValidacion` enum('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE',
  PRIMARY KEY (`idAgencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agencia`
--

LOCK TABLES `agencia` WRITE;
/*!40000 ALTER TABLE `agencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `agencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cita`
--

DROP TABLE IF EXISTS `cita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cita` (
  `idCita` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Servicio_idServicio` int(11) NOT NULL,
  `FechaHora` datetime NOT NULL,
  `Estado` enum('AGENDADA','CONFIRMADA','CANCELADA','REALIZADA') DEFAULT 'AGENDADA',
  `Notas` text DEFAULT NULL,
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idCita`),
  KEY `fk_Cita_Servicio` (`Servicio_idServicio`),
  KEY `idx_cita_estado` (`Estado`),
  KEY `idx_cita_usuario_fecha` (`Usuario_idUsuario`,`FechaHora`),
  CONSTRAINT `fk_Cita_Servicio` FOREIGN KEY (`Servicio_idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Cita_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cita`
--

LOCK TABLES `cita` WRITE;
/*!40000 ALTER TABLE `cita` DISABLE KEYS */;
/*!40000 ALTER TABLE `cita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contenido`
--

DROP TABLE IF EXISTS `contenido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contenido` (
  `idContenido` int(11) NOT NULL AUTO_INCREMENT,
  `Tipo` enum('NOTICIA','ARTICULO','BLOG','SITIO_INTERES') NOT NULL,
  `Titulo` varchar(200) NOT NULL,
  `Cuerpo` text DEFAULT NULL,
  `AutorUsuario_id` int(11) DEFAULT NULL,
  `FechaPublicacion` datetime DEFAULT current_timestamp(),
  `Activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idContenido`),
  KEY `fk_Contenido_Autor` (`AutorUsuario_id`),
  KEY `idx_contenido_tipo_fecha` (`Tipo`,`FechaPublicacion`),
  CONSTRAINT `fk_Contenido_Autor` FOREIGN KEY (`AutorUsuario_id`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contenido`
--

LOCK TABLES `contenido` WRITE;
/*!40000 ALTER TABLE `contenido` DISABLE KEYS */;
/*!40000 ALTER TABLE `contenido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incidencia`
--

DROP TABLE IF EXISTS `incidencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incidencia` (
  `idIncidencia` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` int(11) DEFAULT NULL,
  `Titulo` varchar(150) DEFAULT NULL,
  `Descripcion` text DEFAULT NULL,
  `Estado` enum('ABIERTA','EN_PROGRESO','RESUELTA','CERRADA') DEFAULT 'ABIERTA',
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idIncidencia`),
  KEY `fk_Incidencia_Usuario` (`Usuario_idUsuario`),
  CONSTRAINT `fk_Incidencia_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidencia`
--

LOCK TABLES `incidencia` WRITE;
/*!40000 ALTER TABLE `incidencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `incidencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multimedia`
--

DROP TABLE IF EXISTS `multimedia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multimedia` (
  `idMedia` int(11) NOT NULL AUTO_INCREMENT,
  `Contenido_idContenido` int(11) DEFAULT NULL,
  `Tipo` enum('IMAGEN','VIDEO','DOCUMENTO') DEFAULT 'IMAGEN',
  `Ruta` varchar(255) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idMedia`),
  KEY `fk_Multimedia_Contenido` (`Contenido_idContenido`),
  CONSTRAINT `fk_Multimedia_Contenido` FOREIGN KEY (`Contenido_idContenido`) REFERENCES `contenido` (`idContenido`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multimedia`
--

LOCK TABLES `multimedia` WRITE;
/*!40000 ALTER TABLE `multimedia` DISABLE KEYS */;
/*!40000 ALTER TABLE `multimedia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificacion`
--

DROP TABLE IF EXISTS `notificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificacion` (
  `idNotificacion` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Titulo` varchar(150) DEFAULT NULL,
  `Mensaje` text DEFAULT NULL,
  `Leida` tinyint(1) DEFAULT 0,
  `FechaEnvio` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idNotificacion`),
  KEY `fk_Notificacion_Usuario` (`Usuario_idUsuario`),
  CONSTRAINT `fk_Notificacion_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion`
--

LOCK TABLES `notificacion` WRITE;
/*!40000 ALTER TABLE `notificacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(150) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Existencia` int(11) NOT NULL DEFAULT 0,
  `Activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_producto_update` AFTER UPDATE ON `producto` FOR EACH ROW BEGIN
    DECLARE vUsuario VARCHAR(100);
    SELECT USER() INTO vUsuario;
    IF (NEW.Existencia <> OLD.Existencia) THEN
        INSERT INTO AuditoriaProducto(idProducto, Cambio, Usuario)
        VALUES(NEW.idProducto, NEW.Existencia - OLD.Existencia, vUsuario);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol` (
  `idRol` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'administrador','Administrador del sistema');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicio`
--

DROP TABLE IF EXISTS `servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicio` (
  `idServicio` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(120) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Costo` decimal(10,2) DEFAULT 0.00,
  `Agencia_idAgencia` int(11) DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idServicio`),
  KEY `idx_servicio_agencia` (`Agencia_idAgencia`),
  CONSTRAINT `fk_Servicio_Agencia` FOREIGN KEY (`Agencia_idAgencia`) REFERENCES `agencia` (`idAgencia`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicio`
--

LOCK TABLES `servicio` WRITE;
/*!40000 ALTER TABLE `servicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sugerencia`
--

DROP TABLE IF EXISTS `sugerencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sugerencia` (
  `idSugerencia` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` int(11) DEFAULT NULL,
  `Titulo` varchar(150) NOT NULL,
  `Descripcion` text NOT NULL,
  `Estado` enum('PENDIENTE','EN_PROCESO','ATENDIDA','CERRADA') DEFAULT 'PENDIENTE',
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idSugerencia`),
  KEY `fk_Sugerencia_Usuario` (`Usuario_idUsuario`),
  CONSTRAINT `fk_Sugerencia_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sugerencia`
--

LOCK TABLES `sugerencia` WRITE;
/*!40000 ALTER TABLE `sugerencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `sugerencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(60) NOT NULL,
  `ApellidoP` varchar(60) DEFAULT NULL,
  `ApellidoM` varchar(60) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Telefono` varchar(25) DEFAULT NULL,
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `Tipo` varchar(45) DEFAULT NULL,
  `FechaActualizacion` datetime DEFAULT NULL,
  `IntentosFallidos` int(11) NOT NULL DEFAULT 0,
  `BloqueadoHasta` datetime DEFAULT NULL,
  `PasswordResetToken` varchar(64) DEFAULT NULL,
  `PasswordResetExpires` datetime DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `PasswordResetToken` (`PasswordResetToken`),
  KEY `idx_usuario_email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Admin','Meta','Hogar','admin@example.com','$2y$10$LhX65waxtcCTcX8/itF51Orv97X4bSvfukhHok4LH64LS4wnDiP6.','','2025-11-03 17:37:57',1,'interno','2025-11-08 00:52:10',0,NULL,NULL,NULL),(2,'Alexis','Reyes','Ocampo','alexis.re.ye.es.14@gmail.com','$2y$10$MTOSETstvfCcfTTWWXGhFefApmPHw7pz2O4aVh7sEk/x2zcFAxlFy','7772319257','2025-11-06 23:00:28',1,'externo','2025-11-06 23:18:01',0,NULL,NULL,NULL),(3,'Alexis','Reyes','Ocampo','reysalexis3@gmail.com','$2y$10$qK0jg0SCiKc0UohBcMaOU.aKKzNdTAfqXICjiZQ5TqUec5cbAZCsy','7772319257','2025-11-08 02:25:40',1,'externo',NULL,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_usuario_update` BEFORE UPDATE ON `usuario` FOR EACH ROW BEGIN
    SET NEW.FechaActualizacion = NOW();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `usuariorol`
--

DROP TABLE IF EXISTS `usuariorol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuariorol` (
  `idUsuarioRol` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Rol_idRol` int(11) NOT NULL,
  PRIMARY KEY (`idUsuarioRol`),
  KEY `fk_UsuarioRol_Usuario` (`Usuario_idUsuario`),
  KEY `fk_UsuarioRol_Rol` (`Rol_idRol`),
  CONSTRAINT `fk_UsuarioRol_Rol` FOREIGN KEY (`Rol_idRol`) REFERENCES `rol` (`idRol`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_UsuarioRol_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuariorol`
--

LOCK TABLES `usuariorol` WRITE;
/*!40000 ALTER TABLE `usuariorol` DISABLE KEYS */;
INSERT INTO `usuariorol` VALUES (1,1,1),(2,2,1),(3,3,1);
/*!40000 ALTER TABLE `usuariorol` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-11  9:41:56
