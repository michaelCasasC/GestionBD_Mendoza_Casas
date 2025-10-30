<?php
session_start();
require_once '../models/ReservaModel.php';

class ReservasController {
    
    public function solicitarReserva() {
        if ($_POST['sala'] && $_POST['fecha'] && $_POST['hora_inicio'] && $_POST['hora_fin'] && $_POST['motivo']) {
            
            $reservaModel = new ReservaModel();
            $resultado = $reservaModel->crearSolicitud(
                $_SESSION['user_email'],
                $_POST['sala'],
                $_POST['fecha'], 
                $_POST['hora_inicio'],
                $_POST['hora_fin'],
                $_POST['motivo']
            );
            
            if ($resultado['success']) {
                echo json_encode(['success' => true, 'message' => 'Solicitud enviada']);
            } else {
                echo json_encode(['success' => false, 'message' => $resultado['error']]);
            }
        }
    }
    
    public function obtenerSolicitudesEstudiante() {
        $reservaModel = new ReservaModel();
        $solicitudes = $reservaModel->getSolicitudesPorEstudiante($_SESSION['user_email']);
        echo json_encode($solicitudes);
    }
    
    public function obtenerSolicitudesPendientes() {
        $reservaModel = new ReservaModel();
        $solicitudes = $reservaModel->getSolicitudesPendientes();
        echo json_encode($solicitudes);
    }
    
    public function aprobarSolicitud() {
        if ($_POST['id_solicitud']) {
            $reservaModel = new ReservaModel();
            $resultado = $reservaModel->aprobarSolicitud($_POST['id_solicitud']);
            echo json_encode($resultado);
        }
    }
    
    public function rechazarSolicitud() {
        if ($_POST['id_solicitud'] && $_POST['motivo_rechazo']) {
            $reservaModel = new ReservaModel();
            $resultado = $reservaModel->rechazarSolicitud($_POST['id_solicitud'], $_POST['motivo_rechazo']);
            echo json_encode($resultado);
        }
    }
}

// Ejecutar acción
$action = $_GET['action'] ?? '';
$controller = new ReservasController();

if ($action === 'solicitar') {
    $controller->solicitarReserva();
} elseif ($action === 'mis_solicitudes') {
    $controller->obtenerSolicitudesEstudiante();
} elseif ($action === 'solicitudes_pendientes') {
    $controller->obtenerSolicitudesPendientes();
} elseif ($action === 'aprobar') {
    $controller->aprobarSolicitud();
} elseif ($action === 'rechazar') {
    $controller->rechazarSolicitud();
}
?>