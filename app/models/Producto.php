<?php
// app/models/Producto.php
class Producto {
    public static function getAll($pdo) {
        $stmt = $pdo->query('SELECT idProducto, Nombre, Descripcion, Precio, Existencia, Activo FROM producto ORDER BY idProducto DESC');
        return $stmt->fetchAll();
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare('SELECT * FROM producto WHERE idProducto = ? LIMIT 1');
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public static function crear($pdo, $data) {
        $stmt = $pdo->prepare('INSERT INTO producto (Nombre, Descripcion, Precio, Existencia, Activo) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['nombre'] ?? '',
            $data['descripcion'] ?? '',
            isset($data['precio']) ? (float)$data['precio'] : 0.0,
            isset($data['existencia']) ? (int)$data['existencia'] : 0,
            isset($data['activo']) ? 1 : 0
        ]);
        return $pdo->lastInsertId();
    }

    public static function editar($pdo, $id, $data) {
        $stmt = $pdo->prepare('UPDATE producto SET Nombre = ?, Descripcion = ?, Precio = ?, Existencia = ?, Activo = ? WHERE idProducto = ?');
        return $stmt->execute([
            $data['nombre'] ?? '',
            $data['descripcion'] ?? '',
            isset($data['precio']) ? (float)$data['precio'] : 0.0,
            isset($data['existencia']) ? (int)$data['existencia'] : 0,
            isset($data['activo']) ? 1 : 0,
            (int)$id
        ]);
    }

    public static function eliminar($pdo, $id) {
        // Eliminación lógica
        $stmt = $pdo->prepare('UPDATE producto SET Activo = 0 WHERE idProducto = ?');
        return $stmt->execute([(int)$id]);
    }
}
?>
