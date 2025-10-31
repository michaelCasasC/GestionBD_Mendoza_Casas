<?php
require_once '../modelo/AdminModel.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $model = new AdminModel();
    
    $data = [
        'estudiantes' => $model->getEstudiantes(),
        'docentes' => $model->getDocentes(),
        'salas' => $model->getSalas(),
        'reservas' => $model->getReservas(),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Verifica si hay errores en alguna consulta
    foreach ($data as $key => $value) {
        if ($value === false) {
            $data['error'] = "Error en la consulta: $key";
            break;
        }
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>