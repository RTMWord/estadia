<?php
// app/controllers/ServicioController.php
// Integración del fragmento procedural al controller orientado a objetos.

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Servicio.php';

class ServicioController {
    protected $db;    // puede ser $conn (mysqli) o $pdo
    protected $model;

    public function __construct() {
        // db.php puede exponer $conn (mysqli) o $pdo (PDO)
        global $conn, $pdo;
        if (!empty($conn)) {
            $this->db = $conn;
        } elseif (!empty($pdo)) {
            $this->db = $pdo;
        } else {
            $this->db = null;
        }

        // Instanciar el modelo sólo si existe la clase y hay una conexión válida.
        // El modelo `Servicio` en este proyecto espera un objeto mysqli ($conn) en el
        // constructor; no intentar instanciar sin parámetros porque provocaría
        // un ArgumentCountError.
        if (class_exists('Servicio') && $this->db !== null) {
            try {
                $this->model = new Servicio($this->db);
            } catch (Throwable $e) {
                // Si por alguna razón falla la instanciación, dejar model en null y
                // registrar el error para diagnóstico.
                error_log('ServicioController::__construct error: ' . $e->getMessage());
                $this->model = null;
            }
        } else {
            $this->model = null;
        }
    }

    // Listado público con filtros (invocado desde public/servicios.php)
    public function indexCatalogo() {
        $categoria = trim($_GET['categoria'] ?? '');
        $ubicacion = trim($_GET['ubicacion'] ?? '');
        $q = trim($_GET['q'] ?? '');

        $filters = [];
        if ($categoria !== '') $filters['categoria'] = $categoria;
        if ($ubicacion !== '') $filters['ubicacion'] = $ubicacion;
        if ($q !== '') $filters['q'] = $q;


        if ($this->model && method_exists($this->model, 'obtenerActivos')) {
            $servicios = $this->model->obtenerActivos($filters);
            $categorias = $this->model->obtenerCategorias();
            $ubicaciones = $this->model->obtenerUbicaciones();
        } else {
            // No hay modelo inicializado: devolver listas vacías y dejar que la
            // capa de presentación maneje la ausencia de datos.
            error_log('ServicioController::indexCatalogo - modelo no inicializado o métodos no disponibles');
            $servicios = [];
            $categorias = [];
            $ubicaciones = [];
        }

        return ['servicios' => $servicios, 'categorias' => $categorias, 'ubicaciones' => $ubicaciones, 'filters' => $filters];
    }

    // Detalle público
    public function detalle($id) {
        if ($this->model && method_exists($this->model, 'obtenerPorId')) {
            echo "<!-- detalle(): model present -->\n";
            try {
                $res = $this->model->obtenerPorId((int)$id);
                if (!empty($res)) {
                    echo "<!-- detalle(): model returned row -->\n";
                    return $res;
                } else {
                    echo "<!-- detalle(): model returned empty -->\n";
                }
            } catch (Throwable $e) {
                error_log('ServicioController::detalle model call failed: ' . $e->getMessage());
                echo "<!-- detalle(): model threw exception: " . htmlspecialchars($e->getMessage()) . " -->\n";
            }
        } else {
            echo "<!-- detalle(): model NOT present -->\n";
        }

        // Fallback: intentar consulta directa con PDO (ayuda diagnóstico y compatibilidad)
        try {
            echo "<!-- detalle(): attempting PDO fallback -->\n";
            global $pdo;
            if (!empty($pdo)) {
                $sql = "SELECT idServicio AS id, Nombre AS titulo, Descripcion AS descripcion, Categoria AS categoria, Ubicacion AS ubicacion, Contacto AS contacto, Costo AS precio, Agencia_idAgencia AS agencia_id, Activo AS status FROM servicio WHERE idServicio = ? LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([(int)$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    echo "<!-- detalle(): PDO fallback returned row -->\n";
                    return $row;
                } else {
                    echo "<!-- detalle(): PDO fallback returned empty -->\n";
                }
            } else {
                echo "<!-- detalle(): PDO not available -->\n";
            }
        } catch (Throwable $e) {
            error_log('ServicioController::detalle fallback PDO error: ' . $e->getMessage());
            echo "<!-- detalle(): PDO fallback exception: " . htmlspecialchars($e->getMessage()) . " -->\n";
        }

