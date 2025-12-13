-- =====================================================
-- Schema para Q&A System (Comunidad) - MetaHogar
-- Reutiliza tabla 'incidencia' existente
-- =====================================================

-- =====================================================
-- 1. Alter table INCIDENCIA para agregar columnas Q&A
-- =====================================================
ALTER TABLE `incidencia` ADD COLUMN `Vistas` INT DEFAULT 0 AFTER `Descripcion`;
ALTER TABLE `incidencia` ADD COLUMN `Puntos` INT DEFAULT 0 AFTER `Vistas`;
ALTER TABLE `incidencia` ADD COLUMN `RespuestaAceptada_idRespuestaIncidencia` INT DEFAULT NULL AFTER `Puntos`;

-- =====================================================
-- 2. Crear tabla RESPUESTA_INCIDENCIA
-- =====================================================
CREATE TABLE IF NOT EXISTS `respuesta_incidencia` (
  `idRespuestaIncidencia` INT(11) NOT NULL AUTO_INCREMENT,
  `Incidencia_idIncidencia` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Cuerpo` LONGTEXT NOT NULL,
  `Puntos` INT(11) DEFAULT 0,
  `Aceptada` TINYINT(1) DEFAULT 0,
  `FechaRegistro` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `FechaActualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRespuestaIncidencia`),
  KEY `FK_respuesta_incidencia_incidencia` (`Incidencia_idIncidencia`),
  KEY `FK_respuesta_incidencia_usuario` (`Usuario_idUsuario`),
  CONSTRAINT `FK_respuesta_incidencia_incidencia` 
    FOREIGN KEY (`Incidencia_idIncidencia`) 
    REFERENCES `incidencia` (`idIncidencia`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_respuesta_incidencia_usuario` 
    FOREIGN KEY (`Usuario_idUsuario`) 
    REFERENCES `usuario` (`idUsuario`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 3. Crear tabla VOTO_INCIDENCIA
-- =====================================================
CREATE TABLE IF NOT EXISTS `voto_incidencia` (
  `Incidencia_idIncidencia` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Valor` TINYINT(2) DEFAULT 1,
  `FechaRegistro` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Incidencia_idIncidencia`, `Usuario_idUsuario`),
  KEY `FK_voto_incidencia_usuario` (`Usuario_idUsuario`),
  CONSTRAINT `FK_voto_incidencia_incidencia` 
    FOREIGN KEY (`Incidencia_idIncidencia`) 
    REFERENCES `incidencia` (`idIncidencia`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_voto_incidencia_usuario` 
    FOREIGN KEY (`Usuario_idUsuario`) 
    REFERENCES `usuario` (`idUsuario`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 4. Crear tabla VOTO_RESPUESTA_INCIDENCIA
-- =====================================================
CREATE TABLE IF NOT EXISTS `voto_respuesta_incidencia` (
  `RespuestaIncidencia_idRespuestaIncidencia` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  `Valor` TINYINT(2) DEFAULT 1,
  `FechaRegistro` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RespuestaIncidencia_idRespuestaIncidencia`, `Usuario_idUsuario`),
  KEY `FK_voto_respuesta_incidencia_usuario` (`Usuario_idUsuario`),
  CONSTRAINT `FK_voto_respuesta_incidencia_respuesta` 
    FOREIGN KEY (`RespuestaIncidencia_idRespuestaIncidencia`) 
    REFERENCES `respuesta_incidencia` (`idRespuestaIncidencia`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_voto_respuesta_incidencia_usuario` 
    FOREIGN KEY (`Usuario_idUsuario`) 
    REFERENCES `usuario` (`idUsuario`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Índices adicionales para mejorar performance
-- =====================================================
CREATE INDEX `IDX_respuesta_incidencia_fecha` ON `respuesta_incidencia` (`FechaRegistro`);
CREATE INDEX `IDX_respuesta_incidencia_puntos` ON `respuesta_incidencia` (`Puntos`);
CREATE INDEX `IDX_respuesta_incidencia_aceptada` ON `respuesta_incidencia` (`Aceptada`);
CREATE INDEX `IDX_incidencia_estado` ON `incidencia` (`Estado`);
CREATE INDEX `IDX_incidencia_fecha` ON `incidencia` (`FechaRegistro`);
CREATE INDEX `IDX_incidencia_puntos` ON `incidencia` (`Puntos`);
CREATE INDEX `IDX_incidencia_vistas` ON `incidencia` (`Vistas`);
