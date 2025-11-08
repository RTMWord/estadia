<?php
// app/models/Multimedia.php
class Multimedia {
    public static function getAll($pdo) {
        $stmt = $pdo->query('SELECT idMedia, Contenido_idContenido, Tipo, Ruta, Descripcion FROM multimedia ORDER BY idMedia DESC');
        return $stmt->fetchAll();
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare('SELECT * FROM multimedia WHERE idMedia = ? LIMIT 1');
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public static function crear($pdo, $data) {
        $stmt = $pdo->prepare('INSERT INTO multimedia (Contenido_idContenido, Tipo, Ruta, Descripcion) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            isset($data['contenido_id']) ? (int)$data['contenido_id'] : null,
            $data['tipo'] ?? 'IMAGEN',
            $data['ruta'] ?? '',
            $data['descripcion'] ?? ''
        ]);
        return $pdo->lastInsertId();
    }

    public static function eliminar($pdo, $id) {
        $stmt = $pdo->prepare('DELETE FROM multimedia WHERE idMedia = ?');
        return $stmt->execute([(int)$id]);
    }
}
?>
