<?php
// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/EstudianteModel.php';

// DEPURACIÓN: verificar sesión
error_log("=== DEBUG ESTUDIANTE CONTROLLER ===");
error_log("SESSION ID: " . session_id());
error_log("SESSION DATA: " . print_r($_SESSION, true));
error_log("COOKIE: " . print_r($_COOKIE, true));

class EstudianteController
{
    private $model;

    public function __construct()
    {
        $this->model = new EstudianteModel();

        // Verificar autorización
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'mensaje' => 'No autorizado - Inicie sesión',
                'debug' => [
                    'session_data' => $_SESSION
                ]
            ]);
            exit();
        }
    }

    // ===============================
    // 🔹 1. Obtener salas disponibles
    // ===============================
    public function obtenerSalas()
    {
        $salas = $this->model->getSalasDisponibles();
        echo json_encode(['success' => true, 'salas' => $salas]);
    }

    // ===============================
    // 🔹 2. Solicitar/Crear reserva
    // ===============================
    public function solicitarReserva()
{
    header('Content-Type: application/json');

    // Obtener datos POST
    $id_usuario = $_SESSION['user_id'];
    $id_sala = $_POST['id_sala'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $hora_inicio = $_POST['hora_inicio'] ?? null;
    $hora_fin = $_POST['hora_fin'] ?? null;

    error_log("=== SOLICITANDO RESERVA ===");
    error_log("Datos recibidos: " . json_encode([
        'id_usuario' => $id_usuario,
        'id_sala' => $id_sala,
        'fecha' => $fecha,
        'hora_inicio' => $hora_inicio,
        'hora_fin' => $hora_fin
    ]));

    // Validar datos
    if (!$id_sala || !$fecha || !$hora_inicio || !$hora_fin) {
        echo json_encode(['success' => false, 'mensaje' => 'Faltan datos obligatorios']);
        return;
    }

    try {
        // 1️⃣ Verificar disponibilidad
        $disponible = $this->model->verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin);
        error_log("Disponibilidad sala {$id_sala} en {$fecha} de {$hora_inicio} a {$hora_fin}: " . ($disponible ? 'Disponible' : 'Ocupada'));

        if (!$disponible) {
            echo json_encode(['success' => false, 'mensaje' => 'La sala no está disponible en ese horario']);
            return;
        }

        // 2️⃣ Crear reserva
        $resultado = $this->model->crearReserva($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin);
        error_log("Resultado de crearReserva: " . json_encode($resultado));

        echo json_encode($resultado);

    } catch (Exception $e) {
        error_log("Error en solicitarReserva: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'mensaje' => 'Error al procesar la reserva',
            'error' => $e->getMessage()
        ]);
    }
}

    // ===============================
    // 🔹 3. Obtener mis reservas
    // ===============================
    public function obtenerMisReservas()
    {
        $id_usuario = $_SESSION['user_id'];
        $reservas = $this->model->getReservasPorEstudiante($id_usuario);
        echo json_encode(['success' => true, 'reservas' => $reservas]);
    }

    // ===============================
    // 🔹 4. Obtener reservas próximas
    // ===============================
    public function obtenerReservasProximas()
    {
        $id_usuario = $_SESSION['user_id'];
        $reservas = $this->model->getReservasProximas($id_usuario);
        echo json_encode(['success' => true, 'reservas' => $reservas]);
    }

    // ===============================
    // 🔹 5. Obtener historial de reservas
    // ===============================
    public function obtenerHistorial()
    {
        $id_usuario = $_SESSION['user_id'];
        $reservas = $this->model->getHistorialReservas($id_usuario);
        echo json_encode(['success' => true, 'reservas' => $reservas]);
    }

    // ===============================
    // 🔹 6. Cancelar reserva
    // ===============================
    public function cancelarReserva()
    {
        if (!isset($_POST['id_reserva'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de reserva no proporcionado']);
            return;
        }

        $resultado = $this->model->cancelarReserva($_POST['id_reserva'], $_SESSION['user_id']);
        echo json_encode($resultado);
    }

    // ===============================
    // 🔹 7. Verificar disponibilidad
    // ===============================
   public function verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin)
{
    try {
        // SQL Server requiere que los parámetros de hora se casteen correctamente
        $query = "SELECT COUNT(*) AS conflictos
                  FROM Reserva
                  WHERE id_sala = :id_sala
                  AND fecha = :fecha
                  AND estado IN ('confirmada', 'en_curso')
                  AND (
                      (hora_inicio < CAST(:hora_fin AS TIME) AND hora_fin > CAST(:hora_inicio AS TIME))
                  )";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_sala' => $id_sala,
            ':fecha' => $fecha,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['conflictos'] == 0;

    } catch (PDOException $e) {
        throw new Exception("Error al verificar disponibilidad: " . $e->getMessage());
    }
}



    // ===============================
    // 🔹 8. Obtener información de una sala por ID
    // ===============================
    public function obtenerSala()
    {
        if (!isset($_GET['id_sala'])) {
            echo json_encode(['success' => false, 'mensaje' => 'ID de sala no proporcionado']);
            return;
        }

        $sala = $this->model->getSalaPorId($_GET['id_sala']);
        if ($sala) {
            echo json_encode(['success' => true, 'sala' => $sala]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Sala no encontrada']);
        }
    }

    // ===============================
    // 🔹 9. Logout
    // ===============================
    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        header("Location: ../vista/login.php");
        exit();
    }
}

// ===============================
// 🔹 Rutas y acciones
// ===============================
$action = $_GET['action'] ?? '';
$controller = new EstudianteController();
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'salas':
        if ($method === 'GET') $controller->obtenerSalas();
        break;
    case 'solicitar':
    case 'crear_reserva':
        if ($method === 'POST') $controller->solicitarReserva();
        break;
    case 'mis_reservas':
        if ($method === 'GET') $controller->obtenerMisReservas();
        break;
    case 'reservas_proximas':
        if ($method === 'GET') $controller->obtenerReservasProximas();
        break;
    case 'historial':
        if ($method === 'GET') $controller->obtenerHistorial();
        break;
    case 'cancelar':
        if ($method === 'POST') $controller->cancelarReserva();
        break;
    case 'verificar_disponibilidad':
        if ($method === 'POST') $controller->verificarDisponibilidad();
        break;
    case 'sala':
        if ($method === 'GET') $controller->obtenerSala();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
        break;
}
?>
