<?php
require_once '../config/database.php';

class AdminModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Obtener todos los estudiantes
     */
    public function getEstudiantes() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM Usuarios WHERE rol = 'estudiante' 
                ORDER BY nombre
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener todos los docentes
     */
    public function getDocentes() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM Usuarios WHERE rol = 'docente' 
                ORDER BY nombre
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener todas las salas
     */
    public function getSalas() {
        try {
            $stmt = $this->db->query("
                SELECT * FROM Salas 
                ORDER BY nombre
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener estadísticas del sistema
     */
    public function getEstadisticas() {
        try {
            // Total estudiantes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM Usuarios WHERE rol = 'estudiante'");
            $estudiantes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total docentes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM Usuarios WHERE rol = 'docente'");
            $docentes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total salas
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM Salas");
            $salas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'estudiantes' => $estudiantes,
                'docentes' => $docentes,
                'salas' => $salas
            ];
        } catch (PDOException $e) {
            return ['estudiantes' => 0, 'docentes' => 0, 'salas' => 0];
        }
    }
}
?>