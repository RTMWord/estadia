<?php
require_once '../app/config/db.php';
require_once '../app/helpers/auth.php';
requireLogin();
// Obtener servicios activos
$servicios = $pdo->query('SELECT idServicio, Nombre FROM servicio WHERE Activo=1')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Nueva Cita - MetaHogar</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/bs-navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, Arial; background:#f5f7fb; }
        main { min-height: 70vh; display: flex; align-items: center; justify-content: center; }
        .appointment-card { max-width:820px; margin:0 auto; border-radius:12px; box-shadow:0 12px 36px rgba(15,23,42,0.08); overflow:hidden; }
        .appointment-side { background: linear-gradient(180deg,#0d6efd 0%,#0069d9 100%); color:#fff; padding:28px; display:flex; flex-direction:column; justify-content:center; }
        .appointment-side h3 { margin-bottom:8px; font-weight:700; }
        .appointment-side p { opacity:0.95; }
        .form-section { padding:32px 28px; background:#fff; }
        .form-label { font-weight:600; font-size:0.95rem; }
        .btn-primary { background:#0d6efd; border-color:#0d6efd; }
        .help-text { font-size:0.86rem; color:#6c757d; }
        #dateError { display:none; }
        @media (max-width: 767px) {
            main { min-height: 60vh; }
            .appointment-card { margin: 0 0.5rem; }
        }
    </style>
    
    <!-- Widget de Accesibilidad -->
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
    
    <!-- Widget de Accesibilidad -->
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
    <?php include __DIR__ . '/partials/bs-navbar.php'; ?>
    <div style="height: 32px;"></div>
    <main>
        <div class="appointment-card d-flex flex-column flex-md-row">
            <div class="appointment-side col-md-4 text-center">
                <h3>Agendar Cita</h3>
                <p>Elige el servicio, selecciona la fecha y la hora. Te confirmaremos la cita por correo.</p>
                <div class="mt-3">
                    <i class="fa fa-calendar-check fa-2x" aria-hidden="true"></i>
                </div>
                <button class="btn btn-outline-light mt-4" data-bs-toggle="modal" data-bs-target="#misCitasModal"><i class="fa fa-calendar"></i> Ver Mis Citas</button>
            </div>
            <div class="form-section col-md-8">
                <form id="citaForm" method="POST" action="../app/controllers/CitaController.php" novalidate>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Servicio</label>
                            <select name="servicio" class="form-select" required>
                                <?php foreach ($servicios as $s): ?>
                                    <option value="<?= $s['idServicio'] ?>"><?= htmlspecialchars($s['Nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text help-text">Selecciona el tipo de servicio que necesitas.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Fecha y Hora</label>
                            <input type="datetime-local" name="fechahora" class="form-control" required aria-describedby="dateHelp">
                            <div id="dateHelp" class="form-text help-text">No puedes elegir una fecha u hora anterior al momento actual.</div>
                            <div id="dateError" class="alert alert-danger mt-2 d-none" role="alert"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Notas</label>
                            <input name="notas" class="form-control" placeholder="Detalles relevantes (opcional)">
                        </div>
                        <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" name="crear" class="btn btn-primary">Agendar Cita</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <div style="height: 40px;"></div>

        <?php
        // Mostrar solo las citas del usuario logueado en el modal
        $userId = getUserId();
        require_once '../app/models/Cita.php';
        $citas = Cita::getAll($pdo);
        $misCitas = array_filter($citas, function($c) use ($userId) {
                return $c['Usuario_idUsuario'] == $userId;
        });
        ?>
        <!-- Modal -->
        <div class="modal fade" id="misCitasModal" tabindex="-1" aria-labelledby="misCitasLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="misCitasLabel">Mis Citas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (empty($misCitas)): ?>
                            <div class="alert alert-info">No tienes citas agendadas.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Servicio</th>
                                        <th>Fecha y Hora</th>
                                        <th>Estado</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($misCitas as $c): ?>
                                        <tr>
                                            <td><?= $c['idCita'] ?></td>
                                            <td><?= $c['Servicio'] ?></td>
                                            <td><?= $c['FechaHora'] ?></td>
                                            <td><?= $c['Estado'] ?></td>
                                            <td><?= $c['Notas'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function(){
        function pad(n){ return n < 10 ? '0' + n : n; }
        function toLocalInputValue(d){
            return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
        }

        document.addEventListener('DOMContentLoaded', function(){
            var dt = document.querySelector('input[name="fechahora"]');
            var form = document.getElementById('citaForm');
            var dateError = document.getElementById('dateError');
            if(!dt || !form || !dateError) return;

            var now = new Date();
            now.setSeconds(0,0);
            try {
                dt.min = toLocalInputValue(now);
                if (!dt.value) {
                    dt.value = toLocalInputValue(new Date(now.getTime() + 15 * 60000));
                }
            } catch (e) {}

            form.addEventListener('submit', function(ev){
                ev.preventDefault();
                dateError.classList.add('d-none');
                dateError.textContent = '';

                var selected = new Date(dt.value);
                var nowCheck = new Date();
                if (isNaN(selected.getTime()) || selected < nowCheck) {
                    dateError.classList.remove('d-none');
                    dateError.textContent = 'No puedes seleccionar una fecha/hora anterior al momento actual.';
                    dateError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    dt.focus();
                    return;
                }

                var servicioSelect = form.querySelector('select[name="servicio"]');
                var servicioText = servicioSelect ? servicioSelect.options[servicioSelect.selectedIndex].text : '';
                var notas = (form.querySelector('input[name="notas"]') || { value: '' }).value;
                var fechaTexto = dt.value ? dt.value.replace('T', ' ') : '';

                Swal.fire({
                    title: 'Confirmar cita',
                    html: `
                        <div style="text-align:left; padding:10px 20px;">
                            <p><strong>Servicio:</strong> ${servicioText}</p>
                            <p><strong>Fecha y hora:</strong> ${fechaTexto}</p>
                            <p><strong>Notas:</strong> ${notas ? notas : '<em>Ninguna</em>'}</p>
                            <p style="color:#666; font-size:13px; margin-top:8px;">Al confirmar, tu cita será registrada y procesada. Te notificaremos por correo.</p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar cita',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#17466e',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: function(){ return !Swal.isLoading(); },
                    preConfirm: function(){
                        var fd = new FormData(form);
                        if (!fd.has('crear')) fd.append('crear', '1');

                        return fetch(form.action, {
                            method: 'POST',
                            body: fd,
                            credentials: 'same-origin',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(function(res){
                            var contentType = res.headers.get('content-type') || '';
                            if (contentType.indexOf('application/json') !== -1) {
                                return res.json();
                            }
                            return res.text().then(function(text){
                                throw new Error(text && text.trim() ? 'El servidor no devolvio JSON valido.' : 'Respuesta invalida del servidor.');
                            });
                        })
                        .then(function(data){
                            if (!data || !data.ok) {
                                throw new Error(data && data.message ? data.message : 'Error al crear la cita.');
                            }
                            return data;
                        })
                        .catch(function(err){
                            Swal.showValidationMessage('Error: ' + err.message);
                            return false;
                        });
                    }
                }).then(function(result){
                    if (result.isConfirmed && result.value) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cita agendada',
                            html: '<p>' + (result.value.message || 'Tu cita fue registrada correctamente.') + '</p>',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#17466e'
                        });
                        form.reset();
                    }
                });
            });
        });
    })();
    </script>
</body>
</html>
