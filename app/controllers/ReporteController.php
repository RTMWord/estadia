<?php
require_once __DIR__ . '/../config/db.php';

class ReporteController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- Reportes CRUD (Base) ---
    public function getCitasReporte($filters = []) {
        $sql = 'SELECT c.idCita, CONCAT(u.Nombre, " ", u.ApellidoP) AS Usuario, u.Email AS Email, s.Nombre AS Servicio, c.FechaHora, c.Estado 
                FROM Cita c
                INNER JOIN Usuario u ON c.Usuario_idUsuario = u.idUsuario
                INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio
                WHERE 1=1';
        $params = [];

        if (!empty($filters['estado'])) {
            $sql .= ' AND c.Estado = ?';
            $params[] = $filters['estado'];
        }
        if (!empty($filters['fecha_inicio'])) {
            $sql .= ' AND c.FechaHora >= ?';
            $params[] = $filters['fecha_inicio'] . ' 00:00:00';
        }
        if (!empty($filters['fecha_fin'])) {
            $sql .= ' AND c.FechaHora <= ?';
            $params[] = $filters['fecha_fin'] . ' 23:59:59';
        }

        $sql .= ' ORDER BY c.FechaHora DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUsuariosReporte($filters = []) {
        $sql = 'SELECT u.idUsuario, CONCAT(u.Nombre, " ", u.ApellidoP, " ", u.ApellidoM) AS NombreCompleto, u.Email, u.Telefono, u.Activo, u.Tipo, r.Nombre AS Rol, u.FechaRegistro
                FROM Usuario u
                LEFT JOIN UsuarioRol ur ON u.idUsuario = ur.Usuario_idUsuario
                LEFT JOIN Rol r ON ur.Rol_idRol = r.idRol
                WHERE 1=1';
        $params = [];

        if (!empty($filters['rol'])) {
            $sql .= ' AND r.Nombre = ?';
            $params[] = $filters['rol'];
        }
        if (!empty($filters['tipo'])) {
            $sql .= ' AND u.Tipo = ?';
            $params[] = $filters['tipo'];
        }
        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $sql .= ' AND u.Activo = ?';
            $params[] = $filters['activo'];
        }
        if (!empty($filters['fecha_inicio'])) {
            $sql .= ' AND u.FechaRegistro >= ?';
            $params[] = $filters['fecha_inicio'] . ' 00:00:00';
        }
        if (!empty($filters['fecha_fin'])) {
            $sql .= ' AND u.FechaRegistro <= ?';
            $params[] = $filters['fecha_fin'] . ' 23:59:59';
        }

        $sql .= ' ORDER BY u.idUsuario ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getServiciosReporte($filters = []) {
        $sql = 'SELECT s.idServicio, s.Nombre, s.Descripcion, s.Costo, a.Nombre AS Agencia, s.Activo
                FROM Servicio s
                LEFT JOIN Agencia a ON s.Agencia_idAgencia = a.idAgencia
                WHERE 1=1';
        $params = [];

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $sql .= ' AND s.Activo = ?';
            $params[] = $filters['activo'];
        }
        if (!empty($filters['agencia'])) {
            $sql .= ' AND a.Nombre = ?';
            $params[] = $filters['agencia'];
        }

        $sql .= ' ORDER BY s.Nombre ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductosReporte($filters = []) {
        $sql = 'SELECT idProducto, Nombre, Precio, Existencia, Activo
                FROM Producto
                WHERE 1=1';
        $params = [];

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $sql .= ' AND Activo = ?';
            $params[] = $filters['activo'];
        }
        if (isset($filters['existencia']) && $filters['existencia'] !== '') {
            $allowedOps = ['>=', '<=', '>', '<', '='];
            $op = $filters['existencia_op'] ?? '>=';
            if (!in_array($op, $allowedOps, true)) {
                $op = '>=';
            }
            $sql .= ' AND Existencia ' . $op . ' ?';
            $params[] = (int)$filters['existencia'];
        }

        $sql .= ' ORDER BY Nombre ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Reporte Analítico 1: Capacidad Operativa y Eficiencia (Citas y Servicios) ---
    public function getCapacidadOperativaReport($filters = []) {
        // Por defecto, se analiza el último mes
        $fecha_inicio = $filters['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
        $fecha_fin = $filters['fecha_fin'] ?? date('Y-m-d');
        
        $results = [];

        // 1. Tasa de Cancelación y Realización (histórico)
        $sql_tasa = "SELECT 
            SUM(CASE WHEN Estado = 'AGENDADA' OR Estado = 'CONFIRMADA' THEN 1 ELSE 0 END) AS TotalAgendadas,
            SUM(CASE WHEN Estado = 'CANCELADA' THEN 1 ELSE 0 END) AS TotalCanceladas,
            SUM(CASE WHEN Estado = 'REALIZADA' THEN 1 ELSE 0 END) AS TotalRealizadas
            FROM Cita
            WHERE FechaRegistro >= ? AND FechaRegistro <= ?";
        $stmt_tasa = $this->pdo->prepare($sql_tasa);
        $stmt_tasa->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
        $tasa_data = $stmt_tasa->fetch(PDO::FETCH_ASSOC);
        
        $total_evaluadas = (int)$tasa_data['TotalCanceladas'] + (int)$tasa_data['TotalRealizadas'];
        $tasa_cancelacion = ($tasa_data['TotalCanceladas'] > 0 && $total_evaluadas > 0) ? round(($tasa_data['TotalCanceladas'] / $total_evaluadas) * 100, 2) : 0;
        
        $results['Tasa_General_y_Eficiencia'] = [
            'Periodo Analizado' => $fecha_inicio . ' a ' . $fecha_fin,
            'Total Citas Agendadas/Confirmadas' => $tasa_data['TotalAgendadas'],
            'Total Citas Finalizadas (Canceladas + Realizadas)' => $total_evaluadas,
            'Total Citas Canceladas' => $tasa_data['TotalCanceladas'],
            'Tasa de Cancelación (%)' => $tasa_cancelacion . '%',
            'Total Citas Realizadas' => $tasa_data['TotalRealizadas'],
        ];

        // 2. Top 5 Agencias por Demanda Próxima (Próximos 30 días)
        $sql_demanda = "SELECT a.Nombre AS Agencia, COUNT(c.idCita) AS CitasProximas
            FROM Cita c
            INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio
            LEFT JOIN Agencia a ON s.Agencia_idAgencia = a.idAgencia
            WHERE c.Estado IN ('AGENDADA', 'CONFIRMADA') AND c.FechaHora >= NOW() AND c.FechaHora <= DATE_ADD(NOW(), INTERVAL 30 DAY)
            GROUP BY a.Nombre
            ORDER BY CitasProximas DESC
            LIMIT 5";
        try {
            $stmt_demanda = $this->pdo->query($sql_demanda);
            $results['Demanda_Proxima_(Proximos_30_Dias)'] = $stmt_demanda ? $stmt_demanda->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (\PDOException $e) {
            $results['Demanda_Proxima_(Proximos_30_Dias)'] = [];
        }

        // 3. Latencia de Servicio (AVG de horas entre Registro y Hora Agendada/Promesa de Servicio)
        $sql_latencia = "SELECT 
            s.Nombre as Servicio,
            ROUND(AVG(TIMESTAMPDIFF(HOUR, c.FechaRegistro, c.FechaHora)), 2) AS LatenciaPromedioHoras
            FROM Cita c
            INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio
            WHERE c.Estado IN ('AGENDADA', 'CONFIRMADA')
            GROUP BY s.Nombre";
            
        try {
            $stmt_latencia = $this->pdo->query($sql_latencia);
            $results['Latencia_Promedio_de_Agendamiento'] = $stmt_latencia ? $stmt_latencia->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (\PDOException $e) {
            $results['Latencia_Promedio_de_Agendamiento'] = [];
        }

        return $results;
    }

    // --- Reporte Analítico 2: Análisis de Rendimiento de Inventario (Productos) ---
    public function getInventarioRendimientoReport($filters = []) {
        $results = [];

        // 1. Top 10 Productos más Vendidos y Rentables (Uso de SP o Fallback)
        try {
            // Se asume que el SP sp_reporteVentasPorProducto es funcional
            $sql_ventas = "CALL sp_reporteVentasPorProducto()";
            $stmt_ventas = $this->pdo->query($sql_ventas);
            $results['Ventas_y_Rentabilidad'] = $stmt_ventas ? $stmt_ventas->fetchAll(PDO::FETCH_ASSOC) : [];
            if ($stmt_ventas) { $stmt_ventas->closeCursor(); }
        } catch (\PDOException $e) {
            // Fallback si hay problemas con el SP (lo más probable)
            $sql_fallback = "SELECT p.Nombre AS Producto, SUM(d.Cantidad) AS TotalVendido, SUM(d.Cantidad * d.PrecioUnitario) AS Ingresos
                FROM Producto p 
                INNER JOIN DetallePedido d ON p.idProducto = d.Producto_idProducto
                INNER JOIN Pedido pe ON d.Pedido_idPedido = pe.idPedido
                WHERE pe.Estado IN ('PAGADO','ENVIADO','ENTREGADO')
                GROUP BY p.idProducto
                ORDER BY Ingresos DESC LIMIT 10";
            try {
                $stmt_fb = $this->pdo->query($sql_fallback);
                $results['Ventas_y_Rentabilidad'] = $stmt_fb ? $stmt_fb->fetchAll(PDO::FETCH_ASSOC) : [];
            } catch (\PDOException $e) {
                $results['Ventas_y_Rentabilidad'] = [];
            }
        }

        // 2. Valoración Total de Inventario y Resumen de Rotación
        $sql_valor = "SELECT SUM(Precio * Existencia) AS ValorTotalInventario, COUNT(idProducto) as TotalProductosUnicos, SUM(Existencia) AS TotalUnidadesStock
            FROM Producto WHERE Activo = 1";
        try {
            $stmt_valor = $this->pdo->query($sql_valor);
            $valor_data = $stmt_valor ? $stmt_valor->fetch(PDO::FETCH_ASSOC) : [];
        } catch (\PDOException $e) {
            $valor_data = [];
        }
        
        $total_vendido = array_sum(array_column($results['Ventas_y_Rentabilidad'], 'TotalVendido'));
        $valor_total_inventario = $valor_data['ValorTotalInventario'] ?? 0;
        $total_productos_en_stock = $valor_data['TotalProductosUnicos'] ?? 0;
        $total_unidades_stock = $valor_data['TotalUnidadesStock'] ?? 0;

        // Tasa de Rotación de Unidades (Vendidas / Unidades Totales en stock)
        $tasa_rotacion = ($total_unidades_stock > 0) ? round($total_vendido / $total_unidades_stock, 2) : 0;

        $results['Resumen_General_Inventario'] = [
            'Valoración Total de Inventario (USD)' => '$' . number_format($valor_total_inventario, 2),
            'Productos Únicos en Stock' => $total_productos_en_stock,
            'Total Unidades en Stock' => $total_unidades_stock,
            'Unidades Vendidas (Periodo Reportado)' => $total_vendido,
            'Índice de Rotación (Ventas / Unidades Stock)' => $tasa_rotacion,
        ];
        
        return $results;
    }
    
    // --- Reporte Analítico 3 y 4 (Marcadores de Posición) ---
    public function getUsuarioPerfilRiesgoReport($filters = []) {
        $data = [
            'Nota_Metodologia' => [
                'Propósito' => 'Identificar usuarios con alta necesidad de accesibilidad y/o bajo engagement.',
                'Dependencia' => 'Requiere la tabla `accesibilidad` (no implementada en el flujo actual) y lógica compleja.',
            ],
            'Marcador_de_Posicion' => [
                'Estatus' => 'No implementado.',
                'Sugerencia' => 'Para implementarlo, necesitaría agregar lógica de consulta a la tabla `accesibilidad` y cruzarla con `cita`/`sugerencia`.'
            ]
        ];
        return $data;
    }
    
    public function getTemasCriticosReport($filters = []) {
        $data = [
            'Nota_Metodologia' => [
                'Propósito' => 'Analizar texto libre de sugerencias e incidencias para priorizar problemas.',
                'Dependencia' => 'Requiere análisis de texto (NLP) y, preferiblemente, campos de auditoría de tiempo de resolución para calcular el tiempo de cierre.',
            ],
            'Marcador_de_Posicion' => [
                'Estatus' => 'No implementado.',
                'Sugerencia' => 'Para implementarlo, necesitaría usar lógica de conteo de palabras clave y calcular diferencias de tiempo entre estados de `Sugerencia` e `Incidencia`.'
            ]
        ];
        return $data;
    }


    // Funciones de apoyo para poblar filtros
    public function getCitaEstados() {
        return ['AGENDADA', 'CONFIRMADA', 'CANCELADA', 'REALIZADA'];
    }

    public function getRoles() {
        $stmt = $this->pdo->query('SELECT Nombre FROM Rol ORDER BY Nombre');
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    public function getActivoStatus() {
        return ['1' => 'Sí', '0' => 'No'];
    }
    
    public function getUsuarioTipos() {
        return ['externo', 'interno'];
    }

    public function getAgencias() {
        $stmt = $this->pdo->query('SELECT Nombre FROM Agencia WHERE EstadoValidacion = "APROBADA" ORDER BY Nombre');
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}