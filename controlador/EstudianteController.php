<?php
// Configuración de errores (solo para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 0); // IMPORTANTE: Cambiar a 0 en producción

// Función para manejar errores y devolver JSON
function sendJsonError($message, $code = 500)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => $message
    ]);
    exit();
}

// Manejar errores fatales
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        sendJsonError("Error interno del servidor");
    }
});

// Manejar excepciones no capturadas
set_exception_handler(function ($exception) {
    sendJsonError("Error del sistema: " . $exception->getMessage());
});

try {
    session_start();

    // Verificar sesión antes de cargar el modelo
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
        sendJsonError('No autorizado', 401);
    }

    // Cargar modelo con manejo de errores
    if (!file_exists('../modelo/EstudianteModel.php')) {
        throw new Exception("Archivo del modelo no encontrado");
    }

    require_once '../modelo/EstudianteModel.php';

    class EstudianteController
    {
        private $model;

        public function __construct()
        {
            try {
                $this->model = new EstudianteModel();
            } catch (Exception $e) {
                throw new Exception("Error al inicializar el modelo: " . $e->getMessage());
            }
        }

        /**
         * Obtener todas las salas disponibles
         */
        public function obtenerSalas()
        {
            try {
                $salas = $this->model->getSalasDisponibles();
                $this->sendJsonSuccess(['salas' => $salas]);
            } catch (Exception $e) {
                sendJsonError("Error al obtener salas: " . $e->getMessage());
            }
        }

        /**
         * Solicitar/Crear una nueva reserva
         */
        public function solicitarReserva()
        {
            try {
                // Validar datos requeridos
                if (
                    !isset($_POST['id_sala']) || !isset($_POST['fecha']) ||
                    !isset($_POST['hora_inicio']) || !isset($_POST['hora_fin'])
                ) {
                    sendJsonError('Datos incompletos', 400);
                }

                $id_usuario = $_SESSION['user_id'];
                $id_sala = $_POST['id_sala'];
                $fecha = $_POST['fecha'];
                $hora_inicio = $_POST['hora_inicio'];
                $hora_fin = $_POST['hora_fin'];

                // Validar formato de fecha y hora
                if (!$this->validarFecha($fecha)) {
                    sendJsonError('Formato de fecha inválido. Use YYYY-MM-DD', 400);
                }

                if (!$this->validarHora($hora_inicio) || !$this->validarHora($hora_fin)) {
                    sendJsonError('Formato de hora inválido. Use HH:MM', 400);
                }

                // Validar que la hora de fin sea mayor que la hora de inicio
                if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
                    sendJsonError('La hora de fin debe ser mayor que la hora de inicio', 400);
                }

                // Verificar disponibilidad antes de intentar crear
                $disponible = $this->model->verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin);

                if (!$disponible) {
                    sendJsonError('La sala no está disponible en ese horario', 400);
                }

                // Crear la reserva usando el procedimiento almacenado
                $resultado = $this->model->crearReserva($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin);

                if ($resultado['success']) {
                    $this->sendJsonSuccess([
                        'mensaje' => $resultado['mensaje'],
                        'id_reserva' => $resultado['id_reserva'] ?? null
                    ]);
                } else {
                    sendJsonError($resultado['mensaje'] ?? 'Error al crear reserva');
                }

            } catch (Exception $e) {
                sendJsonError("Error al crear reserva: " . $e->getMessage());
            }
        }

        /**
         * Obtener todas las reservas del estudiante
         */
        public function obtenerMisReservas()
        {
            try {
                $id_usuario = $_SESSION['user_id'];
                $reservas = $this->model->getReservasPorEstudiante($id_usuario);
                $this->sendJsonSuccess(['reservas' => $reservas]);
            } catch (Exception $e) {
                sendJsonError("Error al obtener reservas: " . $e->getMessage());
            }
        }

        /**
         * Cancelar una reserva
         */
        public function cancelarReserva()
        {
            try {
                if (!isset($_POST['id_reserva'])) {
                    sendJsonError('ID de reserva no proporcionado', 400);
                }

                $id_reserva = $_POST['id_reserva'];
                $id_usuario = $_SESSION['user_id'];

                // Cancelar usando el procedimiento almacenado
                $resultado = $this->model->cancelarReserva($id_reserva, $id_usuario);

                if ($resultado['success']) {
                    $this->sendJsonSuccess(['mensaje' => $resultado['mensaje']]);
                } else {
                    sendJsonError($resultado['mensaje'] ?? 'Error al cancelar reserva');
                }

            } catch (Exception $e) {
                sendJsonError("Error al cancelar reserva: " . $e->getMessage());
            }
        }

        /**
         * Verificar disponibilidad de una sala
         */
        public function verificarDisponibilidad()
        {
            try {
                if (
                    !isset($_POST['id_sala']) || !isset($_POST['fecha']) ||
                    !isset($_POST['hora_inicio']) || !isset($_POST['hora_fin'])
                ) {
                    sendJsonError('Datos incompletos', 400);
                }

                $id_sala = $_POST['id_sala'];
                $fecha = $_POST['fecha'];
                $hora_inicio = $_POST['hora_inicio'];
                $hora_fin = $_POST['hora_fin'];

                $disponible = $this->model->verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin);

                $this->sendJsonSuccess([
                    'disponible' => $disponible,
                    'mensaje' => $disponible ? 'Sala disponible' : 'Sala no disponible en ese horario'
                ]);

            } catch (Exception $e) {
                sendJsonError("Error al verificar disponibilidad: " . $e->getMessage());
            }
        }

        /**
         * Obtener información de una sala específica
         */
        public function obtenerSala()
        {
            try {
                if (!isset($_GET['id_sala'])) {
                    sendJsonError('ID de sala no proporcionado', 400);
                }

                $id_sala = $_GET['id_sala'];
                $sala = $this->model->getSalaPorId($id_sala);

                if ($sala) {
                    $this->sendJsonSuccess(['sala' => $sala]);
                } else {
                    sendJsonError('Sala no encontrada', 404);
                }

            } catch (Exception $e) {
                sendJsonError("Error al obtener sala: " . $e->getMessage());
            }
        }

        /**
         * Enviar respuesta JSON exitosa
         */
        private function sendJsonSuccess($data = [])
        {
            header('Content-Type: application/json');
            echo json_encode(array_merge(['success' => true], $data));
            exit();
        }

        /**
         * Validar formato de fecha (YYYY-MM-DD)
         */
        private function validarFecha($fecha)
        {
            $d = DateTime::createFromFormat('Y-m-d', $fecha);
            return $d && $d->format('Y-m-d') === $fecha;
        }

        /**
         * Validar formato de hora (HH:MM o HH:MM:SS)
         */
        private function validarHora($hora)
        {
            return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $hora);
        }
    }

    // Manejo de rutas y acciones
    $action = $_GET['action'] ?? '';

    // Validar acción
    if (empty($action)) {
        sendJsonError('Acción no especificada', 400);
    }

    $controller = new EstudianteController();
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($action) {
        case 'salas':
            if ($method === 'GET') {
                $controller->obtenerSalas();
            } else {
                sendJsonError('Método no permitido', 405);
            }
            break;

        case 'solicitar':
            if ($method === 'POST') {
                $controller->solicitarReserva();
            } else {
                sendJsonError('Método no permitido', 405);
            }
            break;

        case 'mis_reservas':
            if ($method === 'GET') {
                $controller->obtenerMisReservas();
            } else {
                sendJsonError('Método no permitido', 405);
            }
            break;

        case 'cancelar':
            if ($method === 'POST') {
                $controller->cancelarReserva();
            } else {
                sendJsonError('Método no permitido', 405);
            }
            break;

        case 'verificar_disponibilidad':
            if ($method === 'POST') {
                $controller->verificarDisponibilidad();
            } else {
                sendJsonError('Método no permitido', 405);
            }
            break;

        case 'sala':
            if ($method === 'GET') {
                $controller->obtenerSala();
            } else {
                sendJsonError('Método no permitido', 405);
            }
            break;

        default:
            sendJsonError('Acción no válida: ' . $action, 404);
            break;
    }

} catch (Exception $e) {
    sendJsonError("Error interno: " . $e->getMessage());
}
?>