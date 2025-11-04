<?php
class Sugerencia {
    public static function crear($pdo, $data) {
        $stmt = $pdo->prepare('INSERT INTO Sugerencia (Usuario_idUsuario, Titulo, Descripcion, Estado) VALUES (?, ?, ?, ?)');
        $usuarioId = $data['usuario_id'] ?? null;
        $stmt->execute([$usuarioId, $data['titulo'], $data['descripcion'], 'PENDIENTE']);
    }
    public static function getAll($pdo) {
        $sql = 'SELECT s.*, u.Nombre AS UsuarioNombre, u.Email AS UsuarioEmail FROM Sugerencia s LEFT JOIN Usuario u ON s.Usuario_idUsuario = u.idUsuario ORDER BY s.FechaRegistro DESC';
        return $pdo->query($sql)->fetchAll();
    }
    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare('SELECT s.*, u.Nombre AS UsuarioNombre, u.Email AS UsuarioEmail FROM Sugerencia s LEFT JOIN Usuario u ON s.Usuario_idUsuario = u.idUsuario WHERE s.idSugerencia = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function updateEstado($pdo, $id, $estado) {
        $stmt = $pdo->prepare('UPDATE Sugerencia SET Estado = ? WHERE idSugerencia = ?');
        $stmt->execute([$estado, $id]);
    }
    public static function eliminar($pdo, $id) {
        $stmt = $pdo->prepare('DELETE FROM Sugerencia WHERE idSugerencia = ?');
        $stmt->execute([$id]);
    }
}
?>
