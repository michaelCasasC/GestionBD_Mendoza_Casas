<?php
require_once '../config/database.php';

class EstudianteModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Obtener información del estudiante por correo
     */
    public function getEstudiantePorCorreo($correo)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT u.id_usuario, u.nombre, u.apellido, u.correo, r.nombre as rol
                FROM Usuario u
                INNER JOIN Rol r ON u.id_rol = r.id_rol
                WHERE u.correo = ? AND u.activo = 1
            ");
            $stmt->execute([$correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtener todas las salas disponibles
     */
    public function getSalasDisponibles()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT s.id_sala, s.nombre, s.capacidad, s.equipamiento, s.estado, 
                       t.nombre as tipo_sala
                FROM Sala s
                INNER JOIN Tipo_Sala t ON s.id_tipo = t.id_tipo
                WHERE s.estado = 'activa'
                ORDER BY s.nombre
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Crear nueva reserva usando el procedimiento almacenado
     */
    public function crearReserva($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin)
    {
        try {
            $stmt = $this->db->prepare("
                EXEC sp_CrearReserva 
                    @id_usuario = ?, 
                    @id_sala = ?, 
                    @fecha = ?, 
                    @hora_inicio = ?, 
                    @hora_fin = ?
            ");
            $stmt->execute([$id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado['status'] === 'success') {
                return [
                    'success' => true,
                    'mensaje' => $resultado['mensaje'],
                    'id_reserva' => $resultado['id_reserva'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => $resultado['mensaje']
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al crear reserva: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener reservas del estudiante
     */
    public function getReservasPorEstudiante($id_usuario)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.id_reserva, r.fecha, r.hora_inicio, r.hora_fin, r.estado, 
                       r.fecha_creacion, s.nombre as sala, s.capacidad, s.equipamiento
                FROM Reserva r
                INNER JOIN Sala s ON r.id_sala = s.id_sala
                WHERE r.id_usuario = ?
                ORDER BY r.fecha DESC, r.hora_inicio DESC
            ");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cancelar reserva usando el procedimiento almacenado
     */
    public function cancelarReserva($id_reserva, $id_usuario)
    {
        try {
            $stmt = $this->db->prepare("
                EXEC sp_CancelarReserva 
                    @id_reserva = ?, 
                    @id_usuario = ?
            ");
            $stmt->execute([$id_reserva, $id_usuario]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado['status'] === 'success') {
                return [
                    'success' => true,
                    'mensaje' => $resultado['mensaje']
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => $resultado['mensaje']
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al cancelar reserva: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar disponibilidad de sala
     */
    public function verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total
                FROM Reserva
                WHERE id_sala = ? 
                AND fecha = ?
                AND estado IN ('confirmada', 'en_curso')
                AND (
                    (? BETWEEN hora_inicio AND hora_fin) OR
                    (? BETWEEN hora_inicio AND hora_fin) OR
                    (hora_inicio BETWEEN ? AND ?)
                )
            ");
            $stmt->execute([$id_sala, $fecha, $hora_inicio, $hora_fin, $hora_inicio, $hora_fin]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] == 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtener información de una sala específica
     */
    public function getSalaPorId($id_sala)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT s.id_sala, s.nombre, s.capacidad, s.equipamiento, s.estado,
                       t.nombre as tipo_sala
                FROM Sala s
                INNER JOIN Tipo_Sala t ON s.id_tipo = t.id_tipo
                WHERE s.id_sala = ?
            ");
            $stmt->execute([$id_sala]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtener reservas confirmadas del estudiante (próximas)
     */
    public function getReservasProximas($id_usuario)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.id_reserva, r.fecha, r.hora_inicio, r.hora_fin, r.estado,
                       s.nombre as sala, s.capacidad, s.equipamiento
                FROM Reserva r
                INNER JOIN Sala s ON r.id_sala = s.id_sala
                WHERE r.id_usuario = ? 
                AND r.estado IN ('confirmada', 'en_curso')
                AND r.fecha >= CAST(GETDATE() AS DATE)
                ORDER BY r.fecha ASC, r.hora_inicio ASC
            ");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener historial de reservas del estudiante
     */
    public function getHistorialReservas($id_usuario)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.id_reserva, r.fecha, r.hora_inicio, r.hora_fin, r.estado,
                       r.fecha_creacion, s.nombre as sala
                FROM Reserva r
                INNER JOIN Sala s ON r.id_sala = s.id_sala
                WHERE r.id_usuario = ?
                AND (r.estado IN ('completada', 'cancelada') 
                     OR (r.estado = 'confirmada' AND r.fecha < CAST(GETDATE() AS DATE)))
                ORDER BY r.fecha DESC, r.hora_inicio DESC
            ");
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>