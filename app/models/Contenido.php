<?php
// app/models/Contenido.php
class Contenido {
    public static function getAll($pdo) {
        $stmt = $pdo->query('SELECT idContenido, Tipo, Titulo, Cuerpo, ImagenPrincipalRuta, FechaPublicacion, Activo FROM contenido ORDER BY FechaPublicacion DESC');
        return $stmt->fetchAll();
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare('SELECT * FROM contenido WHERE idContenido = ? LIMIT 1');
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public static function crear($pdo, $data) {
        $stmt = $pdo->prepare('INSERT INTO contenido (Tipo, Titulo, Cuerpo, AutorUsuario_id, ImagenPrincipalRuta, FechaPublicacion, Activo) VALUES (?, ?, ?, ?, ?, NOW(), ?)');
        $stmt->execute([
            $data['tipo'] ?? 'ARTICULO',
            $data['titulo'] ?? '',
            $data['cuerpo'] ?? '',
            isset($data['autor']) ? (int)$data['autor'] : null,
            $data['imagen_ruta'] ?? null,
            isset($data['activo']) ? (int)$data['activo'] : 0
        ]);
        return $pdo->lastInsertId();
    }

    public static function editar($pdo, $id, $data) {
        $stmt = $pdo->prepare('UPDATE contenido SET Tipo = ?, Titulo = ?, Cuerpo = ?, AutorUsuario_id = ?, ImagenPrincipalRuta = COALESCE(?, ImagenPrincipalRuta), Activo = ? WHERE idContenido = ?');
        return $stmt->execute([
            $data['tipo'] ?? 'ARTICULO',
            $data['titulo'] ?? '',
            $data['cuerpo'] ?? '',
            isset($data['autor']) ? (int)$data['autor'] : null,
            $data['imagen_ruta'] ?? null,
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
