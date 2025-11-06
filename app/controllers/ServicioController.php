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
            return $this->model->obtenerPorId((int)$id);
        }
        error_log('ServicioController::detalle - modelo no inicializado o método obtenerPorId no disponible');
        return null;
    }

    // Crear servicio (admin) - $files = $_FILES, $post = $_POST, $session para permisos
    public function crear($post, $files, $session) {
        if (!$this->esAdmin($session)) {
            return ['ok' => false, 'error' => 'Acceso denegado'];
        }
        $titulo = trim($post['titulo'] ?? '');
        $descripcion = trim($post['descripcion'] ?? '');
        $descripcion_corta = trim($post['descripcion_corta'] ?? '');
        $categoria = trim($post['categoria'] ?? '');
        $ubicacion = trim($post['ubicacion'] ?? '');
        $contacto = trim($post['contacto'] ?? '');
        $precio = isset($post['precio']) ? floatval($post['precio']) : 0;
        $status = isset($post['status']) ? 1 : 0;

        $imagen_ruta = '';
        if (!empty($files['imagen']['name'])) {
            $upload_dir = __DIR__ . '/../../public/assets/images';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $ext = pathinfo($files['imagen']['name'], PATHINFO_EXTENSION);
            $new = 'serv_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $upload_dir . '/' . $new;
            if (move_uploaded_file($files['imagen']['tmp_name'], $dest)) {
                $imagen_ruta = 'assets/images/' . $new; // ruta relativa a public/
            }
        }

        $data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'descripcion_corta' => $descripcion_corta,
            'categoria' => $categoria,
            'ubicacion' => $ubicacion,
            'contacto' => $contacto,
            'imagen' => $imagen_ruta,
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
            'descripcion_corta' => trim($post['descripcion_corta'] ?? ''),
            'categoria' => trim($post['categoria'] ?? ''),
            'ubicacion' => trim($post['ubicacion'] ?? ''),
            'contacto' => trim($post['contacto'] ?? ''),
            'precio' => isset($post['precio']) ? floatval($post['precio']) : 0,
            'status' => isset($post['status']) ? 1 : 0
        ];

        if (!empty($files['imagen']['name'])) {
            $upload_dir = __DIR__ . '/../../public/assets/images';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $ext = pathinfo($files['imagen']['name'], PATHINFO_EXTENSION);
            $new = 'serv_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = $upload_dir . '/' . $new;
            if (move_uploaded_file($files['imagen']['tmp_name'], $dest)) {
                $data['imagen'] = 'assets/images/' . $new;
            }
        }

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
        if (empty($session)) return false;
        if (!empty($session['role']) && $session['role'] === 'admin') return true;
        if (!empty($session['user_role']) && $session['user_role'] === 'admin') return true;
        if (!empty($session['is_admin']) && $session['is_admin'] == true) return true;
        return false;
    }
}

// Procedural handler (integra el fragmento original).
// Este bloque permite que envíos desde formularios sigan redirigiendo igual que antes.
session_start();
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

