<?php
session_start();
require_once '../modelo/EstudianteModel.php';

class EstudianteController
{
    private $model;

    public function __construct()
    {
        $this->model = new EstudianteModel();

        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'mensaje' => 'No autorizado']);
            exit();
        }
    }

    /**
     * Obtener todas las salas disponibles
     */
    public function obtenerSalas()
    {
        $salas = $this->model->getSalasDisponibles();
        echo json_encode(['success' => true, 'salas' => $salas]);
    }

    /**
     * Solicitar/Crear una nueva reserva
     */
    public function solicitarReserva()
    {
        // Validar datos requeridos
        if (
            !isset($_POST['id_sala']) || !isset($_POST['fecha']) ||
            !isset($_POST['hora_inicio']) || !isset($_POST['hora_fin'])
        ) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Datos incompletos'
            ]);
            return;
        }

        $id_usuario = $_SESSION['user_id'];
        $id_sala = $_POST['id_sala'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];

        // Validar formato de fecha y hora
        if (!$this->validarFecha($fecha)) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Formato de fecha inválido'
            ]);
            return;
        }

        if (!$this->validarHora($hora_inicio) || !$this->validarHora($hora_fin)) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Formato de hora inválido'
            ]);
            return;
        }

        // Validar que la hora de fin sea mayor que la hora de inicio
        if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'La hora de fin debe ser mayor que la hora de inicio'
            ]);
            return;
        }

        // Verificar disponibilidad antes de intentar crear
        $disponible = $this->model->verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin);

        if (!$disponible) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'La sala no está disponible en ese horario'
            ]);
            return;
        }

        // Crear la reserva usando el procedimiento almacenado
        $resultado = $this->model->crearReserva($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin);
        echo json_encode($resultado);
    }

    /**
     * Obtener todas las reservas del estudiante
     */
    public function obtenerMisReservas()
    {
        $id_usuario = $_SESSION['user_id'];
        $reservas = $this->model->getReservasPorEstudiante($id_usuario);
        echo json_encode(['success' => true, 'reservas' => $reservas]);
    }

    /**
     * Obtener reservas próximas del estudiante
     */
    public function obtenerReservasProximas()
    {
        $id_usuario = $_SESSION['user_id'];
        $reservas = $this->model->getReservasProximas($id_usuario);
        echo json_encode(['success' => true, 'reservas' => $reservas]);
    }

    /**
     * Obtener historial de reservas
     */
    public function obtenerHistorial()
    {
        $id_usuario = $_SESSION['user_id'];
        $reservas = $this->model->getHistorialReservas($id_usuario);
        echo json_encode(['success' => true, 'reservas' => $reservas]);
    }

    /**
     * Cancelar una reserva
     */
    public function cancelarReserva()
    {
        if (!isset($_POST['id_reserva'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'ID de reserva no proporcionado'
            ]);
            return;
        }

        $id_reserva = $_POST['id_reserva'];
        $id_usuario = $_SESSION['user_id'];

        // Cancelar usando el procedimiento almacenado
        $resultado = $this->model->cancelarReserva($id_reserva, $id_usuario);
        echo json_encode($resultado);
    }

    /**
     * Verificar disponibilidad de una sala
     */
    public function verificarDisponibilidad()
    {
        if (
            !isset($_POST['id_sala']) || !isset($_POST['fecha']) ||
            !isset($_POST['hora_inicio']) || !isset($_POST['hora_fin'])
        ) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Datos incompletos'
            ]);
            return;
        }

        $id_sala = $_POST['id_sala'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];

        $disponible = $this->model->verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin);

        echo json_encode([
            'success' => true,
            'disponible' => $disponible,
            'mensaje' => $disponible ? 'Sala disponible' : 'Sala no disponible en ese horario'
        ]);
    }

    /**
     * Obtener información de una sala específica
     */
    public function obtenerSala()
    {
        if (!isset($_GET['id_sala'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'ID de sala no proporcionado'
            ]);
            return;
        }

        $id_sala = $_GET['id_sala'];
        $sala = $this->model->getSalaPorId($id_sala);

        if ($sala) {
            echo json_encode(['success' => true, 'sala' => $sala]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Sala no encontrada']);
        }
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
$controller = new EstudianteController();

// Determinar método HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'salas':
        if ($method === 'GET') {
            $controller->obtenerSalas();
        }
        break;

    case 'solicitar':
    case 'crear_reserva':
        if ($method === 'POST') {
            $controller->solicitarReserva();
        }
        break;

    case 'mis_reservas':
        if ($method === 'GET') {
            $controller->obtenerMisReservas();
        }
        break;

    case 'reservas_proximas':
        if ($method === 'GET') {
            $controller->obtenerReservasProximas();
        }
        break;

    case 'historial':
        if ($method === 'GET') {
            $controller->obtenerHistorial();
        }
        break;

    case 'cancelar':
        if ($method === 'POST') {
            $controller->cancelarReserva();
        }
        break;

    case 'verificar_disponibilidad':
        if ($method === 'POST') {
            $controller->verificarDisponibilidad();
        }
        break;

    case 'sala':
        if ($method === 'GET') {
            $controller->obtenerSala();
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'mensaje' => 'Acción no válida'
        ]);
        break;
}
?>