<?php
class Usuario {
    public static function findByEmail($pdo, $email) {
        $stmt = $pdo->prepare('SELECT * FROM Usuario WHERE Email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function getAll($pdo) {
        $sql = 'SELECT u.*, r.Nombre AS Rol FROM Usuario u
                LEFT JOIN UsuarioRol ur ON u.idUsuario = ur.Usuario_idUsuario
                LEFT JOIN Rol r ON ur.Rol_idRol = r.idRol';
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function crear($pdo, $data) {
        $stmt = $pdo->prepare('INSERT INTO Usuario (Nombre, ApellidoP, ApellidoM, Email, PasswordHash, Telefono, Activo, Tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $passHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->execute([
            $data['nombre'], $data['apellidop'], $data['apellidom'], $data['email'], $passHash, $data['telefono'], $data['activo'], $data['tipo']
        ]);
        $idUsuario = $pdo->lastInsertId();
        // Asignar rol
        $stmt2 = $pdo->prepare('INSERT INTO UsuarioRol (Usuario_idUsuario, Rol_idRol) VALUES (?, ?)');
        $stmt2->execute([$idUsuario, $data['rol']]);
    }

    public static function editar($pdo, $data) {
        $sql = 'UPDATE Usuario SET Nombre=?, ApellidoP=?, ApellidoM=?, Email=?, Telefono=?, Activo=?, Tipo=? WHERE idUsuario=?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nombre'], $data['apellidop'], $data['apellidom'], $data['email'], $data['telefono'], $data['activo'], $data['tipo'], $data['id']
        ]);
        // Actualizar rol
        $stmt2 = $pdo->prepare('UPDATE UsuarioRol SET Rol_idRol=? WHERE Usuario_idUsuario=?');
        $stmt2->execute([$data['rol'], $data['id']]);
    }

    public static function eliminar($pdo, $id) {
        $stmt = $pdo->prepare('DELETE FROM Usuario WHERE idUsuario=?');
        $stmt->execute([$id]);
    }

    public static function registrarIntento($pdo, $idUsuario, $fallido = true) {
        if ($fallido) {
            $stmt = $pdo->prepare('UPDATE Usuario SET IntentosFallidos = IFNULL(IntentosFallidos,0)+1 WHERE idUsuario = ?');
            $stmt->execute([$idUsuario]);
        } else {
            $stmt = $pdo->prepare('UPDATE Usuario SET IntentosFallidos = 0 WHERE idUsuario = ?');
            $stmt->execute([$idUsuario]);
        }
    }

    public static function bloquearUsuario($pdo, $idUsuario) {
        $stmt = $pdo->prepare('UPDATE Usuario SET BloqueadoHasta = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE idUsuario = ?');
        $stmt->execute([$idUsuario]);
    }
}
?>
