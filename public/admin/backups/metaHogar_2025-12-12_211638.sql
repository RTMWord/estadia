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
  `Contacto` varchar(120) NOT NULL,
  `Telefono` varchar(25) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Direccion` varchar(255) NOT NULL,
  `EstadoValidacion` enum('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE',
  PRIMARY KEY (`idAgencia`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agencia`
--

LOCK TABLES `agencia` WRITE;
/*!40000 ALTER TABLE `agencia` DISABLE KEYS */;
INSERT INTO `agencia` VALUES (1,'Agencia de enfermeras','Juan Sanchez Godoy','7772319257','17250902@uagro.mx','aguilas 18 manzana 1 Cond tarianes','APROBADA'),(2,'Asistencia Geriátrica S.A.','Carlos Ruiz','7776001000','contacto@agencia1.com','Calle Mayor 10, Jiutepec','APROBADA'),(3,'Transporte Privado','Laura Soto','7776002000','contacto@agencia2.com','Av. Central 20, Jiutepec','PENDIENTE'),(10,'Consultorio Integral','Dr. Javier Soto','7776100100','consultorio@test.com','Calle Falsa 123, Jiutepec','PENDIENTE'),(11,'Logística Médica','Lic. Elena Pérez','7776100200','logistica@test.com','Blvd. Central 456, Jiutepec','APROBADA');
/*!40000 ALTER TABLE `agencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carousel_testimonios`
--

DROP TABLE IF EXISTS `carousel_testimonios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carousel_testimonios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `multimedia_id` int(11) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_multimedia` (`multimedia_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carousel_testimonios`
--

LOCK TABLES `carousel_testimonios` WRITE;
/*!40000 ALTER TABLE `carousel_testimonios` DISABLE KEYS */;
INSERT INTO `carousel_testimonios` VALUES (1,1,0,1),(10,10,1,1),(11,11,2,1);
/*!40000 ALTER TABLE `carousel_testimonios` ENABLE KEYS */;
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
  `Estado` enum('PENDIENTE','AGENDADA','CONFIRMADA','CANCELADA','REALIZADA') DEFAULT 'PENDIENTE',
  `Notas` text NOT NULL,
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idCita`),
  KEY `fk_Cita_Servicio` (`Servicio_idServicio`),
  KEY `idx_cita_estado` (`Estado`),
  KEY `idx_cita_usuario_fecha` (`Usuario_idUsuario`,`FechaHora`),
  CONSTRAINT `fk_Cita_Servicio` FOREIGN KEY (`Servicio_idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Cita_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cita`
--

LOCK TABLES `cita` WRITE;
/*!40000 ALTER TABLE `cita` DISABLE KEYS */;
INSERT INTO `cita` VALUES (1,1,1,'2025-12-01 15:15:00','AGENDADA','Necesito que me respondan a la brevedad, es una urgencia!!!','2025-12-09 23:11:42'),(2,2,2,'2025-12-19 14:00:00','PENDIENTE','Urgente, llamar antes.','2025-12-12 08:14:47'),(3,2,3,'2025-12-18 10:00:00','AGENDADA','Este horario está ocupado en la capa de persistencia.','2025-12-12 08:14:47'),(4,2,1,'2025-12-18 10:00:00','CONFIRMADA','Requiere silla de ruedas.','2025-12-12 08:14:47'),(10,10,11,'2025-12-25 11:00:00','AGENDADA','Cita para Navidad. Confirmar dos días antes.','2025-12-12 08:22:35'),(11,11,10,'2025-12-10 09:30:00','CANCELADA','El usuario canceló por enfermedad.','2025-12-12 08:22:35'),(12,10,10,'2025-12-05 15:00:00','REALIZADA','Consulta exitosa. Paciente estable.','2025-12-01 10:00:00');
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
  `Cuerpo` text NOT NULL,
  `AutorUsuario_id` int(11) DEFAULT NULL,
  `FechaPublicacion` datetime NOT NULL DEFAULT current_timestamp(),
  `Activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idContenido`),
  KEY `fk_Contenido_Autor` (`AutorUsuario_id`),
  KEY `idx_contenido_tipo_fecha` (`Tipo`,`FechaPublicacion`),
  CONSTRAINT `fk_Contenido_Autor` FOREIGN KEY (`AutorUsuario_id`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contenido`
