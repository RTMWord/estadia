-- Script inicial para MetaHogar (Ayuntamiento Jiutepec)
-- Diseñado para XAMPP / MySQL
-- UTF8MB4 y InnoDB para integridad referencial

DROP DATABASE IF EXISTS `MetaHogar`;
CREATE DATABASE `MetaHogar` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `MetaHogar`;

-- Usuarios de ejemplo (ajusta hosts/contraseñas según tu entorno)
DROP USER IF EXISTS 'alexis'@'localhost';
CREATE USER 'alexis'@'localhost' IDENTIFIED BY '123';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON MetaHogar.* TO 'alexis'@'localhost';

DROP USER IF EXISTS 'hector'@'localhost';
CREATE USER 'hector'@'localhost' IDENTIFIED BY '123';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON MetaHogar.* TO 'hector'@'localhost';

DROP USER IF EXISTS 'readonly'@'localhost';
CREATE USER 'readonly'@'localhost' IDENTIFIED BY 'readonly';
GRANT SELECT ON MetaHogar.* TO 'readonly'@'localhost';


-- =========================
-- Tablas principales
-- =========================

CREATE TABLE IF NOT EXISTS `Rol` (
  `idRol` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) NOT NULL, -- e.g. administrador, publico, prestador
  `Descripcion` VARCHAR(255),
  PRIMARY KEY(`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Usuario` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(60) NOT NULL,
  `ApellidoP` VARCHAR(60),
  `ApellidoM` VARCHAR(60),
  `Email` VARCHAR(100) NOT NULL UNIQUE,
  `PasswordHash` VARCHAR(255) NOT NULL,
  `Telefono` VARCHAR(25),
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Activo` TINYINT(1) NOT NULL DEFAULT 1,
  `Tipo` VARCHAR(45) DEFAULT NULL, -- opcional: 'externo', 'interno'
  PRIMARY KEY(`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `UsuarioRol` (
  `idUsuarioRol` INT NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT NOT NULL,
  `Rol_idRol` INT NOT NULL,
  PRIMARY KEY(`idUsuarioRol`),
  CONSTRAINT `fk_UsuarioRol_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario`(`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_UsuarioRol_Rol` FOREIGN KEY (`Rol_idRol`) REFERENCES `Rol`(`idRol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Agencia` (
  `idAgencia` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(120) NOT NULL,
  `Contacto` VARCHAR(120),
  `Telefono` VARCHAR(25),
  `Email` VARCHAR(100),
  `Direccion` VARCHAR(255),
  `EstadoValidacion` ENUM('PENDIENTE','APROBADA','RECHAZADA') DEFAULT 'PENDIENTE',
  PRIMARY KEY(`idAgencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Servicio` (
  `idServicio` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(120) NOT NULL, -- e.g. enfermería a domicilio
  `Descripcion` TEXT,
  `Costo` DECIMAL(10,2) DEFAULT 0.00,
  `Agencia_idAgencia` INT DEFAULT NULL,
  `Activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY(`idServicio`),
  CONSTRAINT `fk_Servicio_Agencia` FOREIGN KEY (`Agencia_idAgencia`) REFERENCES `Agencia`(`idAgencia`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Cita` (
  `idCita` INT NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT NOT NULL, -- quien agenda
  `Servicio_idServicio` INT NOT NULL,
  `FechaHora` DATETIME NOT NULL,
  `Estado` ENUM('AGENDADA','CONFIRMADA','CANCELADA','REALIZADA') DEFAULT 'AGENDADA',
  `Notas` TEXT,
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`idCita`),
  CONSTRAINT `fk_Cita_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario`(`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Cita_Servicio` FOREIGN KEY (`Servicio_idServicio`) REFERENCES `Servicio`(`idServicio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Sugerencia` (
  `idSugerencia` INT NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT DEFAULT NULL,
  `Titulo` VARCHAR(150) NOT NULL,
  `Descripcion` TEXT NOT NULL,
  `Estado` ENUM('PENDIENTE','EN_PROCESO','ATENDIDA','CERRADA') DEFAULT 'PENDIENTE',
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`idSugerencia`),
  CONSTRAINT `fk_Sugerencia_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario`(`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Producto` (
  `idProducto` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(150) NOT NULL,
  `Descripcion` TEXT,
  `Precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `Existencia` INT NOT NULL DEFAULT 0,
  `Activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY(`idProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Pedido` (
  `idPedido` INT NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT NOT NULL,
  `FechaPedido` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `Estado` ENUM('PENDIENTE','PAGADO','ENVIADO','CANCELADO','ENTREGADO') DEFAULT 'PENDIENTE',
  PRIMARY KEY(`idPedido`),
  CONSTRAINT `fk_Pedido_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario`(`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `DetallePedido` (
  `idDetalle` INT NOT NULL AUTO_INCREMENT,
  `Pedido_idPedido` INT NOT NULL,
  `Producto_idProducto` INT NOT NULL,
  `Cantidad` INT NOT NULL DEFAULT 1,
  `PrecioUnitario` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY(`idDetalle`),
  CONSTRAINT `fk_Detalle_Pedido` FOREIGN KEY (`Pedido_idPedido`) REFERENCES `Pedido`(`idPedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Detalle_Producto` FOREIGN KEY (`Producto_idProducto`) REFERENCES `Producto`(`idProducto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Contenido` (
  `idContenido` INT NOT NULL AUTO_INCREMENT,
  `Tipo` ENUM('NOTICIA','ARTICULO','BLOG','SITIO_INTERES') NOT NULL,
  `Titulo` VARCHAR(200) NOT NULL,
  `Cuerpo` TEXT,
  `AutorUsuario_id` INT DEFAULT NULL,
  `FechaPublicacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `Activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY(`idContenido`),
  CONSTRAINT `fk_Contenido_Autor` FOREIGN KEY (`AutorUsuario_id`) REFERENCES `Usuario`(`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Multimedia` (
  `idMedia` INT NOT NULL AUTO_INCREMENT,
  `Contenido_idContenido` INT DEFAULT NULL,
  `Tipo` ENUM('IMAGEN','VIDEO','DOCUMENTO') DEFAULT 'IMAGEN',
  `Ruta` VARCHAR(255) NOT NULL,
  `Descripcion` VARCHAR(255),
  PRIMARY KEY(`idMedia`),
  CONSTRAINT `fk_Multimedia_Contenido` FOREIGN KEY (`Contenido_idContenido`) REFERENCES `Contenido`(`idContenido`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Incidencia` (
  `idIncidencia` INT NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT DEFAULT NULL,
  `Titulo` VARCHAR(150),
  `Descripcion` TEXT,
  `Estado` ENUM('ABIERTA','EN_PROGRESO','RESUELTA','CERRADA') DEFAULT 'ABIERTA',
  `FechaRegistro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`idIncidencia`),
  CONSTRAINT `fk_Incidencia_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario`(`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Notificacion` (
  `idNotificacion` INT NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT NOT NULL,
  `Titulo` VARCHAR(150),
  `Mensaje` TEXT,
  `Leida` TINYINT(1) DEFAULT 0,
  `FechaEnvio` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`idNotificacion`),
  CONSTRAINT `fk_Notificacion_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario`(`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `Accesibilidad` (
  `idAccesibilidad` INT NOT NULL AUTO_INCREMENT,
  `Usuario_idUsuario` INT NOT NULL,
  `TamLetra` ENUM('NORMAL','MEDIANA','GRANDE') DEFAULT 'NORMAL',
  `ModoContraste` ENUM('NORMAL','ALTO') DEFAULT 'NORMAL',
  `LecturaVoz` TINYINT(1) DEFAULT 0,
  PRIMARY KEY(`idAccesibilidad`),
  CONSTRAINT `fk_Accesibilidad_Usuario` FOREIGN KEY (`Usuario_idUsuario`) REFERENCES `Usuario`(`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =========================
-- Índices recomendados
-- =========================
CREATE INDEX idx_usuario_email ON Usuario(Email);
CREATE INDEX idx_cita_estado ON Cita(Estado);
CREATE INDEX idx_servicio_agencia ON Servicio(Agencia_idAgencia);

-- Índices compuestos (útiles en reportes y consultas frecuentes)
CREATE INDEX idx_cita_usuario_fecha ON Cita(Usuario_idUsuario, FechaHora);
CREATE INDEX idx_pedido_usuario_fecha ON Pedido(Usuario_idUsuario, FechaPedido);
CREATE INDEX idx_contenido_tipo_fecha ON Contenido(Tipo, FechaPublicacion);

-- =========================
-- Procedimientos almacenados (ampliados)
-- =========================

DELIMITER $$
-- Reporte de citas por usuario
CREATE PROCEDURE sp_reporteCitasPorUsuario(IN pUsuario INT)
BEGIN
    SELECT c.idCita, s.Nombre AS Servicio, c.FechaHora, c.Estado
    FROM Cita c
    INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio
    WHERE c.Usuario_idUsuario = pUsuario
    ORDER BY c.FechaHora DESC;
END $$
DELIMITER ;

DELIMITER $$
-- Reporte de sugerencias (por estado)
CREATE PROCEDURE sp_reporteSugerenciasPorEstado(IN pEstado VARCHAR(20))
BEGIN
    SELECT s.idSugerencia, s.Titulo, s.Estado, s.FechaRegistro, u.Nombre AS Usuario
    FROM Sugerencia s
    LEFT JOIN Usuario u ON s.Usuario_idUsuario = u.idUsuario
    WHERE s.Estado = pEstado
    ORDER BY s.FechaRegistro DESC;
END $$
DELIMITER ;

DELIMITER $$
-- Reporte de incidencias abiertas
CREATE PROCEDURE sp_reporteIncidenciasAbiertas()
BEGIN
    SELECT i.idIncidencia, i.Titulo, i.Descripcion, i.FechaRegistro, u.Nombre
    FROM Incidencia i
    LEFT JOIN Usuario u ON i.Usuario_idUsuario = u.idUsuario
    WHERE i.Estado = 'ABIERTA'
    ORDER BY i.FechaRegistro ASC;
END $$
DELIMITER ;

DELIMITER $$
-- Reporte de ventas por producto
CREATE PROCEDURE sp_reporteVentasPorProducto()
BEGIN
    SELECT p.Nombre AS Producto, SUM(d.Cantidad) AS TotalVendido, SUM(d.Cantidad * d.PrecioUnitario) AS Ingresos
    FROM DetallePedido d
    INNER JOIN Producto p ON d.Producto_idProducto = p.idProducto
    INNER JOIN Pedido pe ON d.Pedido_idPedido = pe.idPedido
    WHERE pe.Estado IN ('PAGADO','ENVIADO','ENTREGADO')
    GROUP BY p.idProducto
    ORDER BY Ingresos DESC;
END $$
DELIMITER ;

DELIMITER $$
-- Reporte de notificaciones no leídas
CREATE PROCEDURE sp_reporteNotificacionesPendientes(IN pUsuario INT)
BEGIN
    SELECT idNotificacion, Titulo, Mensaje, FechaEnvio
    FROM Notificacion
    WHERE Usuario_idUsuario = pUsuario AND Leida = 0
    ORDER BY FechaEnvio DESC;
END $$
DELIMITER ;

-- =========================
-- Triggers de auditoría
-- =========================

-- Auditoría: registrar fecha de última actualización en Usuario
ALTER TABLE Usuario ADD COLUMN FechaActualizacion DATETIME NULL;

DELIMITER $$
CREATE TRIGGER trg_usuario_update
BEFORE UPDATE ON Usuario
FOR EACH ROW
BEGIN
    SET NEW.FechaActualizacion = NOW();
END $$
DELIMITER ;

-- Auditoría: registrar movimiento en productos (stock)
CREATE TABLE IF NOT EXISTS AuditoriaProducto (
    idAudit INT AUTO_INCREMENT PRIMARY KEY,
    idProducto INT,
    Cambio INT,
    FechaCambio DATETIME DEFAULT CURRENT_TIMESTAMP,
    Usuario VARCHAR(100)
);

DELIMITER $$
CREATE TRIGGER trg_producto_update
AFTER UPDATE ON Producto
FOR EACH ROW
BEGIN
    DECLARE vUsuario VARCHAR(100);
    SELECT USER() INTO vUsuario;
    IF (NEW.Existencia <> OLD.Existencia) THEN
        INSERT INTO AuditoriaProducto(idProducto, Cambio, Usuario)
        VALUES(NEW.idProducto, NEW.Existencia - OLD.Existencia, vUsuario);
    END IF;
END $$
DELIMITER ;

-- Auditoría: registrar pedidos nuevos
CREATE TABLE IF NOT EXISTS AuditoriaPedido (
    idAudit INT AUTO_INCREMENT PRIMARY KEY,
    idPedido INT,
    Usuario_id INT,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    Accion VARCHAR(50)
);

DELIMITER $$
CREATE TRIGGER trg_pedido_insert
AFTER INSERT ON Pedido
FOR EACH ROW
BEGIN
    INSERT INTO AuditoriaPedido(idPedido, Usuario_id, Accion)
    VALUES(NEW.idPedido, NEW.Usuario_idUsuario, 'CREACION');
END $$
DELIMITER ;

-- =========================
-- Procedimientos de validación
-- =========================

DELIMITER $$
-- Validar agencia de servicios
CREATE PROCEDURE sp_validarAgencia(IN pAgencia INT, IN pEstado VARCHAR(20))
BEGIN
    UPDATE Agencia SET EstadoValidacion = pEstado WHERE idAgencia = pAgencia;
    SELECT CONCAT('Agencia ', pAgencia, ' actualizada a estado: ', pEstado) AS Mensaje;
END $$
DELIMITER ;

DELIMITER $$
-- Cambiar estado de cita
CREATE PROCEDURE sp_cambiarEstadoCita(IN pCita INT, IN pEstado VARCHAR(20))
BEGIN
    UPDATE Cita SET Estado = pEstado WHERE idCita = pCita;
    SELECT CONCAT('Cita ', pCita, ' ahora está en estado: ', pEstado) AS Mensaje;
END $$
DELIMITER ;

-- =========================
-- Fin de ampliación
-- =========================


-- Insertar usuario Alexis con password 'AlReOc75' (hash ejemplo, usa el hash real en producción)
INSERT INTO Usuario (Nombre, ApellidoP, ApellidoM, Email, PasswordHash, Telefono, Tipo)
VALUES ('Alexis', 'Reyes', 'Ocampo', 'reysalexis3@gmail.com', SHA2('AlReOc75', 256), '7772319257', 'externo');