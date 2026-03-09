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
        $fecha_inicio = !empty($filters['fecha_inicio']) ? $filters['fecha_inicio'] : date('Y-m-d', strtotime('-30 days'));
        $fecha_fin = !empty($filters['fecha_fin']) ? $filters['fecha_fin'] : date('Y-m-d');
        
        $results = [];

        // 1. Tasa de Cancelación y Realización (histórico)
        $sql_tasa = "SELECT 
            SUM(CASE WHEN Estado = 'AGENDADA' OR Estado = 'CONFIRMADA' THEN 1 ELSE 0 END) AS TotalAgendadas,
            SUM(CASE WHEN Estado = 'CANCELADA' THEN 1 ELSE 0 END) AS TotalCanceladas,
            SUM(CASE WHEN Estado = 'REALIZADA' THEN 1 ELSE 0 END) AS TotalRealizadas
            FROM Cita
            WHERE FechaRegistro >= ? AND FechaRegistro <= ?";
        try {
            $stmt_tasa = $this->pdo->prepare($sql_tasa);
            $stmt_tasa->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
            $tasa_data = $stmt_tasa->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Fallback para instalaciones con diferencias de esquema/columnas de fecha
            $sql_tasa_fallback = "SELECT 
                SUM(CASE WHEN Estado = 'AGENDADA' OR Estado = 'CONFIRMADA' THEN 1 ELSE 0 END) AS TotalAgendadas,
                SUM(CASE WHEN Estado = 'CANCELADA' THEN 1 ELSE 0 END) AS TotalCanceladas,
                SUM(CASE WHEN Estado = 'REALIZADA' THEN 1 ELSE 0 END) AS TotalRealizadas
                FROM Cita
                WHERE FechaHora >= ? AND FechaHora <= ?";
            $stmt_tasa_fb = $this->pdo->prepare($sql_tasa_fallback);
            $stmt_tasa_fb->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
            $tasa_data = $stmt_tasa_fb->fetch(PDO::FETCH_ASSOC);
        }

        if (!is_array($tasa_data)) {
            $tasa_data = ['TotalAgendadas' => 0, 'TotalCanceladas' => 0, 'TotalRealizadas' => 0];
        }
        
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

        // 2. Top 5 Agencias por Demanda (periodo filtrado)
        $sql_demanda = "SELECT a.Nombre AS Agencia, COUNT(c.idCita) AS CitasProximas
            FROM Cita c
            INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio
            LEFT JOIN Agencia a ON s.Agencia_idAgencia = a.idAgencia
            WHERE c.Estado IN ('PENDIENTE', 'AGENDADA', 'CONFIRMADA')
            AND c.FechaHora >= ? AND c.FechaHora <= ?
            GROUP BY a.Nombre
            ORDER BY CitasProximas DESC
            LIMIT 5";
        try {
            $stmt_demanda = $this->pdo->prepare($sql_demanda);
            $stmt_demanda->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
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
            AND c.FechaHora >= ? AND c.FechaHora <= ?
            GROUP BY s.Nombre";
            
        try {
            $stmt_latencia = $this->pdo->prepare($sql_latencia);
            $stmt_latencia->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
            $results['Latencia_Promedio_de_Agendamiento'] = $stmt_latencia ? $stmt_latencia->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (\PDOException $e) {
            $results['Latencia_Promedio_de_Agendamiento'] = [];
        }

        return $results;
    }

    // --- Reporte Analítico 2: Análisis de Rendimiento de Inventario (Productos) ---
    public function getInventarioRendimientoReport($filters = []) {
        $results = [];
        $results['Ventas_y_Rentabilidad'] = [];

        $activoFilter = (isset($filters['activo']) && $filters['activo'] !== '') ? (int)$filters['activo'] : null;
        $fecha_inicio = !empty($filters['fecha_inicio']) ? $filters['fecha_inicio'] : null;
        $fecha_fin = !empty($filters['fecha_fin']) ? $filters['fecha_fin'] : null;

        // 1. Top 10 Productos más Vendidos y Rentables (Uso de SP o Fallback)
        try {
            // Se asume que el SP sp_reporteVentasPorProducto es funcional
            $sql_ventas = "CALL sp_reporteVentasPorProducto()";
            $stmt_ventas = $this->pdo->query($sql_ventas);
            $results['Ventas_y_Rentabilidad'] = $stmt_ventas ? $stmt_ventas->fetchAll(PDO::FETCH_ASSOC) : [];
            if ($stmt_ventas) {
                while ($stmt_ventas->nextRowset()) {}
                $stmt_ventas->closeCursor();
            }
        } catch (\PDOException $e) {
            $results['Ventas_y_Rentabilidad'] = [];
        }

        // Fallback robusto si SP no existe/no devuelve datos
        if (empty($results['Ventas_y_Rentabilidad'])) {
            $fallbackQueries = [
                "SELECT p.Nombre AS Producto, SUM(d.Cantidad) AS TotalVendido, SUM(d.Cantidad * d.PrecioUnitario) AS Ingresos
                 FROM Producto p
                 INNER JOIN DetallePedido d ON p.idProducto = d.Producto_idProducto
                 INNER JOIN Pedido pe ON d.Pedido_idPedido = pe.idPedido
                 WHERE pe.Estado IN ('PAGADO','ENVIADO','ENTREGADO')",
                "SELECT p.Nombre AS Producto, SUM(d.Cantidad) AS TotalVendido, SUM(d.Cantidad * d.PrecioUnitario) AS Ingresos
                 FROM producto p
                 INNER JOIN detallepedido d ON p.idProducto = d.Producto_idProducto
                 INNER JOIN pedido pe ON d.Pedido_idPedido = pe.idPedido
                 WHERE pe.Estado IN ('PAGADO','ENVIADO','ENTREGADO')"
            ];

            if ($activoFilter !== null) {
                foreach ($fallbackQueries as &$q) {
                    $q .= " AND p.Activo = ?";
                }
                unset($q);
            }

            if ($fecha_inicio) {
                foreach ($fallbackQueries as &$q) {
                    $q .= " AND pe.FechaPedido >= ?";
                }
                unset($q);
            }

            if ($fecha_fin) {
                foreach ($fallbackQueries as &$q) {
                    $q .= " AND pe.FechaPedido <= ?";
                }
                unset($q);
            }

            foreach ($fallbackQueries as &$q) {
                $q .= " GROUP BY p.idProducto, p.Nombre ORDER BY Ingresos DESC LIMIT 10";
            }
            unset($q);

            foreach ($fallbackQueries as $query) {
                try {
                    $params = [];
                    if ($activoFilter !== null) {
                        $params[] = $activoFilter;
                    }
                    if ($fecha_inicio) {
                        $params[] = $fecha_inicio . ' 00:00:00';
                    }
                    if ($fecha_fin) {
                        $params[] = $fecha_fin . ' 23:59:59';
                    }

                    $stmt_fb = $this->pdo->prepare($query);
                    $stmt_fb->execute($params);
                    $rows = $stmt_fb->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($rows)) {
                        $results['Ventas_y_Rentabilidad'] = $rows;
                        break;
                    }
                } catch (\PDOException $e) {
                    // Intenta siguiente variante de consulta
                }
            }
        }

        // 2. Valoración Total de Inventario y Resumen de Rotación
        $sql_valor = "SELECT SUM(Precio * Existencia) AS ValorTotalInventario, COUNT(idProducto) as TotalProductosUnicos, SUM(Existencia) AS TotalUnidadesStock
            FROM Producto WHERE 1=1";
        $valorParams = [];
        if ($activoFilter !== null) {
            $sql_valor .= " AND Activo = ?";
            $valorParams[] = $activoFilter;
        }
        try {
            $stmt_valor = $this->pdo->prepare($sql_valor);
            $stmt_valor->execute($valorParams);
            $valor_data = $stmt_valor ? $stmt_valor->fetch(PDO::FETCH_ASSOC) : [];
        } catch (\PDOException $e) {
            // Fallback por posible variación de mayúsculas/minúsculas en tabla
            $sql_valor_fb = str_replace('FROM Producto', 'FROM producto', $sql_valor);
            try {
                $stmt_valor_fb = $this->pdo->prepare($sql_valor_fb);
                $stmt_valor_fb->execute($valorParams);
                $valor_data = $stmt_valor_fb ? $stmt_valor_fb->fetch(PDO::FETCH_ASSOC) : [];
            } catch (\PDOException $e) {
                $valor_data = [];
            }
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
        // Implementación basada en diagnósticos (data/diagnosticos.json)
        $dataFile = __DIR__ . '/../../data/diagnosticos.json';
        $entries = [];
        if (is_file($dataFile)) {
            $raw = @file_get_contents($dataFile);
            $entries = $raw ? json_decode($raw, true) : [];
            if (!is_array($entries)) {
                $entries = [];
            }
            // Soporte para formatos con contenedor (ej. {"items": [...]})
            if (is_array($entries) && !array_is_list($entries)) {
                foreach (['items', 'data', 'diagnosticos'] as $containerKey) {
                    if (isset($entries[$containerKey]) && is_array($entries[$containerKey])) {
                        $entries = $entries[$containerKey];
                        break;
                    }
                }
            }
        }

        if (!is_array($entries)) {
            $entries = [];
        }

        // Filtros opcionales (mismos campos que Diagnósticos)
        $fecha_inicio = !empty($filters['fecha_inicio']) ? strtotime($filters['fecha_inicio'] . ' 00:00:00') : null;
        $fecha_fin = !empty($filters['fecha_fin']) ? strtotime($filters['fecha_fin'] . ' 23:59:59') : null;
        $filtro_ciudad = isset($filters['ciudad']) ? trim((string)$filters['ciudad']) : '';
        $filtro_perfil = isset($filters['perfil']) ? trim((string)$filters['perfil']) : '';
        $filtro_dificultad = isset($filters['dificultad']) ? trim((string)$filters['dificultad']) : '';

        $perfilCounts = [];
        $cityCounts = [];
        $difficultyCounts = [];
        $scored = [];

        foreach ($entries as $e) {
            if (!is_array($e)) {
                continue;
            }

            $createdTs = isset($e['created_at']) ? strtotime((string)$e['created_at']) : null;
            if ($fecha_inicio && ($createdTs === false || $createdTs < $fecha_inicio)) {
                continue;
            }
            if ($fecha_fin && ($createdTs === false || $createdTs > $fecha_fin)) {
                continue;
            }

            $perfil = trim((string)($e['perfil'] ?? 'desconocido'));
            if ($perfil === '') {
                $perfil = 'desconocido';
            }

            $ciudad = trim((string)($e['contact']['ciudad'] ?? 'desconocida'));
            if ($ciudad === '') {
                $ciudad = 'desconocida';
            }

            if ($filtro_ciudad !== '' && stripos($ciudad, $filtro_ciudad) === false) {
                continue;
            }
            if ($filtro_perfil !== '' && $perfil !== $filtro_perfil) {
                continue;
            }

            $difs = $e['dificultades'] ?? [];
            if (!is_array($difs)) {
                $difs = [];
            }
            $difs = array_values(array_filter(array_map(function($d){
                return trim((string)$d);
            }, $difs), function($d){
                return $d !== '';
            }));

            if ($filtro_dificultad !== '' && !in_array($filtro_dificultad, $difs, true)) {
                continue;
            }

            $perfilCounts[$perfil] = ($perfilCounts[$perfil] ?? 0) + 1;
            $cityCounts[$ciudad] = ($cityCounts[$ciudad] ?? 0) + 1;
            foreach ($difs as $d) $difficultyCounts[$d] = ($difficultyCounts[$d] ?? 0) + 1;

            // Score heuristic: perfil weight + number of dificultades
            $weight = 0;
            if (stripos($perfil, 'adulto') !== false || stripos($perfil, 'adulto_mayor') !== false) $weight += 2;
            if (stripos($perfil, 'otro') !== false) $weight += 1;
            $score = $weight + count($difs);

            $scored[] = [
                'id' => $e['id'] ?? '',
                'nombre' => $e['contact']['nombre'] ?? '',
                'email' => $e['contact']['email'] ?? '',
                'ciudad' => $ciudad,
                'perfil' => $perfil,
                'dificultades' => implode(', ', $difs),
                'score' => $score,
                'created_at' => $e['created_at'] ?? '',
            ];
        }

        // ordenar por score descendente
        usort($scored, function($a, $b){ return ($b['score'] <=> $a['score']); });

        // preparar secciones retorno
        arsort($perfilCounts);
        arsort($cityCounts);

        $perfilRows = [];
        foreach ($perfilCounts as $perfil => $cantidad) {
            $perfilRows[] = ['perfil' => $perfil, 'cantidad' => $cantidad];
        }

        $cityRows = [];
        foreach ($cityCounts as $ciudad => $cantidad) {
            $cityRows[] = ['ciudad' => $ciudad, 'cantidad' => $cantidad];
        }

        $results = [];
        $results['Resumen_Por_Perfil'] = $perfilRows;
        $results['Usuarios_Por_Ciudad'] = $cityRows;
        // top dificultades
        arsort($difficultyCounts);
        $topDiff = [];
        foreach ($difficultyCounts as $k => $v) $topDiff[] = ['dificultad' => $k, 'cantidad' => $v];
        $results['Top_Dificultades'] = array_slice($topDiff, 0, 20);
        // usuarios alto riesgo (top 20)
        $results['Usuarios_Alto_Riesgo'] = array_slice($scored, 0, 20);

        return $results;
    }
    
    public function getTemasCriticosReport($filters = []) {
        // Análisis simple de frecuencia de palabras en sugerencias
        $params = [];
        $where = ' WHERE 1=1';
        if (!empty($filters['estado'])) {
            $where .= ' AND s.Estado = ?';
            $params[] = $filters['estado'];
        }
        if (!empty($filters['fecha_inicio'])) {
            $where .= ' AND s.FechaRegistro >= ?';
            $params[] = $filters['fecha_inicio'] . ' 00:00:00';
        }
        if (!empty($filters['fecha_fin'])) {
            $where .= ' AND s.FechaRegistro <= ?';
            $params[] = $filters['fecha_fin'] . ' 23:59:59';
        }

        $rows = [];
        $queries = [
            'SELECT s.Titulo, s.Descripcion, u.Nombre AS UsuarioNombre FROM Sugerencia s LEFT JOIN Usuario u ON s.Usuario_idUsuario = u.idUsuario' . $where,
            'SELECT s.Titulo, s.Descripcion, u.Nombre AS UsuarioNombre FROM sugerencia s LEFT JOIN usuario u ON s.Usuario_idUsuario = u.idUsuario' . $where
        ];
        foreach ($queries as $sql) {
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                if (is_array($rows)) {
                    break;
                }
            } catch (\PDOException $e) {
                $rows = [];
            }
        }

        $text = '';
        foreach ($rows as $r) {
            if (!is_array($r)) continue;
            $text .= ' ' . ($r['Titulo'] ?? '') . ' ' . ($r['Descripcion'] ?? '');
        }

        // Fallback: si no hay sugerencias, usar señales textuales de diagnosticos.json
        if (trim($text) === '') {
            $diagFile = __DIR__ . '/../../data/diagnosticos.json';
            if (is_file($diagFile)) {
                $raw = @file_get_contents($diagFile);
                $entries = $raw ? json_decode($raw, true) : [];
                if (is_array($entries)) {
                    foreach ($entries as $e) {
                        if (!is_array($e)) continue;
                        $text .= ' ' . ($e['perfil'] ?? '');
                        $text .= ' ' . implode(' ', is_array($e['dificultades'] ?? null) ? $e['dificultades'] : []);
                        $text .= ' ' . implode(' ', is_array($e['intereses'] ?? null) ? $e['intereses'] : []);
                        $text .= ' ' . ($e['tipo_vivienda'] ?? '');
                        $text .= ' ' . ($e['contact']['ciudad'] ?? '');
                    }
                }
            }
        }

        // Normalizar y tokenizar
        $text = mb_strtolower($text, 'UTF-8');
        // quitar signos y números
        $text = preg_replace('/[^\p{L}\s]/u', ' ', $text);
        $tokens = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $stopwords = [
            'de','la','que','el','en','y','a','los','se','del','las','por','con','para','es','un','una','su','al','como','más','pero','no','si','porque','sus','le','lo','ya','o','este','esta','está','son','fue','ha','han','tener'
        ];
        $freq = [];
        foreach ($tokens as $t) {
            if (mb_strlen($t) < 4) continue;
            if (in_array($t, $stopwords)) continue;
            $freq[$t] = ($freq[$t] ?? 0) + 1;
        }

        arsort($freq);
        $top = [];
        $count = 0;
        foreach ($freq as $k => $v) {
            $top[] = ['term' => $k, 'count' => $v];
            $count++;
            if ($count >= 30) break;
        }

        $results = [];
        $results['Nota_Metodologia'] = [
            'Propósito' => 'Analizar texto libre de sugerencias para priorizar temas críticos.',
            'Método' => 'Conteo de términos tras normalización y eliminación de stopwords (heurístico).',
            'Fuente' => !empty($rows) ? 'Sugerencias registradas' : 'Diagnósticos (fallback)'
        ];
        $results['Top_Temas'] = $top;
        $results['Resumen_Cobertura'] = [
            'Total de sugerencias analizadas' => count($rows),
            'Total de términos relevantes detectados' => count($top),
        ];

        return $results;
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