--

LOCK TABLES `contenido` WRITE;
/*!40000 ALTER TABLE `contenido` DISABLE KEYS */;
INSERT INTO `contenido` VALUES (1,'ARTICULO','Información que cura','Este es un mensaje de prueba para que podamos observar la insuficiencia de este sistema',NULL,'2025-11-24 17:46:10',1),(10,'BLOG','Ventajas de la Fisioterapia','Detalle sobre los beneficios y ejercicios...',1,'2025-12-12 08:23:03',1),(11,'NOTICIA','Nueva Ley de Asistencia','El ayuntamiento aprueba la nueva normativa...',1,'2025-12-12 08:23:03',1);
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
  `Titulo` varchar(150) NOT NULL,
  `Descripcion` text NOT NULL,
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
  `Descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`idMedia`),
  KEY `fk_Multimedia_Contenido` (`Contenido_idContenido`),
  CONSTRAINT `fk_Multimedia_Contenido` FOREIGN KEY (`Contenido_idContenido`) REFERENCES `contenido` (`idContenido`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multimedia`
--

LOCK TABLES `multimedia` WRITE;
/*!40000 ALTER TABLE `multimedia` DISABLE KEYS */;
INSERT INTO `multimedia` VALUES (1,NULL,'IMAGEN','assets/media/media_1763523364_9433.jpeg','ejemplo clave'),(10,10,'IMAGEN','/uploads/blog/fisio_main.jpg','Imagen principal del artículo de fisioterapia.'),(11,11,'IMAGEN','/uploads/noticias/ley_aprobada.png','Gráfico de la nueva ley de asistencia.');
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
  `Titulo` varchar(150) NOT NULL,
  `Mensaje` text NOT NULL,
  `Leida` tinyint(1) DEFAULT 0,
  `FechaEnvio` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idNotificacion`),
  KEY `fk_Notificacion_Usuario` (`Usuario_idUsuario`),
  CONSTRAINT `fk_Notificacion_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificacion`
--

LOCK TABLES `notificacion` WRITE;
/*!40000 ALTER TABLE `notificacion` DISABLE KEYS */;
INSERT INTO `notificacion` VALUES (1,2,'Respuesta a tu sugerencia: Nunca hay sistema','Hola buenas noches, no nos interesa tu sugerencia, adios.',0,'2025-12-10 20:53:00'),(2,2,'Respuesta a tu sugerencia: Nunca hay sistema','Hola buenas noches, no estamos interesados en las sugerencias u observaciones que nos estas haciendo pero agradecemos que te hayas tomado la molestia de escribirnos.',0,'2025-12-10 20:58:07'),(3,6,'Respuesta a tu sugerencia: Es fake ','Hola Hector te responde el encargado de este sistema bla bla, la neta no se que decirte pero la cheimbaum no vale na\'',0,'2025-12-10 21:05:28');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES (1,'vendas','descripcion random',12.00,777,1),(10,'Reloj de Asistencia','Reloj con GPS y botón de pánico.',1200.00,50,1),(11,'Baston Ergonómico','Baston de altura ajustable con luz LED.',350.50,0,0),(12,'Alarma de Caídas','Sensor de muñeca que detecta caídas.',980.00,12,1);
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
  `Descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'administrador','Administrador del sistema'),(2,'Usuario','Ciudadano / Familiar / Adulto mayor'),(3,'PROVEEDOR','Representante de agencia con acceso a gestión de servicios propios.');
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
  `Descripcion` text NOT NULL,
  `Costo` decimal(10,2) DEFAULT 0.00,
  `Agencia_idAgencia` int(11) DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idServicio`),
  KEY `idx_servicio_agencia` (`Agencia_idAgencia`),
  CONSTRAINT `fk_Servicio_Agencia` FOREIGN KEY (`Agencia_idAgencia`) REFERENCES `agencia` (`idAgencia`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicio`
