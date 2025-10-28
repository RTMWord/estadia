<?php
class Cita {
    public static function getAll($pdo) {
        $sql = 'SELECT c.*, u.Nombre AS Usuario, s.Nombre AS Servicio FROM Cita c
                INNER JOIN Usuario u ON c.Usuario_idUsuario = u.idUsuario
                INNER JOIN Servicio s ON c.Servicio_idServicio = s.idServicio';
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }
    public static function crear($pdo, $data) {
        $stmt = $pdo->prepare('INSERT INTO Cita (Usuario_idUsuario, Servicio_idServicio, FechaHora, Estado, Notas) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['usuario'], $data['servicio'], $data['fechahora'], $data['estado'], $data['notas']
        ]);
    }
    public static function editar($pdo, $data) {
        $stmt = $pdo->prepare('UPDATE Cita SET Servicio_idServicio=?, FechaHora=?, Estado=?, Notas=? WHERE idCita=?');
        $stmt->execute([
            $data['servicio'], $data['fechahora'], $data['estado'], $data['notas'], $data['id']
        ]);
    }
    public static function eliminar($pdo, $id) {
        $stmt = $pdo->prepare('DELETE FROM Cita WHERE idCita=?');
        $stmt->execute([$id]);
    }
}
?>
