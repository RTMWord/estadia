<?php
class Respuesta {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtener respuestas de una incidencia
     */
    public function getByIncidencia($idIncidencia, $orderBy = 'Puntos') {
        $allowedOrders = ['Puntos', 'FechaCreacion'];
        if (!in_array($orderBy, $allowedOrders)) {
            $orderBy = 'Puntos';
        }

        $sql = "SELECT ri.*, 
                       u.Nombre, u.ApellidoP, u.Email
                FROM respuesta_incidencia ri
                INNER JOIN usuario u ON ri.Usuario_idUsuario = u.idUsuario
                WHERE ri.Incidencia_idIncidencia = ?
                ORDER BY ri.Aceptada DESC, ri.$orderBy DESC, ri.FechaCreacion ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idIncidencia]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear una nueva respuesta
     */
    public function create($idIncidencia, $usuarioId, $cuerpo) {
        $sql = "INSERT INTO respuesta_incidencia (Incidencia_idIncidencia, Usuario_idUsuario, Cuerpo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idIncidencia, $usuarioId, $cuerpo]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Votar respuesta
     */
    public function votar($idRespuesta, $usuarioId, $valor) {
        try {
            $this->pdo->beginTransaction();

            // Verificar si ya votó
            $stmt = $this->pdo->prepare("SELECT Valor FROM voto_respuesta_incidencia WHERE RespuestaIncidencia_idRespuestaIncidencia = ? AND Usuario_idUsuario = ?");
            $stmt->execute([$idRespuesta, $usuarioId]);
            $votoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($votoExistente) {
                // Cambiar voto
                $diferencia = $valor - $votoExistente['Valor'];
                $stmt = $this->pdo->prepare("UPDATE voto_respuesta_incidencia SET Valor = ? WHERE RespuestaIncidencia_idRespuestaIncidencia = ? AND Usuario_idUsuario = ?");
                $stmt->execute([$valor, $idRespuesta, $usuarioId]);
            } else {
                // Nuevo voto
                $diferencia = $valor;
                $stmt = $this->pdo->prepare("INSERT INTO voto_respuesta_incidencia (RespuestaIncidencia_idRespuestaIncidencia, Usuario_idUsuario, Valor) VALUES (?, ?, ?)");
                $stmt->execute([$idRespuesta, $usuarioId, $valor]);
            }

            // Actualizar puntos de la respuesta
            $stmt = $this->pdo->prepare("UPDATE respuesta_incidencia SET Puntos = Puntos + ? WHERE idRespuestaIncidencia = ?");
            $stmt->execute([$diferencia, $idRespuesta]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener voto de usuario para una respuesta
     */
    public function getVotoUsuario($idRespuesta, $usuarioId) {
        $stmt = $this->pdo->prepare("SELECT Valor FROM voto_respuesta_incidencia WHERE RespuestaIncidencia_idRespuestaIncidencia = ? AND Usuario_idUsuario = ?");
        $stmt->execute([$idRespuesta, $usuarioId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['Valor'] : 0;
    }

    /**
     * Actualizar respuesta
     */
    public function update($idRespuesta, $usuarioId, $cuerpo) {
        // Verificar que el usuario es el dueño
        $stmt = $this->pdo->prepare("SELECT Usuario_idUsuario FROM respuesta_incidencia WHERE idRespuestaIncidencia = ?");
        $stmt->execute([$idRespuesta]);
        $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$respuesta || $respuesta['Usuario_idUsuario'] != $usuarioId) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE respuesta_incidencia SET Cuerpo = ?, FechaActualizacion = NOW() WHERE idRespuestaIncidencia = ?");
        $stmt->execute([$cuerpo, $idRespuesta]);
        return true;
    }

    /**
     * Eliminar respuesta
     */
    public function delete($idRespuesta, $usuarioId) {
        // Verificar que el usuario es el dueño
        $stmt = $this->pdo->prepare("SELECT Usuario_idUsuario FROM respuesta_incidencia WHERE idRespuestaIncidencia = ?");
        $stmt->execute([$idRespuesta]);
        $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$respuesta || $respuesta['Usuario_idUsuario'] != $usuarioId) {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM respuesta_incidencia WHERE idRespuestaIncidencia = ?");
        $stmt->execute([$idRespuesta]);
        return true;
    }
}