--

LOCK TABLES `servicio` WRITE;
/*!40000 ALTER TABLE `servicio` DISABLE KEYS */;
INSERT INTO `servicio` VALUES (1,'Servicio de traslado','Nos dedicamos a trasladar personas viejitos',15000.00,1,1),(2,'Acompañamiento 24H','Servicio de asistencia permanente.',350.50,1,1),(3,'Enfermería a Domicilio','Servicio de enfermería por horas.',280.00,2,1),(4,'Transporte Especializado','Traslados médicos y personales.',150.00,1,1),(10,'Consulta Nutricional','Consulta especializada en nutrición para adultos mayores.',550.00,10,1),(11,'Fisioterapia a Domicilio','Sesiones de rehabilitación en casa.',750.00,11,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sugerencia`
--

LOCK TABLES `sugerencia` WRITE;
/*!40000 ALTER TABLE `sugerencia` DISABLE KEYS */;
INSERT INTO `sugerencia` VALUES (1,2,'Nunca hay sistema','no cuenta con sistema las 24 horas del dia ','ATENDIDA','2025-12-10 20:51:45'),(2,6,'Es fake ','El sitio es una estafa de primera, viva la 4T #AMLO','EN_PROCESO','2025-12-10 21:04:35'),(3,NULL,'nose','es una prueba','PENDIENTE','2025-12-12 13:40:45');
/*!40000 ALTER TABLE `sugerencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonio`
--

DROP TABLE IF EXISTS `testimonio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonio` (
  `idTestimonio` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(150) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Calificacion` tinyint(4) NOT NULL DEFAULT 5,
  `Testimonio` text NOT NULL,
  `Aprobado` tinyint(1) DEFAULT 0,
  `FechaCreacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idTestimonio`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonio`
--

LOCK TABLES `testimonio` WRITE;
/*!40000 ALTER TABLE `testimonio` DISABLE KEYS */;
INSERT INTO `testimonio` VALUES (1,'Alexis Reyes Ocampo','',1,'Este es un ejemplo de cómo se vería el sitio de \"Testimonios\"',1,'2025-11-13 23:13:20'),(2,'Juanito Perez Corral.es','',1,'No venden pizza, pésimo servicio.',0,'2025-11-18 21:37:19'),(3,'Sergio elSabado','',1,'no me gustó este sitio web porque es demasiado bootstrap',0,'2025-11-28 19:13:07'),(4,'Juan Ernesto Mar Hernandez','',1,'De lo peor, es triste ver estos sistemas en producción',0,'2025-12-10 20:32:15'),(10,'Alicia Rivas','alicia@mail.com',5,'Excelente servicio, el acompañamiento superó mis expectativas.',1,'2025-12-12 08:23:17'),(11,'Pedro Martínez','pedro@mail.com',3,'El catálogo de productos podría mejorar.',0,'2025-12-12 08:23:17'),(12,'Juana Castillo','juana@mail.com',5,'Gracias a la alarma de caídas, mi padre está más seguro.',1,'2025-12-12 08:23:17');
/*!40000 ALTER TABLE `testimonio` ENABLE KEYS */;
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
  `ApellidoP` varchar(60) NOT NULL,
  `ApellidoM` varchar(60) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Telefono` varchar(25) NOT NULL,
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `Tipo` varchar(45) NOT NULL,
  `FechaActualizacion` datetime DEFAULT NULL,
  `IntentosFallidos` int(11) NOT NULL DEFAULT 0,
  `BloqueadoHasta` datetime DEFAULT NULL,
  `PasswordResetToken` varchar(64) DEFAULT NULL,
  `PasswordResetExpires` datetime DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `PasswordResetToken` (`PasswordResetToken`),
  KEY `idx_usuario_email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Admin','Meta','Hogar','admin@example.com','$2y$10$LhX65waxtcCTcX8/itF51Orv97X4bSvfukhHok4LH64LS4wnDiP6.','','2025-11-03 17:37:57',1,'interno','2025-12-12 13:42:37',0,NULL,NULL,NULL),(2,'Alexis','Reyes','Ocampo','alexis.re.ye.es.14@gmail.com','$2y$10$MTOSETstvfCcfTTWWXGhFefApmPHw7pz2O4aVh7sEk/x2zcFAxlFy','7772319257','2025-11-06 23:00:28',1,'externo','2025-12-09 22:41:04',0,NULL,'587f5d201bf227c1aec0164e8f71db9a785cb102bce4a196b5930862c7ae05b5','2025-11-15 04:08:57'),(3,'Alexis','Reyes','Ocampo','reysalexis3@gmail.com','$2y$10$qK0jg0SCiKc0UohBcMaOU.aKKzNdTAfqXICjiZQ5TqUec5cbAZCsy','7772319257','2025-11-08 02:25:40',1,'externo',NULL,0,NULL,NULL,NULL),(4,'Juan 55','Reyes','Ocampo5','alex_iss11@outlook.com','$2y$10$85UFFJgN6xMb68mklCmS4O9u6D.71.jt5yWnM1McPfh7uLQ7rq2rC','7772319257','2025-11-13 23:28:07',0,'externo','2025-12-09 22:41:10',0,NULL,NULL,NULL),(5,'Alexis','Reyes','Ocampo','rklcolek@outlook.com','$2y$10$ge.eLz.Eo9XwejFwp65aHeDLoCXPY6hc4AE/f.7pECHrcQAgTuroO','7772319257','2025-11-13 23:37:42',0,'interno','2025-11-13 23:43:09',1,NULL,NULL,NULL),(6,'Hector','Hernandez','Velazquez','hvho221863@upemor.edu.mx','$2y$10$/Pfj0WfLzHvNd62N4B/w1udq6G9PHc45H/kQvNCh8y2xSOnsXwRHm','7774528561','2025-12-10 21:03:57',1,'externo',NULL,0,NULL,NULL,NULL),(7,'Beto','Fidel','Vargas','beto.v12@gmail.com','$2y$10$3WfOOaU6L/FifS9WYod.Wez2ZwHtYXt9u/Y6l9zGW/uowsNicsCqO','777134789','2025-12-12 08:05:08',1,'Usuario','2025-12-12 08:06:25',5,'2025-12-12 08:21:25',NULL,NULL),(8,'Leopoldo','Juarez','Molina','leopoldo.jm@gmail.com','$2y$10$lII1H9WCbo3DHKBOQ.4GE.TcMOG79tbU4lcyTS9bqrnvYy79feXyS','777456134','2025-12-12 08:08:15',1,'externo',NULL,0,NULL,NULL,NULL),(10,'Sofia','Hernandez','Rojas','sofia@ciudadano.com','$2a$10$HASH_SEGURO_PROD','7775551212','2025-12-12 08:17:36',0,'externo','2025-12-12 08:19:48',0,NULL,NULL,NULL),(11,'Roberto','Mendoza','Solis','roberto@ciudadano.com','$2a$10$HASH_SEGURO_PROD','7775551313','2025-12-12 08:17:36',1,'',NULL,0,NULL,NULL,NULL),(12,'Elena','Vargas','Perez','agencia.prueba@test.com','$2a$10$HASH_SEGURO_PROD','7775551414','2025-12-12 08:17:36',1,'',NULL,0,NULL,'TOKEN_12345',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuariorol`
--

LOCK TABLES `usuariorol` WRITE;
/*!40000 ALTER TABLE `usuariorol` DISABLE KEYS */;
INSERT INTO `usuariorol` VALUES (1,1,1),(2,2,1),(3,3,1),(4,4,1),(5,5,2),(6,6,2),(7,7,2),(8,8,3),(12,10,2),(13,11,2),(14,12,3);
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

-- Dump completed on 2025-12-12 14:16:38
