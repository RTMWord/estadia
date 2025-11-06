-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-11-2025 a las 03:26:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `metahogar`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiarEstadoCita` (IN `pCita` INT, IN `pEstado` VARCHAR(20))   BEGIN
    UPDATE Cita SET Estado = pEstado WHERE idCita = pCita;
    SELECT CONCAT('Cita ', pCita, ' ahora est� en estado: ', pEstado) AS Mensaje;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporteCitasPorUsuario` (IN `pUsuario` INT)   BEGIN
    SELECT c.idCita, s.Nombre AS Servicio, c.FechaHora, c.Estado
    FROM Cita c
    INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio
    WHERE c.Usuario_idUsuario = pUsuario
    ORDER BY c.FechaHora DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporteIncidenciasAbiertas` ()   BEGIN
    SELECT i.idIncidencia, i.Titulo, i.Descripcion, i.FechaRegistro, u.Nombre
    FROM Incidencia i
    LEFT JOIN Usuario u ON i.Usuario_idUsuario = u.idUsuario
    WHERE i.Estado = 'ABIERTA'
    ORDER BY i.FechaRegistro ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporteNotificacionesPendientes` (IN `pUsuario` INT)   BEGIN
    SELECT idNotificacion, Titulo, Mensaje, FechaEnvio
    FROM Notificacion
    WHERE Usuario_idUsuario = pUsuario AND Leida = 0
    ORDER BY FechaEnvio DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporteSugerenciasPorEstado` (IN `pEstado` VARCHAR(20))   BEGIN
    SELECT s.idSugerencia, s.Titulo, s.Estado, s.FechaRegistro, u.Nombre AS Usuario
    FROM Sugerencia s
    LEFT JOIN Usuario u ON s.Usuario_idUsuario = u.idUsuario
    WHERE s.Estado = pEstado
    ORDER BY s.FechaRegistro DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporteVentasPorProducto` ()   BEGIN
    SELECT p.Nombre AS Producto, SUM(d.Cantidad) AS TotalVendido, SUM(d.Cantidad * d.PrecioUnitario) AS Ingresos
    FROM DetallePedido d
    INNER JOIN Producto p ON d.Producto_idProducto = p.idProducto
    INNER JOIN Pedido pe ON d.Pedido_idPedido = pe.idPedido
    WHERE pe.Estado IN ('PAGADO','ENVIADO','ENTREGADO')
    GROUP BY p.idProducto
    ORDER BY Ingresos DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_validarAgencia` (IN `pAgencia` INT, IN `pEstado` VARCHAR(20))   BEGIN
    UPDATE Agencia SET EstadoValidacion = pEstado WHERE idAgencia = pAgencia;
    SELECT CONCAT('Agencia ', pAgencia, ' actualizada a estado: ', pEstado) AS Mensaje;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesibilidad`
--

