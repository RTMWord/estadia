<?php
// app/models/Servicio.php
// Clase Servicio usando mysqli ($conn) — integra los métodos proporcionados.

if (!class_exists('Servicio')) {
    class Servicio {
        protected $conn;

        public function __construct($conn) {
            $this->conn = $conn;
        }

        // Obtener servicios activos con filtros opcionales
        public function obtenerActivos($filters = [], $limit = 0, $offset = 0) {
            $sql = "SELECT id, titulo, descripcion_corta, categoria, ubicacion, contacto, imagen, precio, created_at
                    FROM servicios WHERE status = 1";
            $params = [];
            $types = '';

            if (!empty($filters['categoria'])) {
                $sql .= " AND categoria = ?";
                $params[] = $filters['categoria'];
                $types .= 's';
            }
            if (!empty($filters['ubicacion'])) {
                $sql .= " AND ubicacion = ?";
                $params[] = $filters['ubicacion'];
                $types .= 's';
            }
            if (!empty($filters['q'])) {
                $sql .= " AND (titulo LIKE ? OR descripcion LIKE ?)";
                $q = '%' . $filters['q'] . '%';
                $params[] = $q;
                $params[] = $q;
                $types .= 'ss';
            }
            $sql .= " ORDER BY created_at DESC";
            if ($limit > 0) {
                $sql .= " LIMIT ?";
                $params[] = (int)$limit;
                $types .= 'i';
                if ($offset > 0) {
                    $sql .= " OFFSET ?";
                    $params[] = (int)$offset;
                    $types .= 'i';
                }
            }

            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                error_log("prepare obtenerActivos: " . $this->conn->error);
                return [];
            }
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
            return $rows;
        }

        // Obtener servicio por id (incluso inactivo)
        public function obtenerPorId($id) {
            $sql = "SELECT * FROM servicios WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                error_log("prepare obtenerPorId: " . $this->conn->error);
                return null;
            }
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $stmt->close();
            return $row;
        }

        // Agregar servicio
        public function crear($data) {
            $sql = "INSERT INTO servicios (titulo, descripcion, descripcion_corta, categoria, ubicacion, contacto, imagen, precio, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                return ['ok' => false, 'error' => $this->conn->error];
            }
            $status = isset($data['status']) ? (int)$data['status'] : 1;
            $precio = isset($data['precio']) ? (float)$data['precio'] : 0.0;

            // Tipos: titulo(s), descripcion(s), descripcion_corta(s), categoria(s), ubicacion(s),
            // contacto(s), imagen(s), precio(d), status(i) => 'sssssssdi' (7x s, d, i)
            $types = 'sssssssdi';
            $bindParams = [
                $data['titulo'] ?? '',
                $data['descripcion'] ?? '',
                $data['descripcion_corta'] ?? '',
                $data['categoria'] ?? '',
                $data['ubicacion'] ?? '',
                $data['contacto'] ?? '',
                $data['imagen'] ?? '',
                $precio,
                $status
            ];

            $stmt->bind_param($types, ...$bindParams);
            $exec = $stmt->execute();
            if (!$exec) {
                $err = $stmt->error;
                $stmt->close();
                return ['ok' => false, 'error' => $err];
            }
            $id = $stmt->insert_id;
            $stmt->close();
            return ['ok' => true, 'id' => $id];
        }

        // Editar servicio (si 'imagen' no está en $data, no cambia la imagen)
        public function editar($id, $data) {
            $sql = "UPDATE servicios SET titulo = ?, descripcion = ?, descripcion_corta = ?, categoria = ?, ubicacion = ?, contacto = ?, precio = ?, status = ?, updated_at = NOW()";
            $params = [];
            $types = '';

            $params[] = $data['titulo']; $types .= 's';
            $params[] = $data['descripcion']; $types .= 's';
            $params[] = $data['descripcion_corta']; $types .= 's';
            $params[] = $data['categoria']; $types .= 's';
            $params[] = $data['ubicacion']; $types .= 's';
            $params[] = $data['contacto']; $types .= 's';
            $params[] = (float)$data['precio']; $types .= 'd';
            $params[] = (int)$data['status']; $types .= 'i';

            if (!empty($data['imagen'])) {
                $sql .= ", imagen = ?";
                $params[] = $data['imagen']; $types .= 's';
            }

            $sql .= " WHERE id = ?";
            $params[] = (int)$id; $types .= 'i';

            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                return ['ok' => false, 'error' => $this->conn->error];
            }
            $stmt->bind_param($types, ...$params);
            $exec = $stmt->execute();
            if (!$exec) {
                $err = $stmt->error;
                $stmt->close();
                return ['ok' => false, 'error' => $err];
            }
            $affected = $stmt->affected_rows;
            $stmt->close();
            return ['ok' => true, 'affected' => $affected];
        }

        // Eliminación lógica
        public function eliminar($id) {
            $sql = "UPDATE servicios SET status = 0, updated_at = NOW() WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                return ['ok' => false, 'error' => $this->conn->error];
            }
            $stmt->bind_param('i', $id);
            $exec = $stmt->execute();
            if (!$exec) {
                $err = $stmt->error;
                $stmt->close();
                return ['ok' => false, 'error' => $err];
            }
            $stmt->close();
            return ['ok' => true];
        }

        // Obtener categorías y ubicaciones (para filtros)
        public function obtenerCategorias() {
            $sql = "SELECT DISTINCT categoria FROM servicios WHERE categoria IS NOT NULL AND categoria <> ''";
            $res = $this->conn->query($sql);
            $out = [];
            if ($res) {
                while ($r = $res->fetch_assoc()) $out[] = $r['categoria'];
                $res->free();
            }
            return $out;
        }

        public function obtenerUbicaciones() {
            $sql = "SELECT DISTINCT ubicacion FROM servicios WHERE ubicacion IS NOT NULL AND ubicacion <> ''";
            $res = $this->conn->query($sql);
            $out = [];
            if ($res) {
                while ($r = $res->fetch_assoc()) $out[] = $r['ubicacion'];
                $res->free();
            }
            return $out;
        }
    }
}
?>

