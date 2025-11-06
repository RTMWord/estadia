<?php
// app/models/Testimonio.php
class Testimonio {
    // Si la tabla no existe, la crea (uso seguro para despliegues locales)
    public static function ensureTable($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS testimonio (
            idTestimonio INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Nombre VARCHAR(150) NOT NULL,
            Email VARCHAR(150) DEFAULT NULL,
            Calificacion TINYINT NOT NULL DEFAULT 5,
            Testimonio TEXT NOT NULL,
            Aprobado TINYINT(1) DEFAULT 0,
            FechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($sql);
    }

    public static function crear($pdo, $data) {
        self::ensureTable($pdo);
        $stmt = $pdo->prepare('INSERT INTO testimonio (Nombre, Email, Calificacion, Testimonio, Aprobado) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['nombre'] ?? 'Anonimo',
            $data['email'] ?? null,
            isset($data['calificacion']) ? (int)$data['calificacion'] : 5,
            $data['testimonio'] ?? '',
            isset($data['aprobado']) ? (int)$data['aprobado'] : 0
        ]);
        return $pdo->lastInsertId();
    }

    public static function getPublic($pdo) {
        self::ensureTable($pdo);
        $stmt = $pdo->query('SELECT idTestimonio, Nombre, Email, Calificacion, Testimonio, FechaCreacion FROM testimonio WHERE Aprobado = 1 ORDER BY FechaCreacion DESC');
        return $stmt->fetchAll();
    }

    public static function getAll($pdo) {
        self::ensureTable($pdo);
        $stmt = $pdo->query('SELECT * FROM testimonio ORDER BY FechaCreacion DESC');
        return $stmt->fetchAll();
    }

    public static function aprobar($pdo, $id, $valor = 1) {
        $stmt = $pdo->prepare('UPDATE testimonio SET Aprobado = ? WHERE idTestimonio = ?');
        return $stmt->execute([(int)$valor, (int)$id]);
    }

    public static function eliminar($pdo, $id) {
        $stmt = $pdo->prepare('DELETE FROM testimonio WHERE idTestimonio = ?');
        return $stmt->execute([(int)$id]);
    }
}
?>
