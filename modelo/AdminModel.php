<?php
require_once '../config/dbEstudent.php';

class AdminModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getEstudiantes() {
        try {
            $sql = "
                SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.fecha_registro, u.activo
                FROM Usuario u
                INNER JOIN Rol r ON u.id_rol = r.id_rol
                WHERE r.nombre = 'estudiante'
                ORDER BY u.nombre
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEstudiantes: " . $e->getMessage());
            return [];
        }
    }

    public function getDocentes() {
        try {
            $sql = "
                SELECT u.id_usuario, u.nombre, u.apellido, u.correo, r.nombre AS rol, u.activo
                FROM Usuario u
                INNER JOIN Rol r ON u.id_rol = r.id_rol
                WHERE r.nombre IN ('docente', 'administrativo')
                ORDER BY u.nombre
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getDocentes: " . $e->getMessage());
            return [];
        }
    }

    public function getSalas() {
        try {
            $sql = "
                SELECT s.id_sala, s.nombre, s.capacidad, s.equipamiento, s.estado, t.nombre AS tipo
                FROM Sala s
                INNER JOIN Tipo_Sala t ON s.id_tipo = t.id_tipo
                ORDER BY s.nombre
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getSalas: " . $e->getMessage());
            return [];
        }
    }

    public function getReservas() {
        try {
            $sql = "
                SELECT r.id_reserva, 
                       CONCAT(u.nombre, ' ', u.apellido) AS estudiante, 
                       s.nombre AS sala, r.fecha, r.hora_inicio, r.hora_fin, r.estado
                FROM Reserva r
                INNER JOIN Usuario u ON r.id_usuario = u.id_usuario
                INNER JOIN Sala s ON r.id_sala = s.id_sala
                WHERE r.estado IN ('confirmada', 'en_curso')
                ORDER BY r.fecha DESC
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getReservas: " . $e->getMessage());
            return [];
        }
    }
}
?>