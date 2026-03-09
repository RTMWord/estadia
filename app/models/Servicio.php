<?php
// app/models/Servicio.php
// Clase Servicio usando mysqli ($conn) — integra los métodos proporcionados.

if (!class_exists('Servicio')) {
    class Servicio {
        protected $conn;

        public function __construct($conn) {
            $this->conn = $conn;
        }

        // Static helper for legacy calls: get all active servicios
        public static function getAll($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT s.*, a.Nombre AS Agencia FROM servicio s LEFT JOIN agencia a ON s.Agencia_idAgencia = a.idAgencia WHERE s.Activo=1 ORDER BY s.Nombre");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log('Servicio::getAll error: ' . $e->getMessage());
                return [];
            }
        }

        // Obtener servicios activos con filtros opcionales
        public function obtenerActivos($filters = [], $limit = 0, $offset = 0) {
            // Use PDO for queries
                $sql = "SELECT idServicio AS id, Nombre AS titulo, Descripcion AS descripcion, Imagen AS imagen, Costo AS precio, Agencia_idAgencia AS agencia_id, Activo AS status
                    FROM servicio WHERE Activo = 1";
            $params = [];
            if (!empty($filters['q'])) {
                $sql .= " AND (Nombre LIKE :q OR Descripcion LIKE :q2)";
                $params[':q'] = '%' . $filters['q'] . '%';
                $params[':q2'] = '%' . $filters['q'] . '%';
            }
            $sql .= " ORDER BY idServicio DESC";
            if ($limit > 0) {
                $sql .= " LIMIT " . intval($limit);
                if ($offset > 0) {
                    $sql .= " OFFSET " . intval($offset);
                }
            }
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log('Servicio::obtenerActivos error: ' . $e->getMessage());
                return [];
            }
        }

        // Obtener servicio por id (incluso inactivo)
        public function obtenerPorId($id) {
            $sql = "SELECT idServicio AS id, Nombre AS titulo, Descripcion AS descripcion, Costo AS precio, Agencia_idAgencia AS agencia_id, Activo AS status, Imagen AS imagen FROM servicio WHERE idServicio = ? LIMIT 1";
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log('Servicio::obtenerPorId error: ' . $e->getMessage());
                return null;
            }
        }

        // Agregar servicio
        public function crear($data) {
            // Adapt to existing `servicio` table (PDO)
            $sql = "INSERT INTO servicio (Nombre, Descripcion, Costo, Agencia_idAgencia, Imagen, Activo) VALUES (?, ?, ?, ?, ?, ?)";
            try {
                $status = isset($data['status']) ? (int)$data['status'] : 1;
                $precio = isset($data['precio']) ? (float)$data['precio'] : 0.0;
                $nombre = $data['titulo'] ?? '';
                $descripcion = $data['descripcion'] ?? '';
                $agencia = !empty($data['agencia']) ? (int)$data['agencia'] : null;
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$nombre, $descripcion, $precio, $agencia, $data['imagen'] ?? null, $status]);
                $id = $this->conn->lastInsertId();
                return ['ok' => true, 'id' => $id];
            } catch (Exception $e) {
                return ['ok' => false, 'error' => $e->getMessage()];
            }
        }

        // Editar servicio (si 'imagen' no está en $data, no cambia la imagen)
        public function editar($id, $data) {
            // Map to `servicio` table columns
            // Si incluye 'imagen' en $data, actualizarla; si no, mantener la imagen actual
            // Incluir los campos adicionales; si 'imagen' está presente actualizarla también
            if (array_key_exists('imagen', $data)) {
                $sql = "UPDATE servicio SET Nombre = ?, Descripcion = ?, Costo = ?, Agencia_idAgencia = ?, Imagen = ?, Activo = ? WHERE idServicio = ?";
                $params = [
                    $data['titulo'],
                    $data['descripcion'],
                    (float)$data['precio'],
                    !empty($data['agencia']) ? (int)$data['agencia'] : null,
                    $data['imagen'] ?: null,
                    (int)$data['status'],
                    (int)$id
                ];
            } else {
                $sql = "UPDATE servicio SET Nombre = ?, Descripcion = ?, Costo = ?, Agencia_idAgencia = ?, Activo = ? WHERE idServicio = ?";
                $params = [
                    $data['titulo'],
                    $data['descripcion'],
                    (float)$data['precio'],
                    !empty($data['agencia']) ? (int)$data['agencia'] : null,
                    (int)$data['status'],
                    (int)$id
                ];
            }
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                return ['ok' => true, 'affected' => $stmt->rowCount()];
            } catch (Exception $e) {
                return ['ok' => false, 'error' => $e->getMessage()];
            }
        }

        // Eliminación lógica
        public function eliminar($id) {
            $sql = "UPDATE servicio SET Activo = 0 WHERE idServicio = ?";
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id]);
                return ['ok' => true];
            } catch (Exception $e) {
                return ['ok' => false, 'error' => $e->getMessage()];
            }
        }

        // Obtener categorías y ubicaciones (para filtros)
        public function obtenerCategorias() {
            // No hay columna 'categoria' en la tabla actual 'servicio'. Devolver vacío.
            return [];
        }

        public function obtenerUbicaciones() {
            // No hay columna 'ubicacion' en la tabla actual 'servicio'. Devolver vacío.
            return [];
        }
    }
}
?>

