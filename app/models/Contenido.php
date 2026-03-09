<?php
// app/models/Contenido.php
class Contenido {
    public static function getAll($pdo) {
        // Evitar seleccionar columnas que pueden no existir en algunas instalaciones.
        $stmt = $pdo->query('SELECT idContenido, Tipo, Titulo, Cuerpo, FechaPublicacion, Activo FROM contenido ORDER BY FechaPublicacion DESC');
        return $stmt->fetchAll();
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare('SELECT * FROM contenido WHERE idContenido = ? LIMIT 1');
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public static function crear($pdo, $data) {
        // La tabla `contenido` no tiene columna para ruta de imagen en esta instalación.
        // Insertar sólo las columnas existentes.
        $stmt = $pdo->prepare('INSERT INTO contenido (Tipo, Titulo, Cuerpo, AutorUsuario_id, FechaPublicacion, Activo) VALUES (?, ?, ?, ?, NOW(), ?)');
        $stmt->execute([
            $data['tipo'] ?? 'ARTICULO',
            $data['titulo'] ?? '',
            $data['cuerpo'] ?? '',
            isset($data['autor']) ? (int)$data['autor'] : null,
            isset($data['activo']) ? (int)$data['activo'] : 0
        ]);
        return $pdo->lastInsertId();
    }

    public static function editar($pdo, $id, $data) {
        // Actualizar sólo columnas existentes en la tabla `contenido`.
        $stmt = $pdo->prepare('UPDATE contenido SET Tipo = ?, Titulo = ?, Cuerpo = ?, AutorUsuario_id = ?, Activo = ? WHERE idContenido = ?');
        return $stmt->execute([
            $data['tipo'] ?? 'ARTICULO',
            $data['titulo'] ?? '',
            $data['cuerpo'] ?? '',
            isset($data['autor']) ? (int)$data['autor'] : null,
            isset($data['activo']) ? (int)$data['activo'] : 0,
            (int)$id
        ]);
    }

    public static function eliminar($pdo, $id) {
        // eliminación lógica
        $stmt = $pdo->prepare('UPDATE contenido SET Activo = 0 WHERE idContenido = ?');
        return $stmt->execute([(int)$id]);
    }

    public static function setActivo($pdo, $id, $activo) {
        $stmt = $pdo->prepare('UPDATE contenido SET Activo = ? WHERE idContenido = ?');
        return $stmt->execute([(int)$activo, (int)$id]);
    }
}
?>
