<?php
require_once __DIR__ . '/../config/db.php';

class ReporteController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- Lógica de Reporte Dinámico para Citas ---
    public function getCitasReporte($filters = []) {
        $sql = 'SELECT c.idCita, u.Nombre AS Usuario, u.Email AS Email, s.Nombre AS Servicio, c.FechaHora, c.Estado 
                FROM Cita c
                INNER JOIN Usuario u ON c.Usuario_idUsuario = u.idUsuario
                INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio
                WHERE 1=1';
        $params = [];

        // Filtro por Estado de Cita
        if (!empty($filters['estado'])) {
            $sql .= ' AND c.Estado = ?';
            $params[] = $filters['estado'];
        }
        // Filtro por Fecha (Desde)
        if (!empty($filters['fecha_inicio'])) {
            $sql .= ' AND c.FechaHora >= ?';
            $params[] = $filters['fecha_inicio'] . ' 00:00:00';
        }
        // Filtro por Fecha (Hasta)
        if (!empty($filters['fecha_fin'])) {
            $sql .= ' AND c.FechaHora <= ?';
            $params[] = $filters['fecha_fin'] . ' 23:59:59';
        }

        $sql .= ' ORDER BY c.FechaHora DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // --- (Implementación de reportes para Usuarios, Servicios y Productos similar a la de Citas) ---

    // Funciones de apoyo para poblar filtros
    public function getCitaEstados() {
        // Obtenido del esquema metahogar.sql: ENUM('AGENDADA','CONFIRMADA','CANCELADA','REALIZADA')
        return ['AGENDADA', 'CONFIRMADA', 'CANCELADA', 'REALIZADA'];
    }
}
?>