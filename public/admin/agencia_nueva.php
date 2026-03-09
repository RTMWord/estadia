<?php
require_once '../../app/config/db.php';
require_once '../../app/helpers/auth.php';
requireRole($pdo, 'administrador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registrar Agencia - MetaHogar</title>
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
        <h2 class="mb-4">Registrar Nueva Agencia</h2>
        <div class="alert alert-info">
            <strong>Requisitos de campos:</strong>
            <ul class="mb-0">
                <li>Nombre (agencia): puede contener letras y números, espacios, guiones o apóstrofes; máximo 50 caracteres.</li>
                <li>Contacto (persona responsable): sólo letras, espacios, guiones o apóstrofes; máximo 50 caracteres.</li>
                <li>Teléfono: sólo números; exactamente 10 dígitos.</li>
            </ul>
        </div>
        <form id="agencyCreateForm" method="post" action="../../app/controllers/AgenciaController.php">
            <div id="agencyFormErrors" class="alert alert-danger d-none"></div>
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input id="nombre" name="nombre" class="form-control" maxlength="50" pattern="[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ'\-\s]+" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contacto</label>
                <input id="contacto" name="contacto" class="form-control" maxlength="50" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ'\-\s]+">
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input id="telefono" name="telefono" class="form-control" maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input name="direccion" class="form-control">
            </div>
            <button name="crear" class="btn btn-primary">Crear</button>
            <a href="agencias.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <script>
    (function(){
        var form = document.getElementById('agencyCreateForm');
        if(!form) return;
        var errorsDiv = document.getElementById('agencyFormErrors');
        form.addEventListener('submit', function(e){
            errorsDiv.classList.add('d-none'); errorsDiv.innerHTML = '';
            var nombre = (document.getElementById('nombre').value || '').trim();
            var contacto = (document.getElementById('contacto').value || '').trim();
            var telefono = (document.getElementById('telefono').value || '').trim();
            var nameRegexAgency = /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ'\-\s]+$/;
            var nameRegexPerson = /^[A-Za-zÁÉÍÓÚáéíóúÑñ'\-\s]+$/;
            var phoneRegex = /^[0-9]{10}$/;
            var errors = [];
            if(!nameRegexAgency.test(nombre)) errors.push('Nombre (agencia) sólo debe contener letras, números, espacios, guiones o apóstrofes.');
            if(contacto && !nameRegexPerson.test(contacto)) errors.push('Contacto sólo debe contener letras, espacios, guiones o apóstrofes.');
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