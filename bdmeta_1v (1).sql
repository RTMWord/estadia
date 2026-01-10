CREATE DATABASE IF NOT EXISTS bdmeta1;
use bdmeta1;

-- --------------------------------
-- 1. Tablas de Usuario y Roles
-- --------------------------------
CREATE TABLE IF NOT EXISTS `rol` (
  `idRol` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) NOT NULL,
  `Descripcion` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `usuario` (
  `idUsuario` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(60) NOT NULL,
  `ApellidoP` VARCHAR(60) NOT NULL,
  `ApellidoM` VARCHAR(60) NOT NULL,
  `Email` VARCHAR(100) NOT NULL,
  `PasswordHash` VARCHAR(255) NOT NULL,
  `Telefono` VARCHAR(25) NOT NULL,
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `Activo` TINYINT(1) NOT NULL DEFAULT 1,
  `FechaActualizacion` DATETIME DEFAULT NULL,
  `IntentosFallidos` INT(11) NOT NULL DEFAULT 0,
  `BloqueadoHasta` DATETIME DEFAULT NULL,
  `PasswordResetToken` VARCHAR(64) DEFAULT NULL,
  `PasswordResetExpires` DATETIME DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `PasswordResetToken` (`PasswordResetToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CORRECCIÓN CRÍTICA: Implementación correcta de N:M con Clave Primaria Compuesta.
CREATE TABLE IF NOT EXISTS `usuariorol` (
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Rol_idRol` INT(11) NOT NULL,
  PRIMARY KEY (`Usuario_idUsuario`, `Rol_idRol`),
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Rol_idRol`) REFERENCES `rol` (`idRol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------
-- 2. Tablas de Funcionalidades
-- --------------------------------
CREATE TABLE IF NOT EXISTS `incidencia` (
  `idIncidencia` INT(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT(11) DEFAULT NULL, -- Puede ser anónimo, ON DELETE SET NULL es aceptable aquí.
  `Titulo` VARCHAR(150) NOT NULL,
  `Descripcion` TEXT NOT NULL,
  `Estado` ENUM('ABIERTA','EN_PROGRESO','RESUELTA','CERRADA') DEFAULT 'ABIERTA',
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idIncidencia`),
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla para respuestas a incidencias (comunidad Q/A)
CREATE TABLE IF NOT EXISTS `respuesta_incidencia` (
  `idRespuestaIncidencia` INT(11) NOT NULL AUTO_INCREMENT,
  `Incidencia_idIncidencia` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Cuerpo` TEXT NOT NULL,
  `Aceptada` TINYINT(1) DEFAULT 0,
  `Puntos` INT(11) DEFAULT 0,
  `FechaCreacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `FechaActualizacion` DATETIME DEFAULT NULL,
  PRIMARY KEY (`idRespuestaIncidencia`),
  FOREIGN KEY (`Incidencia_idIncidencia`) REFERENCES `incidencia` (`idIncidencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla de votos para respuestas
CREATE TABLE IF NOT EXISTS `voto_respuesta_incidencia` (
  `idVoto` INT(11) NOT NULL AUTO_INCREMENT,
  `RespuestaIncidencia_idRespuestaIncidencia` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Valor` TINYINT(1) NOT NULL,
  PRIMARY KEY (`idVoto`),
  UNIQUE KEY `uq_respuesta_usuario` (`RespuestaIncidencia_idRespuestaIncidencia`, `Usuario_idUsuario`),
  FOREIGN KEY (`RespuestaIncidencia_idRespuestaIncidencia`) REFERENCES `respuesta_incidencia` (`idRespuestaIncidencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CORRECCIÓN: Sugerencia siempre debe tener un autor. ON DELETE RESTRICT.
CREATE TABLE IF NOT EXISTS `sugerencia` (
  `idSugerencia` INT(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Titulo` VARCHAR(150) NOT NULL,
  `Descripcion` TEXT NOT NULL,
  `Estado` ENUM('PENDIENTE','EN_PROCESO','ATENDIDA','CERRADA') DEFAULT 'PENDIENTE',
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idSugerencia`),
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CORRECCIÓN: Contenido siempre debe tener un autor. ON DELETE RESTRICT.
CREATE TABLE IF NOT EXISTS `contenido` (
  `idContenido` INT(11) NOT NULL AUTO_INCREMENT,
  `Tipo` ENUM('NOTICIA','ARTICULO','BLOG','SITIO_INTERES') NOT NULL,
  `Titulo` VARCHAR(200) NOT NULL,
  `Cuerpo` TEXT NOT NULL,
  `AutorUsuario_id` INT(11) NOT NULL,
  `FechaPublicacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `Activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`idContenido`),
  FOREIGN KEY (`AutorUsuario_id`) REFERENCES `usuario` (`idUsuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `multimedia` (
  `idMedia` INT(11) NOT NULL AUTO_INCREMENT,
  `Contenido_idContenido` INT(11) NOT NULL,
  `Tipo` ENUM('IMAGEN','VIDEO','DOCUMENTO') DEFAULT 'IMAGEN',
  `Ruta` VARCHAR(255) NOT NULL,
  `Descripcion` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idMedia`),
  FOREIGN KEY (`Contenido_idContenido`) REFERENCES `contenido` (`idContenido`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------
-- 3. Tablas de Servicios
-- --------------------------------
CREATE TABLE IF NOT EXISTS `agencia` (
  `idAgencia` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(120) NOT NULL,
  `Contacto` VARCHAR(120) NOT NULL,
  `Telefono` VARCHAR(25) NOT NULL,
  `Email` VARCHAR(100) NOT NULL,
  `Direccion` VARCHAR(255) NOT NULL,
  `EstadoValidacion` ENUM('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE',
  PRIMARY KEY (`idAgencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CORRECCIÓN CRÍTICA: Relación fuerte Agencia-Servicio. ON DELETE RESTRICT.
CREATE TABLE IF NOT EXISTS `servicio` (
  `idServicio` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(120) NOT NULL,
  `Descripcion` TEXT NOT NULL,
  `Costo` DECIMAL(10,2) DEFAULT 0.00,
  `Agencia_idAgencia` INT(11) NOT NULL,
  `Activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`idServicio`),
  FOREIGN KEY (`Agencia_idAgencia`) REFERENCES `agencia` (`idAgencia`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `cita` (
  `idCita` INT(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Servicio_idServicio` INT(11) NOT NULL,
  `FechaHora` DATETIME NOT NULL,
  `Estado` ENUM('PENDIENTE','AGENDADA','CONFIRMADA','CANCELADA','REALIZADA') DEFAULT 'PENDIENTE',
  `Notas` TEXT NOT NULL,
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idCita`),
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Servicio_idServicio`) REFERENCES `servicio` (`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------
-- 4. Tablas de Producto y Pedidos
-- --------------------------------
CREATE TABLE IF NOT EXISTS `producto` (
  `idProducto` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(150) NOT NULL,
  `Descripcion` TEXT NOT NULL,
  `Precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `Existencia` INT(11) NOT NULL DEFAULT 0,
  `Activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`idProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `pedido` (
  `idPedido` INT(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `FechaPedido` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `Total` DECIMAL(10,2) NOT NULL,
  `Estado` ENUM('PENDIENTE','PAGADO','ENVIADO','ENTREGADO','CANCELADO') NOT NULL DEFAULT 'PENDIENTE',
  PRIMARY KEY (`idPedido`),
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `detallepedido` (
  `idDetallePedido` INT(11) NOT NULL AUTO_INCREMENT,
  `Pedido_idPedido` INT(11) NOT NULL,
  `Producto_idProducto` INT(11) NOT NULL,
  `Cantidad` INT(11) NOT NULL,
  `PrecioUnitario` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`idDetallePedido`),
  UNIQUE KEY `uq_pedido_producto` (`Pedido_idPedido`, `Producto_idProducto`),
  FOREIGN KEY (`Pedido_idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Producto_idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------
-- 5. Tablas de Auditoría y Complementos
-- --------------------------------

-- CORRECCIÓN CRÍTICA: Usuario es NOT NULL y ON DELETE RESTRICT para asegurar trazabilidad.
CREATE TABLE IF NOT EXISTS `auditoria_pedido` (
  `idAuditoriaPedido` INT(11) NOT NULL AUTO_INCREMENT,
  `Pedido_idPedido` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `CampoAfectado` VARCHAR(50) NOT NULL,
  `ValorAnterior` VARCHAR(255) DEFAULT NULL,
  `ValorNuevo` VARCHAR(255) DEFAULT NULL,
  `FechaCambio` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `Accion` ENUM('INSERCION', 'MODIFICACION', 'ELIMINACION') NOT NULL,
  PRIMARY KEY (`idAuditoriaPedido`),
  FOREIGN KEY (`Pedido_idPedido`) REFERENCES `pedido` (`idPedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CORRECCIÓN CRÍTICA: Usuario es NOT NULL y ON DELETE RESTRICT para asegurar trazabilidad.
CREATE TABLE IF NOT EXISTS `auditoria_producto` (
  `idAuditoriaProducto` INT(11) NOT NULL AUTO_INCREMENT,
  `Producto_idProducto` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `CampoAfectado` VARCHAR(50) NOT NULL,
  `ValorAnterior` VARCHAR(255) DEFAULT NULL,
  `ValorNuevo` VARCHAR(255) DEFAULT NULL,
  `FechaCambio` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `Accion` ENUM('INSERCION', 'MODIFICACION', 'ELIMINACION') NOT NULL,
  PRIMARY KEY (`idAuditoriaProducto`),
  FOREIGN KEY (`Producto_idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `notificacion` (
  `idNotificacion` INT(11) NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Titulo` VARCHAR(150) NOT NULL,
  `Mensaje` TEXT NOT NULL,
  `Leida` TINYINT(1) DEFAULT 0,
  `FechaEnvio` DATETIME DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idNotificacion`),
  FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `testimonio` (
  `idTestimonio` INT(11) NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(150) NOT NULL,
  `Email` VARCHAR(150) NOT NULL,
  `Calificacion` TINYINT(4) NOT NULL DEFAULT 5,
  `Testimonio` TEXT NOT NULL,
  `Aprobado` TINYINT(1) DEFAULT 0,
  `FechaCreacion` DATETIME DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`idTestimonio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `carousel_testimonios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `multimedia_id` INT(11) NOT NULL,
  `orden` INT(11) DEFAULT 0,
  `activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_multimedia` (`multimedia_id`),
  FOREIGN KEY (`multimedia_id`) REFERENCES `multimedia` (`idMedia`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;