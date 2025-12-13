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
                <form method="POST" action="../app/controllers/CitaController.php?redir=1" novalidate>
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

    <script>
    (function(){
        function pad(n){return n<10? '0'+n : n}
        function toLocalInputValue(d){
            return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
        }
        document.addEventListener('DOMContentLoaded', function(){
            var dt = document.querySelector('input[name="fechahora"]');
            if(!dt) return;
            var now = new Date();
            now.setSeconds(0,0);
            var min = now; // evitar seleccionar una fecha/hora anterior al momento actual
            try{
                dt.min = toLocalInputValue(min);
                if(!dt.value) dt.value = toLocalInputValue(new Date(min.getTime() + 15*60000));
            }catch(e){ /* algunos navegadores antiguos pueden fallar */ }

            var form = dt.closest('form');
            var dateError = document.getElementById('dateError');
            form.addEventListener('submit', function(ev){
                var selected = new Date(dt.value);
                var nowCheck = new Date();
                if(isNaN(selected.getTime()) || selected < nowCheck){
                    ev.preventDefault();
                    dateError.classList.remove('d-none');
                    dateError.textContent = 'No puedes seleccionar una fecha/hora anterior al momento actual.';
                    dateError.scrollIntoView({behavior:'smooth', block:'center'});
                    dt.focus();
                }
            });
        });
    })();
    </script>
</body>
</html>