        error_log('ServicioController::detalle - servicio no encontrado o modelo no inicializado');
        return null;
    }

    // Crear servicio (admin) - $files = $_FILES, $post = $_POST, $session para permisos
    public function crear($post, $files, $session) {
        if (!$this->esAdmin($session)) {
            return ['ok' => false, 'error' => 'Acceso denegado'];
        }
        $titulo = trim($post['titulo'] ?? '');
        $descripcion = trim($post['descripcion'] ?? '');
        $categoria = trim($post['categoria'] ?? '');
        $ubicacion = trim($post['ubicacion'] ?? '');
        $contacto = trim($post['contacto'] ?? '');
        $agencia = isset($post['agencia']) ? (int)$post['agencia'] : null;
        $precio = isset($post['precio']) ? floatval($post['precio']) : 0;
        $status = isset($post['status']) ? 1 : 0;

        $data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'categoria' => $categoria,
            'ubicacion' => $ubicacion,
            'contacto' => $contacto,
            'agencia' => $agencia,
            'precio' => $precio,
            'status' => $status
        ];

        if ($this->model && method_exists($this->model, 'crear')) {
            return $this->model->crear($data);
        }
        // Modelo no inicializado o método no disponible
        error_log('ServicioController::crear - modelo no inicializado o método crear no disponible');
        return ['ok' => false, 'error' => 'Servicio no disponible'];
    }

    // Editar (admin)
    public function editar($id, $post, $files, $session) {
        if (!$this->esAdmin($session)) {
            return ['ok' => false, 'error' => 'Acceso denegado'];
        }
        $data = [
            'titulo' => trim($post['titulo'] ?? ''),
            'descripcion' => trim($post['descripcion'] ?? ''),
            'categoria' => trim($post['categoria'] ?? ''),
            'ubicacion' => trim($post['ubicacion'] ?? ''),
            'contacto' => trim($post['contacto'] ?? ''),
            'precio' => isset($post['precio']) ? floatval($post['precio']) : 0,
            'agencia' => isset($post['agencia']) ? (int)$post['agencia'] : null,
            'status' => isset($post['status']) ? 1 : 0
        ];

        // No manejamos imágenes: la columna será eliminada de la tabla

        if ($this->model && method_exists($this->model, 'editar')) {
            return $this->model->editar((int)$id, $data);
        }
        error_log('ServicioController::editar - modelo no inicializado o método editar no disponible');
        return ['ok' => false, 'error' => 'Servicio no disponible'];
    }

    // Eliminar (admin)
    public function eliminar($id, $session) {
        if (!$this->esAdmin($session)) {
            return ['ok' => false, 'error' => 'Acceso denegado'];
        }
        if ($this->model && method_exists($this->model, 'eliminar')) {
            return $this->model->eliminar((int)$id);
        }
        error_log('ServicioController::eliminar - modelo no inicializado o método eliminar no disponible');
        return ['ok' => false, 'error' => 'Servicio no disponible'];
    }

    // Verifica admin según sesión. Ajusta según tu sistema (AuthController).
    protected function esAdmin($session) {
        // Prefer explicit session flags if present
        if (!empty($session)) {
            if (!empty($session['role']) && ($session['role'] === 'admin' || $session['role'] === 'administrador')) return true;
            if (!empty($session['user_role']) && ($session['user_role'] === 'admin' || $session['user_role'] === 'administrador')) return true;
            if (!empty($session['is_admin']) && $session['is_admin'] == true) return true;
        }

        // Fallback: check DB mapping Usuario -> UsuarioRol -> Rol
        $userId = $session['user_id'] ?? $_SESSION['user_id'] ?? null;
        if ($userId) {
            try {
                global $pdo;
                if (!empty($pdo)) {
                    $stm = $pdo->prepare('SELECT r.Nombre FROM UsuarioRol ur JOIN Rol r ON ur.Rol_idRol = r.idRol WHERE ur.Usuario_idUsuario = ? LIMIT 1');
                    $stm->execute([$userId]);
                    $rol = $stm->fetchColumn();
                    if ($rol === 'administrador' || $rol === 'admin') return true;
                }
            } catch (Throwable $e) {
                error_log('ServicioController::esAdmin DB check failed: ' . $e->getMessage());
            }
        }

        return false;
    }
}

// Procedural handler (integra el fragmento original).
// Este bloque permite que envíos desde formularios sigan redirigiendo igual que antes.
$controller = new ServicioController();

// Crear
if (isset($_POST['crear'])) {
    $res = $controller->crear($_POST, $_FILES, $_SESSION);
    // Puedes verificar $res['ok'] si deseas manejar errores.
    header('Location: ../../public/admin/servicios.php');
    exit;
}

// Editar
if (isset($_POST['editar'])) {
    $id = $_POST['id'] ?? null;
    $res = $controller->editar($id, $_POST, $_FILES, $_SESSION);
    header('Location: ../../public/admin/servicios.php');
    exit;
}

// Eliminar (viene por GET en el fragmento original)
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $res = $controller->eliminar($id, $_SESSION);
    header('Location: ../../public/admin/servicios.php');
    exit;
}

