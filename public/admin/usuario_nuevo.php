<?php
require_once '../../app/config/db.php';
require_once '../../app/models/Usuario.php';
// Obtener roles
$roles = $pdo->query('SELECT * FROM Rol')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario - MetaHogar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.userway.org/widget.js" data-account="kjnkkEfZy1"></script>
    <style>
        .userway-icon {
            position: fixed !important;
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 9999 !important;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/partials/admin_nav.php'; ?>
    <div class="container py-5">
        <h2 class="text-primary mb-4">Crear Nuevo Usuario</h2>
        <div class="alert alert-info">
            <strong>Requisitos de campos:</strong>
            <ul class="mb-0">
                <li>Nombre y apellidos: solo letras, espacios, guiones o apóstrofes; máximo 50 caracteres.</li>
                <li>Teléfono: solo números; exactamente 10 dígitos.</li>
            </ul>
        </div>
        <form id="userCreateForm" method="POST" action="../../app/controllers/UserController.php">
            <div id="userFormErrors" class="alert alert-danger d-none"></div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input id="nombre" type="text" name="nombre" class="form-control" maxlength="50" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Paterno</label>
                    <input id="apellidop" type="text" name="apellidop" class="form-control" maxlength="50" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apellido Materno</label>
                    <input id="apellidom" type="text" name="apellidom" class="form-control" maxlength="50">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input id="telefono" type="text" name="telefono" class="form-control" maxlength="10" pattern="\d{10}" inputmode="numeric">
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="externo">Externo</option>
                    <option value="interno">Interno</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Activo</label>
                <select name="activo" class="form-select">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['idRol'] ?>"><?= $rol['Nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="crear" class="btn btn-success">Crear Usuario</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script>
    (function(){
        var form = document.getElementById('userCreateForm');
        if(!form) return;
        var errorsDiv = document.getElementById('userFormErrors');
        form.addEventListener('submit', function(e){
            errorsDiv.classList.add('d-none'); errorsDiv.innerHTML = '';
            var nombre = (document.getElementById('nombre').value || '').trim();
            var apellidop = (document.getElementById('apellidop').value || '').trim();
            var apellidom = (document.getElementById('apellidom').value || '').trim();
            var telefono = (document.getElementById('telefono').value || '').trim();
            var nameRegex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ'\-\s]+$/;
            var phoneRegex = /^[0-9]{10}$/;
            var errors = [];
            if(!nameRegex.test(nombre)) errors.push('Nombre sólo debe contener letras, espacios, guiones o apóstrofes.');
            if(!nameRegex.test(apellidop)) errors.push('Apellido Paterno sólo debe contener letras, espacios, guiones o apóstrofes.');
            if(apellidom && !nameRegex.test(apellidom)) errors.push('Apellido Materno sólo debe contener letras, espacios, guiones o apóstrofes.');
            if(telefono && !phoneRegex.test(telefono)) errors.push('Teléfono debe ser numérico y tener exactamente 10 dígitos.');
            if(errors.length){
                e.preventDefault();
                errorsDiv.innerHTML = errors.map(function(x){ return '<div>' + x + '</div>'; }).join('');
                errorsDiv.classList.remove('d-none');
                window.scrollTo(0, errorsDiv.getBoundingClientRect().top + window.pageYOffset - 20);
            }
        });
    })();
    </script>
</body>
</html>
