-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-03-2026 a las 21:33:08
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
    SELECT p.Nombre AS Producto,
           SUM(d.Cantidad) AS TotalVendido,
           SUM(d.Cantidad * d.PrecioUnitario) AS Ingresos
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
-- Estructura de tabla para la tabla `agencia`
--

CREATE TABLE `agencia` (
  `idAgencia` int(11) NOT NULL,
  `Nombre` varchar(120) NOT NULL,
  `Contacto` varchar(120) NOT NULL,
  `Telefono` varchar(25) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Direccion` varchar(255) NOT NULL,
  `EstadoValidacion` enum('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `agencia`
--

INSERT INTO `agencia` (`idAgencia`, `Nombre`, `Contacto`, `Telefono`, `Email`, `Direccion`, `EstadoValidacion`) VALUES
(1, 'Agencia de enfermeras', 'Juan Sanchez Godoy', '7772319257', '17250902@uagro.mx', 'aguilas 18 manzana 1 Cond tarianes', 'APROBADA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carousel_testimonios`
--

CREATE TABLE `carousel_testimonios` (
  `id` int(11) NOT NULL,
  `multimedia_id` int(11) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carousel_testimonios`
--

INSERT INTO `carousel_testimonios` (`id`, `multimedia_id`, `orden`, `activo`) VALUES
(1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cita`
--

CREATE TABLE `cita` (
  `idCita` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Servicio_idServicio` int(11) NOT NULL,
  `FechaHora` datetime NOT NULL,
  `Estado` enum('PENDIENTE','AGENDADA','CONFIRMADA','CANCELADA','REALIZADA') DEFAULT 'PENDIENTE',
  `Notas` text NOT NULL,
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cita`
--

INSERT INTO `cita` (`idCita`, `Usuario_idUsuario`, `Servicio_idServicio`, `FechaHora`, `Estado`, `Notas`, `FechaRegistro`) VALUES
(5, 1, 1, '2025-12-21 10:00:00', 'CONFIRMADA', 'Se prevee el uso de guantes para entrar a la casa', '2025-12-12 22:37:01'),
(6, 1, 2, '2025-12-28 08:00:00', 'AGENDADA', 'Se requiere apoyo para cuidar a una persona mayor que tiene problemas para caminar y baja visibilidad ', '2025-12-12 22:55:23'),
(7, 8, 2, '2025-12-12 23:13:00', 'AGENDADA', 'dsad', '2025-12-12 22:58:44'),
(8, 8, 1, '2025-12-12 23:17:00', 'AGENDADA', '', '2025-12-12 23:02:10'),
(9, 8, 2, '2026-01-03 00:03:00', 'AGENDADA', '', '2025-12-12 23:49:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

CREATE TABLE `contenido` (
  `idContenido` int(11) NOT NULL,
  `Tipo` enum('NOTICIA','ARTICULO','BLOG','SITIO_INTERES') NOT NULL,
  `Titulo` varchar(200) NOT NULL,
  `Cuerpo` text NOT NULL,
  `AutorUsuario_id` int(11) DEFAULT NULL,
  `FechaPublicacion` datetime NOT NULL DEFAULT current_timestamp(),
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido`
--

INSERT INTO `contenido` (`idContenido`, `Tipo`, `Titulo`, `Cuerpo`, `AutorUsuario_id`, `FechaPublicacion`, `Activo`) VALUES
(1, 'ARTICULO', 'Informaci?n que cura', 'Este es un mensaje de prueba para que podamos observar la insuficiencia de este sistema', NULL, '2025-11-24 17:46:10', 1),
(2, 'SITIO_INTERES', 'El pepe', 'Hola', NULL, '2025-12-10 21:07:27', 1),
(3, 'ARTICULO', 'Juan', 'dsdada', NULL, '2025-12-10 21:08:01', 1),
(4, 'BLOG', 'bloc', 'bloc de txt', NULL, '2025-12-10 21:08:28', 1),
(5, 'NOTICIA', 'Todos odian a pepe', 'pepe', NULL, '2025-12-10 21:26:25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia`
--

CREATE TABLE `incidencia` (
  `idIncidencia` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) DEFAULT NULL,
  `Titulo` varchar(150) NOT NULL,
  `Descripcion` text NOT NULL,
  `Estado` enum('ABIERTA','EN_PROGRESO','RESUELTA','CERRADA') DEFAULT 'ABIERTA',
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `incidencia`
--

INSERT INTO `incidencia` (`idIncidencia`, `Usuario_idUsuario`, `Titulo`, `Descripcion`, `Estado`, `FechaRegistro`) VALUES
(1, 1, '¿Cómo cuido más a mi adulto mayor?', 'Quiero cuidar más a mi mamá etc, etc, etc.', 'RESUELTA', '2026-01-09 21:32:17'),
(2, 8, 'El pepe', 'hopladadsada', 'ABIERTA', '2026-01-09 21:48:31'),
(3, 8, 'dsdssdsdsdds', 'https://psicologiaymente.com/inteligencia/tipos-falacias-logicas-argumentativas', 'ABIERTA', '2026-01-12 21:54:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `multimedia`
--

CREATE TABLE `multimedia` (
  `idMedia` int(11) NOT NULL,
  `Contenido_idContenido` int(11) DEFAULT NULL,
  `Tipo` enum('IMAGEN','VIDEO','DOCUMENTO') DEFAULT 'IMAGEN',
  `Ruta` varchar(255) NOT NULL,
  `Descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `multimedia`
--

INSERT INTO `multimedia` (`idMedia`, `Contenido_idContenido`, `Tipo`, `Ruta`, `Descripcion`) VALUES
(1, NULL, 'IMAGEN', 'assets/media/media_1763523364_9433.jpeg', 'ejemplo clave');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `idNotificacion` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Titulo` varchar(150) NOT NULL,
  `Mensaje` text NOT NULL,
  `Leida` tinyint(1) DEFAULT 0,
  `FechaEnvio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificacion`
--

INSERT INTO `notificacion` (`idNotificacion`, `Usuario_idUsuario`, `Titulo`, `Mensaje`, `Leida`, `FechaEnvio`) VALUES
(1, 8, 'Respuesta a tu sugerencia: Me encanta este sitio', 'Gracias, eres una gran persona jeje :D', 0, '2025-12-12 22:42:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(10) UNSIGNED NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Existencia` int(11) NOT NULL DEFAULT 0,
  `Activo` tinyint(1) NOT NULL DEFAULT 1,
  `RutaImagen` varchar(512) DEFAULT NULL,
  `FechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `Nombre`, `Descripcion`, `Precio`, `Existencia`, `Activo`, `RutaImagen`, `FechaCreacion`, `FechaActualizacion`) VALUES
(1, 'sds', 'sds', 23123.00, 123, 1, 'assets/media/products/1768193699_logo_white.png', '2026-01-11 22:47:53', '2026-01-11 22:54:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_incidencia`
--

CREATE TABLE `respuesta_incidencia` (
  `idRespuestaIncidencia` int(11) NOT NULL,
  `Incidencia_idIncidencia` int(11) NOT NULL,
  `Usuario_idUsuario` int(11) NOT NULL,
  `Cuerpo` text NOT NULL,
  `Aceptada` tinyint(1) DEFAULT 0,
  `Puntos` int(11) DEFAULT 0,
  `FechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta_incidencia`
--

INSERT INTO `respuesta_incidencia` (`idRespuestaIncidencia`, `Incidencia_idIncidencia`, `Usuario_idUsuario`, `Cuerpo`, `Aceptada`, `Puntos`, `FechaCreacion`, `FechaActualizacion`) VALUES
(1, 1, 8, 'Claro, puedes hacer esto esto y aquello', 0, -1, '2026-01-09 21:37:02', NULL),
(2, 1, 10, 'Yo opino que me caes mal', 0, 0, '2026-01-09 21:40:23', NULL),
(3, 2, 10, 'hola', 0, 0, '2026-01-11 21:54:43', NULL),
(4, 2, 10, 'ss', 0, 0, '2026-01-11 21:56:40', NULL),
(5, 2, 10, 'sdsds', 0, 0, '2026-01-11 21:58:31', NULL),
(6, 2, 10, 'ggggg', 0, 0, '2026-01-11 21:59:20', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idRol` int(11) NOT NULL,
  `Nombre` varchar(45) NOT NULL,
  `Descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idRol`, `Nombre`, `Descripcion`) VALUES
(1, 'administrador', 'Administrador del sistema'),
(2, 'Usuario', 'Ciudadano / Familiar / Adulto mayor'),
(4, 'proveedor', 'Representante de agencia que propone contenido y servicios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `idServicio` int(11) NOT NULL,
  `Nombre` varchar(120) NOT NULL,
  `Descripcion` text NOT NULL,
  `Costo` decimal(10,2) DEFAULT 0.00,
  `Agencia_idAgencia` int(11) DEFAULT NULL,
  `Imagen` varchar(512) DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1,
  `FechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `FechaActualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`idServicio`, `Nombre`, `Descripcion`, `Costo`, `Agencia_idAgencia`, `Imagen`, `Activo`, `FechaCreacion`, `FechaActualizacion`) VALUES
(1, 'sds', 'dsds', 11111.00, 1, NULL, 0, '2026-01-11 22:20:46', NULL),
(2, 'Cuidado de persona mayor', 'Se cuida a persona mayor a domicilio, donde ira una enfermera a apoyar en las actividades diarias de la persona mayor', 5000.00, 1, NULL, 0, '2026-01-11 22:20:46', '2026-01-11 23:01:26'),
(3, 'El pepe', 'sss', 3213.00, 1, NULL, 0, '2026-01-11 22:22:36', '2026-01-11 23:01:14'),
(4, 'dsds', 'dsdsd', 13232.00, 1, 'srv_6964781b5c242.jpg', 0, '2026-01-11 22:27:07', '2026-01-11 23:01:22'),
(5, 'sds', 'wqq', 123.00, 1, 'srv_69647e6c5670e.jpg', 0, '2026-01-11 22:54:04', '2026-01-11 23:08:11'),
(6, 'sd', 'sds', 123.00, 1, 'srv_696480348f7c1.jpg', 1, '2026-01-11 23:01:40', NULL),
(7, 'sadasd', 'dsadadsa', 1231.00, 1, 'srv_6964810c2a167.jpg', 0, '2026-01-11 23:05:16', '2026-03-03 13:55:12'),
(8, 'asda', 'dsada', 1321.00, 1, 'srv_696481c586c89.jpg', 1, '2026-01-11 23:08:21', NULL),
(9, 'Servicio 1', 'El servicio consiste en...', 1200.00, 1, NULL, 0, '2026-03-03 13:52:59', '2026-03-03 13:55:25'),
(10, 'Propuesta de Servicio 1', 'se propone...', 2000.00, 1, NULL, 0, '2026-03-03 14:05:02', '2026-03-03 14:21:15');

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

--
-- Volcado de datos para la tabla `sugerencia`
--

INSERT INTO `sugerencia` (`idSugerencia`, `Usuario_idUsuario`, `Titulo`, `Descripcion`, `Estado`, `FechaRegistro`) VALUES
(1, 8, 'Me encanta este sitio', 'Esta bonito el sitio, no es sugerencia pero es una felicitacion jeje', 'ATENDIDA', '2025-12-12 22:41:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testimonio`
--

CREATE TABLE `testimonio` (
  `idTestimonio` int(11) NOT NULL,
  `Nombre` varchar(150) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Calificacion` tinyint(4) NOT NULL DEFAULT 5,
  `Testimonio` text NOT NULL,
  `Aprobado` tinyint(1) DEFAULT 0,
  `FechaCreacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `testimonio`
--

INSERT INTO `testimonio` (`idTestimonio`, `Nombre`, `Email`, `Calificacion`, `Testimonio`, `Aprobado`, `FechaCreacion`) VALUES
(1, 'Alexis Reyes Ocampo', '', 1, 'Este es un ejemplo de c?mo se ver?a el sitio de \"Testimonios\"', 1, '2025-11-13 23:13:20'),
(2, 'Juanito Perez Corral.es', '', 1, 'No venden pizza, p?simo servicio.', 1, '2025-11-18 21:37:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
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
  `PasswordResetExpires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `Nombre`, `ApellidoP`, `ApellidoM`, `Email`, `PasswordHash`, `Telefono`, `FechaRegistro`, `Activo`, `Tipo`, `FechaActualizacion`, `IntentosFallidos`, `BloqueadoHasta`, `PasswordResetToken`, `PasswordResetExpires`) VALUES
(1, 'Admin', 'Meta', 'Hogar', 'admin@example.com', '$2y$10$LhX65waxtcCTcX8/itF51Orv97X4bSvfukhHok4LH64LS4wnDiP6.', '', '2025-11-03 17:37:57', 1, 'interno', '2026-03-03 14:18:53', 0, NULL, NULL, NULL),
(2, 'Alexis', 'Reyes', 'Ocampo', 'alexis.re.ye.es.14@gmail.com', '$2y$10$MTOSETstvfCcfTTWWXGhFefApmPHw7pz2O4aVh7sEk/x2zcFAxlFy', '7772319257', '2025-11-06 23:00:28', 1, 'externo', '2025-11-14 20:08:57', 0, NULL, '587f5d201bf227c1aec0164e8f71db9a785cb102bce4a196b5930862c7ae05b5', '2025-11-15 04:08:57'),
(3, 'Alexis', 'Reyes', 'Ocampo', 'reysalexis3@gmail.com', '$2y$10$qK0jg0SCiKc0UohBcMaOU.aKKzNdTAfqXICjiZQ5TqUec5cbAZCsy', '7772319257', '2025-11-08 02:25:40', 1, 'externo', NULL, 0, NULL, NULL, NULL),
(5, 'Alexis', 'Reyes', 'Ocampo', 'rklcolek@outlook.com', '$2y$10$ge.eLz.Eo9XwejFwp65aHeDLoCXPY6hc4AE/f.7pECHrcQAgTuroO', '7772319257', '2025-11-13 23:37:42', 0, 'interno', '2025-11-13 23:43:09', 1, NULL, NULL, NULL),
(6, 'Axel', 'Rebollar', 'Nava', 'rnao220946@upemor.edu.mx', '$2y$10$hEbvqLM3.b.abW3HiZre0.ShAJcSiY8PAOtvSrJvG2fVg4gHIMjOW', '7353252584', '2025-11-25 20:05:05', 0, 'externo', '2025-11-25 20:05:33', 0, NULL, NULL, NULL),
(8, 'Hector', 'Hernandez', 'Velazquez', 'hectorhdzvelz@gmail.com', '$2y$10$Jd3DERun6IgV/vq0NQaGP.OwMHcmKNLW1EoBtJmz5ynKIqOIxmY6C', '7774528561', '2025-12-10 23:03:41', 1, 'Usuario', '2026-01-12 21:53:31', 0, NULL, NULL, NULL),
(9, 'Israel Ulises', 'Rodriguez', 'Contreras', 'isra@gmail.com', '$2y$10$5w8rKbZ13wbkmDpQT8vivuI7e9YUyIV82ZxGOi2c9875MAZhAKxq6', '77712345879', '2025-12-12 22:33:42', 0, 'externo', '2025-12-12 22:34:52', 0, NULL, NULL, NULL),
(10, 'Pedro', 'Ramirez', 'de la Cruz', 'pepe@gmail.com', '$2y$10$O4s6YSqMLrf2CirptBQqAuLNtjUOSQL7NYz9smntIe3Ccl3nwMFe.', '7771415560', '2026-01-09 21:39:56', 1, 'Usuario', '2026-01-11 21:56:31', 0, NULL, NULL, NULL),
(11, 'Elienai', 'Montor', 'Paredes', 'elienaimontorp@gmail.com', '$2y$10$VtLHOWbNu3Zqyxl4hdQKheH7d3RIOAMczt6eW46S0I5GB6DXy7Olu', '123456', '2026-02-09 10:24:55', 0, 'interno', '2026-02-09 10:26:28', 0, NULL, NULL, NULL),
(13, 'Hector', 'Rodriguez', 'Hernandez', 'hola@gmail.com', '$2y$10$tU81yvTQsbqUY6IbRArKIOzviTt5jwMrKXNheRvTO8L/iq3OhDv6C', '7774528561', '2026-03-03 13:47:26', 1, 'externo', '2026-03-03 14:16:37', 0, NULL, NULL, NULL);

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
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(5, 5, 2),
(6, 6, 1),
(8, 8, 2),
(9, 9, 2),
(10, 10, 2),
(11, 11, 2),
(12, 13, 4);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agencia`
--
ALTER TABLE `agencia`
  ADD PRIMARY KEY (`idAgencia`);

--
-- Indices de la tabla `carousel_testimonios`
--
ALTER TABLE `carousel_testimonios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `multimedia_id` (`multimedia_id`);

--
-- Indices de la tabla `cita`
--
ALTER TABLE `cita`
  ADD PRIMARY KEY (`idCita`),
  ADD KEY `Usuario_idUsuario` (`Usuario_idUsuario`,`FechaHora`),
  ADD KEY `Estado` (`Estado`),
  ADD KEY `fk_Cita_Servicio` (`Servicio_idServicio`);

--
-- Indices de la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD PRIMARY KEY (`idContenido`),
  ADD KEY `Tipo` (`Tipo`,`FechaPublicacion`),
  ADD KEY `AutorUsuario_id` (`AutorUsuario_id`);

--
-- Indices de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD PRIMARY KEY (`idIncidencia`),
  ADD KEY `Usuario_idUsuario` (`Usuario_idUsuario`);

--
-- Indices de la tabla `multimedia`
--
ALTER TABLE `multimedia`
  ADD PRIMARY KEY (`idMedia`),
  ADD KEY `Contenido_idContenido` (`Contenido_idContenido`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`idNotificacion`),
  ADD KEY `Usuario_idUsuario` (`Usuario_idUsuario`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`),
  ADD KEY `idx_nombre` (`Nombre`),
  ADD KEY `idx_activo` (`Activo`);

--
-- Indices de la tabla `respuesta_incidencia`
--
ALTER TABLE `respuesta_incidencia`
  ADD PRIMARY KEY (`idRespuestaIncidencia`),
  ADD KEY `idx_incidencia` (`Incidencia_idIncidencia`),
  ADD KEY `Usuario_idUsuario` (`Usuario_idUsuario`);

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
  ADD KEY `Agencia_idAgencia` (`Agencia_idAgencia`);

--
-- Indices de la tabla `sugerencia`
--
ALTER TABLE `sugerencia`
  ADD PRIMARY KEY (`idSugerencia`),
  ADD KEY `Usuario_idUsuario` (`Usuario_idUsuario`);

--
-- Indices de la tabla `testimonio`
--
ALTER TABLE `testimonio`
  ADD PRIMARY KEY (`idTestimonio`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `PasswordResetToken` (`PasswordResetToken`);

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
-- AUTO_INCREMENT de la tabla `agencia`
--
ALTER TABLE `agencia`
  MODIFY `idAgencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `carousel_testimonios`
--
ALTER TABLE `carousel_testimonios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cita`
--
ALTER TABLE `cita`
  MODIFY `idCita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `contenido`
--
ALTER TABLE `contenido`
  MODIFY `idContenido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  MODIFY `idIncidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `multimedia`
--
ALTER TABLE `multimedia`
  MODIFY `idMedia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `idNotificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `respuesta_incidencia`
--
ALTER TABLE `respuesta_incidencia`
  MODIFY `idRespuestaIncidencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `idServicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `sugerencia`
--
ALTER TABLE `sugerencia`
  MODIFY `idSugerencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `testimonio`
--
ALTER TABLE `testimonio`
  MODIFY `idTestimonio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuariorol`
--
ALTER TABLE `usuariorol`
  MODIFY `idUsuarioRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

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
-- Filtros para la tabla `respuesta_incidencia`
--
ALTER TABLE `respuesta_incidencia`
  ADD CONSTRAINT `respuesta_incidencia_ibfk_1` FOREIGN KEY (`Incidencia_idIncidencia`) REFERENCES `incidencia` (`idIncidencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `respuesta_incidencia_ibfk_2` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