CREATE TABLE `accesibilidad` (
  `idAccesibilidad` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `TamLetra` enum('NORMAL','MEDIANA','GRANDE') DEFAULT 'NORMAL',
  `ModoContraste` enum('NORMAL','ALTO') DEFAULT 'NORMAL',
  `LecturaVoz` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agencia`
--

CREATE TABLE `agencia` (
  `idAgencia` int(11) NOT NULL,
  `Nombre` varchar(120) NOT NULL,
  `Contacto` varchar(120) DEFAULT NULL,
  `Telefono` varchar(25) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Direccion` varchar(255) DEFAULT NULL,
  `EstadoValidacion` enum('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoriapedido`
--

CREATE TABLE `auditoriapedido` (
  `idAudit` int(11) NOT NULL,
  `idPedido` int(11) DEFAULT NULL,
  `Usuario_id` int(11) DEFAULT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `Accion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoriaproducto`
--

CREATE TABLE `auditoriaproducto` (
  `idAudit` int(11) NOT NULL,
  `idProducto` int(11) DEFAULT NULL,
  `Cambio` int(11) DEFAULT NULL,
  `FechaCambio` datetime DEFAULT current_timestamp(),
  `Usuario` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita`
--

CREATE TABLE `cita` (
  `idCita` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Servicio_idServicio` int(11) NOT NULL,
  `FechaHora` datetime NOT NULL,
  `Estado` enum('AGENDADA','CONFIRMADA','CANCELADA','REALIZADA') DEFAULT 'AGENDADA',
  `Notas` text DEFAULT NULL,
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

CREATE TABLE `contenido` (
  `idContenido` int(11) NOT NULL,
  `Tipo` enum('NOTICIA','ARTICULO','BLOG','SITIO_INTERES') NOT NULL,
  `Titulo` varchar(200) NOT NULL,
  `Cuerpo` text DEFAULT NULL,
  `AutorUsuario_id` int(11) DEFAULT NULL,
  `FechaPublicacion` datetime DEFAULT current_timestamp(),
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallepedido`
--

CREATE TABLE `detallepedido` (
  `idDetalle` int(11) NOT NULL,
  `Pedido_idPedido` int(11) NOT NULL,
  `Producto_idProducto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL DEFAULT 1,
  `PrecioUnitario` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia`
--

CREATE TABLE `incidencia` (
  `idIncidencia` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) DEFAULT NULL,
  `Titulo` varchar(150) DEFAULT NULL,
  `Descripcion` text DEFAULT NULL,
  `Estado` enum('ABIERTA','EN_PROGRESO','RESUELTA','CERRADA') DEFAULT 'ABIERTA',
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `multimedia`
--

CREATE TABLE `multimedia` (
  `idMedia` int(11) NOT NULL,
  `Contenido_idContenido` int(11) DEFAULT NULL,
  `Tipo` enum('IMAGEN','VIDEO','DOCUMENTO') DEFAULT 'IMAGEN',
  `Ruta` varchar(255) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `idNotificacion` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Titulo` varchar(150) DEFAULT NULL,
  `Mensaje` text DEFAULT NULL,
  `Leida` tinyint(1) DEFAULT 0,
  `FechaEnvio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `idPedido` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `FechaPedido` datetime NOT NULL DEFAULT current_timestamp(),
  `Total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `Estado` enum('PENDIENTE','PAGADO','ENVIADO','CANCELADO','ENTREGADO') DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `pedido`
--
DELIMITER $$
CREATE TRIGGER `trg_pedido_insert` AFTER INSERT ON `pedido` FOR EACH ROW BEGIN
    INSERT INTO AuditoriaPedido(idPedido, Usuario_id, Accion)
    VALUES(NEW.idPedido, NEW.Usuario_idUsuario, 'CREACION');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL,
  `Nombre` varchar(150) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Existencia` int(11) NOT NULL DEFAULT 0,
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `trg_producto_update` AFTER UPDATE ON `producto` FOR EACH ROW BEGIN
    DECLARE vUsuario VARCHAR(100);
    SELECT USER() INTO vUsuario;
    IF (NEW.Existencia <> OLD.Existencia) THEN
        INSERT INTO AuditoriaProducto(idProducto, Cambio, Usuario)
        VALUES(NEW.idProducto, NEW.Existencia - OLD.Existencia, vUsuario);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idRol` int(11) NOT NULL,
  `Nombre` varchar(45) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idRol`, `Nombre`, `Descripcion`) VALUES
(1, 'administrador', 'Administrador del sistema');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idServicio` int(11) NOT NULL,
  `Nombre` varchar(120) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Costo` decimal(10,2) DEFAULT 0.00,
  `Agencia_idAgencia` int(11) DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencia`
--

CREATE TABLE `sugerencia` (
  `idSugerencia` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) DEFAULT NULL,
  `Titulo` varchar(150) NOT NULL,
  `Descripcion` text NOT NULL,
  `Estado` enum('PENDIENTE','EN_PROCESO','ATENDIDA','CERRADA') DEFAULT 'PENDIENTE',
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
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
  `BloqueadoHasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `Nombre`, `ApellidoP`, `ApellidoM`, `Email`, `PasswordHash`, `Telefono`, `FechaRegistro`, `Activo`, `Tipo`, `FechaActualizacion`, `IntentosFallidos`, `BloqueadoHasta`) VALUES
(1, 'Admin', 'Meta', 'Hogar', 'admin@example.com', '$2y$10$LhX65waxtcCTcX8/itF51Orv97X4bSvfukhHok4LH64LS4wnDiP6.', '', '2025-11-03 17:37:57', 1, 'interno', '2025-11-03 21:47:12', 0, NULL);

--
-- Disparadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `trg_usuario_update` BEFORE UPDATE ON `usuario` FOR EACH ROW BEGIN
    SET NEW.FechaActualizacion = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariorol`
--

CREATE TABLE `usuariorol` (
  `idUsuarioRol` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Rol_idRol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuariorol`
--

INSERT INTO `usuariorol` (`idUsuarioRol`, `Usuario_idUsuario`, `Rol_idRol`) VALUES
(1, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesibilidad`
--
ALTER TABLE `accesibilidad`
  ADD PRIMARY KEY (`idAccesibilidad`),
  ADD KEY `fk_Accesibilidad_Usuario` (`Usuario_idUsuario`);

--
-- Indices de la tabla `agencia`
--
ALTER TABLE `agencia`
  ADD PRIMARY KEY (`idAgencia`);

--
-- Indices de la tabla `auditoriapedido`
--
ALTER TABLE `auditoriapedido`
  ADD PRIMARY KEY (`idAudit`);

--
-- Indices de la tabla `auditoriaproducto`
--
ALTER TABLE `auditoriaproducto`
  ADD PRIMARY KEY (`idAudit`);

--
-- Indices de la tabla `cita`
--
ALTER TABLE `cita`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `fk_Cita_Servicio` (`Servicio_idServicio`),
  ADD KEY `idx_cita_estado` (`Estado`),
  ADD KEY `idx_cita_usuario_fecha` (`Usuario_idUsuario`,`FechaHora`);

--
-- Indices de la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD PRIMARY KEY (`idContenido`),
  ADD KEY `fk_Contenido_Autor` (`AutorUsuario_id`),
  ADD KEY `idx_contenido_tipo_fecha` (`Tipo`,`FechaPublicacion`);

--
-- Indices de la tabla `detallepedido`
--
ALTER TABLE `detallepedido`
  ADD PRIMARY KEY (`idDetalle`),
  ADD KEY `fk_Detalle_Pedido` (`Pedido_idPedido`),
  ADD KEY `fk_Detalle_Producto` (`Producto_idProducto`);

--
-- Indices de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD PRIMARY KEY (`idIncidencia`),
  ADD KEY `fk_Incidencia_Usuario` (`Usuario_idUsuario`);

--
-- Indices de la tabla `multimedia`
--
ALTER TABLE `multimedia`
  ADD PRIMARY KEY (`idMedia`),
  ADD KEY `fk_Multimedia_Contenido` (`Contenido_idContenido`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`idNotificacion`),
  ADD KEY `fk_Notificacion_Usuario` (`Usuario_idUsuario`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`idPedido`),
  ADD KEY `idx_pedido_usuario_fecha` (`Usuario_idUsuario`,`FechaPedido`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`idServicio`),
  ADD KEY `idx_servicio_agencia` (`Agencia_idAgencia`);

--
-- Indices de la tabla `sugerencia`
--
ALTER TABLE `sugerencia`
  ADD PRIMARY KEY (`idSugerencia`),
  ADD KEY `fk_Sugerencia_Usuario` (`Usuario_idUsuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `idx_usuario_email` (`Email`);

--
-- Indices de la tabla `usuariorol`
--
ALTER TABLE `usuariorol`
  ADD PRIMARY KEY (`idUsuarioRol`),
  ADD KEY `fk_UsuarioRol_Usuario` (`Usuario_idUsuario`),
  ADD KEY `fk_UsuarioRol_Rol` (`Rol_idRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesibilidad`
--
ALTER TABLE `accesibilidad`
  MODIFY `idAccesibilidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `agencia`
--
ALTER TABLE `agencia`
  MODIFY `idAgencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoriapedido`
--
ALTER TABLE `auditoriapedido`
  MODIFY `idAudit` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoriaproducto`
--
ALTER TABLE `auditoriaproducto`
  MODIFY `idAudit` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cita`
--
ALTER TABLE `cita`
  MODIFY `idCita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contenido`
--
ALTER TABLE `contenido`
  MODIFY `idContenido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detallepedido`
--
ALTER TABLE `detallepedido`
  MODIFY `idDetalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  MODIFY `idIncidencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `multimedia`
--
ALTER TABLE `multimedia`
  MODIFY `idMedia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sugerencia`
--
ALTER TABLE `sugerencia`
  MODIFY `idSugerencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuariorol`
--
ALTER TABLE `usuariorol`
  MODIFY `idUsuarioRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesibilidad`
--
ALTER TABLE `accesibilidad`
  ADD CONSTRAINT `fk_Accesibilidad_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cita`
--
ALTER TABLE `cita`
  ADD CONSTRAINT `fk_Cita_Servicio` FOREIGN KEY (`Servicio_idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Cita_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD CONSTRAINT `fk_Contenido_Autor` FOREIGN KEY (`AutorUsuario_id`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `detallepedido`
--
ALTER TABLE `detallepedido`
  ADD CONSTRAINT `fk_Detalle_Pedido` FOREIGN KEY (`Pedido_idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Detalle_Producto` FOREIGN KEY (`Producto_idProducto`) REFERENCES `producto` (`idProducto`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD CONSTRAINT `fk_Incidencia_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `multimedia`
--
ALTER TABLE `multimedia`
  ADD CONSTRAINT `fk_Multimedia_Contenido` FOREIGN KEY (`Contenido_idContenido`) REFERENCES `contenido` (`idContenido`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `fk_Notificacion_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_Pedido_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `fk_Servicio_Agencia` FOREIGN KEY (`Agencia_idAgencia`) REFERENCES `agencia` (`idAgencia`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `sugerencia`
--
ALTER TABLE `sugerencia`
  ADD CONSTRAINT `fk_Sugerencia_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuariorol`
--
ALTER TABLE `usuariorol`
  ADD CONSTRAINT `fk_UsuarioRol_Rol` FOREIGN KEY (`Rol_idRol`) REFERENCES `rol` (`idRol`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_UsuarioRol_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Metadatos
--
USE `phpmyadmin`;

--
-- Metadatos para la tabla accesibilidad
--

--
-- Metadatos para la tabla agencia
--

--
-- Metadatos para la tabla auditoriapedido
--

--
-- Metadatos para la tabla auditoriaproducto
--

--
-- Metadatos para la tabla cita
--

--
-- Metadatos para la tabla contenido
--

--
-- Metadatos para la tabla detallepedido
--

--
-- Metadatos para la tabla incidencia
--

--
-- Metadatos para la tabla multimedia
--

--
-- Metadatos para la tabla notificacion
--

--
-- Metadatos para la tabla pedido
--

--
-- Metadatos para la tabla producto
--

--
-- Metadatos para la tabla rol
--

--
-- Metadatos para la tabla servicio
--

--
-- Metadatos para la tabla sugerencia
--

--
-- Metadatos para la tabla usuario
--

--
-- Metadatos para la tabla usuariorol
--

--
-- Metadatos para la base de datos metahogar
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
