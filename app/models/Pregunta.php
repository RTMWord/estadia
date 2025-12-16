<?php
class Pregunta {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todas las incidencias (preguntas) con información del autor y conteo de respuestas
     */
    public function getAll($limit = 50, $offset = 0, $orderBy = 'FechaCreacion', $order = 'DESC') {
        $allowedOrders = ['FechaRegistro', 'Puntos', 'Vistas'];
        if (!in_array($orderBy, $allowedOrders)) {
            $orderBy = 'FechaRegistro';
        }
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT i.*, 
                       u.Nombre, u.ApellidoP, u.Email,
                       COUNT(DISTINCT ri.idRespuestaIncidencia) as NumRespuestas
                FROM incidencia i
                INNER JOIN usuario u ON i.Usuario_idUsuario = u.idUsuario
                LEFT JOIN respuesta_incidencia ri ON i.idIncidencia = ri.Incidencia_idIncidencia
                WHERE i.Estado != 'CERRADA'
                GROUP BY i.idIncidencia
                ORDER BY i.$orderBy $order
                LIMIT ? OFFSET ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener incidencia por ID con toda la información
     */
    public function getById($idIncidencia) {
        $sql = "SELECT i.*, 
                       u.Nombre, u.ApellidoP, u.Email,
                       COUNT(DISTINCT ri.idRespuestaIncidencia) as NumRespuestas
                FROM incidencia i
                INNER JOIN usuario u ON i.Usuario_idUsuario = u.idUsuario
                LEFT JOIN respuesta_incidencia ri ON i.idIncidencia = ri.Incidencia_idIncidencia
                WHERE i.idIncidencia = ?
                GROUP BY i.idIncidencia";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idIncidencia]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear una nueva incidencia (pregunta)
     */
    public function create($usuarioId, $titulo, $cuerpo) {
        $sql = "INSERT INTO incidencia (Usuario_idUsuario, Titulo, Descripcion, Estado) 
                VALUES (?, ?, ?, 'ABIERTA')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuarioId, $titulo, $cuerpo]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Incrementar vistas
     */
    public function incrementViews($idIncidencia) {
        $stmt = $this->pdo->prepare("UPDATE incidencia SET Vistas = Vistas + 1 WHERE idIncidencia = ?");
        $stmt->execute([$idIncidencia]);
    }

    /**
     * Votar incidencia
     */
    public function votar($idIncidencia, $usuarioId, $valor) {
        try {
            $this->pdo->beginTransaction();

            // Verificar si ya votó
            $stmt = $this->pdo->prepare("SELECT Valor FROM voto_incidencia WHERE Incidencia_idIncidencia = ? AND Usuario_idUsuario = ?");
            $stmt->execute([$idIncidencia, $usuarioId]);
            $votoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($votoExistente) {
                // Cambiar voto
                $diferencia = $valor - $votoExistente['Valor'];
                $stmt = $this->pdo->prepare("UPDATE voto_incidencia SET Valor = ? WHERE Incidencia_idIncidencia = ? AND Usuario_idUsuario = ?");
                $stmt->execute([$valor, $idIncidencia, $usuarioId]);
            } else {
                // Nuevo voto
                $diferencia = $valor;
                $stmt = $this->pdo->prepare("INSERT INTO voto_incidencia (Incidencia_idIncidencia, Usuario_idUsuario, Valor) VALUES (?, ?, ?)");
                $stmt->execute([$idIncidencia, $usuarioId, $valor]);
            }

            // Actualizar puntos de la incidencia
            $stmt = $this->pdo->prepare("UPDATE incidencia SET Puntos = Puntos + ? WHERE idIncidencia = ?");
            $stmt->execute([$diferencia, $idIncidencia]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener estadísticas generales
     */
    public function getEstadisticas() {
        $stats = [];

        // Total preguntas (incidencias abiertas/activas)
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM incidencia WHERE Estado != 'CERRADA'");
        $stats['totalPreguntas'] = $stmt->fetchColumn();

        // Total respuestas
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM respuesta_incidencia");
        $stats['totalRespuestas'] = $stmt->fetchColumn();

        // Total miembros activos
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM usuario WHERE Activo = 1");
        $stats['totalMiembros'] = $stmt->fetchColumn();

        // Porcentaje resueltas (Estado = RESUELTA)
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM incidencia WHERE Estado = 'RESUELTA'");
        $resueltas = $stmt->fetchColumn();
        $stats['porcentajeResueltas'] = $stats['totalPreguntas'] > 0 ? round(($resueltas / $stats['totalPreguntas']) * 100) : 0;

        return $stats;
    }

    /**
     * Marcar incidencia como resuelta
     */
    public function marcarResuelta($idIncidencia, $respuestaId, $usuarioId) {
        // Verificar que el usuario es el dueño de la incidencia
        $stmt = $this->pdo->prepare("SELECT Usuario_idUsuario FROM incidencia WHERE idIncidencia = ?");
        $stmt->execute([$idIncidencia]);
        $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$incidencia || $incidencia['Usuario_idUsuario'] != $usuarioId) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            // Desmarcar respuesta anterior si existe
            $this->pdo->prepare("UPDATE respuesta_incidencia SET Aceptada = 0 WHERE Incidencia_idIncidencia = ?")
                ->execute([$idIncidencia]);

            // Marcar nueva respuesta
            $this->pdo->prepare("UPDATE respuesta_incidencia SET Aceptada = 1 WHERE idRespuestaIncidencia = ?")
                ->execute([$respuestaId]);

            // Actualizar incidencia
            $this->pdo->prepare("UPDATE incidencia SET Estado = 'RESUELTA', RespuestaAceptada_idRespuestaIncidencia = ? WHERE idIncidencia = ?")
                ->execute([$respuestaId, $idIncidencia]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

