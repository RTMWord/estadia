<?php
require_once __DIR__ . '/../models/Pregunta.php';
require_once __DIR__ . '/../models/Respuesta.php';

class ComunidadController {
    private $pdo;
    private $preguntaModel;
    private $respuestaModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->preguntaModel = new Pregunta($pdo);
        $this->respuestaModel = new Respuesta($pdo);
    }

    /**
     * Listar incidencias (preguntas)
     */
    public function index($orderBy = 'FechaRegistro', $limit = 20) {
        return $this->preguntaModel->getAll($limit, 0, $orderBy);
    }

    /**
     * Ver detalle de incidencia (pregunta)
     */
    public function ver($idIncidencia) {
        $incidencia = $this->preguntaModel->getById($idIncidencia);
        if (!$incidencia) {
            return null;
        }

        // Incrementar vistas
        $this->preguntaModel->incrementViews($idIncidencia);

        // Obtener respuestas
        $incidencia['respuestas'] = $this->respuestaModel->getByIncidencia($idIncidencia);

        return $incidencia;
    }

    /**
     * Crear incidencia (pregunta)
     */
    public function crearPregunta($usuarioId, $titulo, $cuerpo) {
        // Validar
        if (empty($titulo) || strlen($titulo) < 10) {
            throw new Exception('El título debe tener al menos 10 caracteres');
        }
        if (empty($cuerpo) || strlen($cuerpo) < 20) {
            throw new Exception('El cuerpo debe tener al menos 20 caracteres');
        }

        return $this->preguntaModel->create($usuarioId, $titulo, $cuerpo);
    }

    /**
     * Crear respuesta
     */
    public function crearRespuesta($idIncidencia, $usuarioId, $cuerpo) {
        if (empty($cuerpo) || strlen($cuerpo) < 10) {
            throw new Exception('La respuesta debe tener al menos 10 caracteres');
        }

        return $this->respuestaModel->create($idIncidencia, $usuarioId, $cuerpo);
    }

    /**
     * Votar incidencia
     */
    public function votarPregunta($idIncidencia, $usuarioId, $valor) {
        if ($valor != 1 && $valor != -1) {
            throw new Exception('Valor de voto inválido');
        }
        return $this->preguntaModel->votar($idIncidencia, $usuarioId, $valor);
    }

    /**
     * Votar respuesta
     */
    public function votarRespuesta($idRespuesta, $usuarioId, $valor) {
        if ($valor != 1 && $valor != -1) {
            throw new Exception('Valor de voto inválido');
        }
        return $this->respuestaModel->votar($idRespuesta, $usuarioId, $valor);
    }

    /**
     * Aceptar respuesta
     */
    public function aceptarRespuesta($idIncidencia, $idRespuesta, $usuarioId) {
        return $this->preguntaModel->marcarResuelta($idIncidencia, $idRespuesta, $usuarioId);
    }

    /**
     * Obtener estadísticas
     */
    public function getEstadisticas() {
        return $this->preguntaModel->getEstadisticas();
    }

    /**
     * Obtener etiquetas populares (simuladas, ya que no usamos tabla de etiquetas)
     */
    public function getEtiquetasPopulares($limit = 15) {
        // Etiquetas predefinidas sin BD
        $etiquetas = [
            ['Nombre' => 'configuración', 'UsosCount' => 45],
            ['Nombre' => 'seguridad', 'UsosCount' => 38],
            ['Nombre' => 'wifi', 'UsosCount' => 31],
            ['Nombre' => 'instalación', 'UsosCount' => 28],
            ['Nombre' => 'energía', 'UsosCount' => 25],
            ['Nombre' => 'app-móvil', 'UsosCount' => 22],
            ['Nombre' => 'sensores', 'UsosCount' => 19],
            ['Nombre' => 'automatización', 'UsosCount' => 18],
            ['Nombre' => 'iluminación', 'UsosCount' => 16],
            ['Nombre' => 'voz', 'UsosCount' => 14],
            ['Nombre' => 'alexa', 'UsosCount' => 12],
            ['Nombre' => 'google-home', 'UsosCount' => 11],
            ['Nombre' => 'hogar-inteligente', 'UsosCount' => 35],
            ['Nombre' => 'conexión', 'UsosCount' => 20],
            ['Nombre' => 'integraciones', 'UsosCount' => 17],
        ];
        
        return array_slice($etiquetas, 0, $limit);
    }
}
