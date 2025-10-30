<?php
require_once '../config/database.php';

class ReservaModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Crear nueva solicitud de reserva
     */
    public function crearSolicitud($email_estudiante, $sala, $fecha, $hora_inicio, $hora_fin, $motivo) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Solicitudes (email_estudiante, sala, fecha, hora_inicio, hora_fin, motivo, estado) 
                VALUES (?, ?, ?, ?, ?, ?, 'pendiente')
            ");
            $stmt->execute([$email_estudiante, $sala, $fecha, $hora_inicio, $hora_fin, $motivo]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener solicitudes de un estudiante
     */
    public function getSolicitudesPorEstudiante($email_estudiante) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM Solicitudes 
                WHERE email_estudiante = ? 
                ORDER BY fecha_creacion DESC
            ");
            $stmt->execute([$email_estudiante]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener todas las solicitudes pendientes
     */
    public function getSolicitudesPendientes() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM Solicitudes 
                WHERE estado = 'pendiente' 
                ORDER BY fecha_creacion DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Aprobar una solicitud
     */
    public function aprobarSolicitud($id_solicitud) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Solicitudes SET estado = 'aprobada' 
                WHERE id = ?
            ");
            $stmt->execute([$id_solicitud]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Rechazar una solicitud
     */
    public function rechazarSolicitud($id_solicitud, $motivo_rechazo) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Solicitudes 
                SET estado = 'rechazada', motivo_rechazo = ? 
                WHERE id = ?
            ");
            $stmt->execute([$motivo_rechazo, $id_solicitud]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>