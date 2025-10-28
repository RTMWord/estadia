<?php
class Servicio {
    public static function getAll($pdo) {
        $sql = 'SELECT s.*, a.Nombre AS Agencia FROM Servicio s LEFT JOIN Agencia a ON s.Agencia_idAgencia = a.idAgencia WHERE s.Activo=1';
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }
    public static function crear($pdo, $data) {
        $stmt = $pdo->prepare('INSERT INTO Servicio (Nombre, Descripcion, Costo, Agencia_idAgencia, Activo) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['nombre'], $data['descripcion'], $data['costo'], $data['agencia'], $data['activo']
        ]);
    }
    public static function editar($pdo, $data) {
        $stmt = $pdo->prepare('UPDATE Servicio SET Nombre=?, Descripcion=?, Costo=?, Agencia_idAgencia=?, Activo=? WHERE idServicio=?');
        $stmt->execute([
            $data['nombre'], $data['descripcion'], $data['costo'], $data['agencia'], $data['activo'], $data['id']
        ]);
    }
    public static function eliminar($pdo, $id) {
        $stmt = $pdo->prepare('DELETE FROM Servicio WHERE idServicio=?');
        $stmt->execute([$id]);
    }
}
?>
