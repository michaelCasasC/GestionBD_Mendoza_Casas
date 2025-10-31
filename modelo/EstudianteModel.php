<?php
class EstudianteModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Obtener todas las salas disponibles
    public function getSalas() {
        $stmt = $this->db->prepare("SELECT * FROM Salas WHERE estado = 'activa'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar una nueva reserva
    public function crearReserva($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin, $motivo, $capacidad) {
        $stmt = $this->db->prepare("INSERT INTO Reservas (id_usuario, id_sala, fecha, hora_inicio, hora_fin, motivo, capacidad, estado)
                                    VALUES (:id_usuario, :id_sala, :fecha, :hora_inicio, :hora_fin, :motivo, :capacidad, 'confirmada')");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_sala', $id_sala);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->bindParam(':capacidad', $capacidad);
        return $stmt->execute();
    }

    // Obtener reservas de un estudiante
    public function getReservas($id_usuario) {
        $stmt = $this->db->prepare("
            SELECT r.id_reserva, s.nombre AS sala, r.fecha, r.hora_inicio, r.hora_fin, r.capacidad, r.estado
            FROM Reservas r
            INNER JOIN Salas s ON r.id_sala = s.id_sala
            WHERE r.id_usuario = :id_usuario
            ORDER BY r.fecha DESC, r.hora_inicio DESC
        ");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cancelar reserva
    public function cancelarReserva($id_reserva, $id_usuario) {
        $stmt = $this->db->prepare("UPDATE Reservas SET estado = 'cancelada' WHERE id_reserva = :id_reserva AND id_usuario = :id_usuario");
        $stmt->bindParam(':id_reserva', $id_reserva);
        $stmt->bindParam(':id_usuario', $id_usuario);
        return $stmt->execute();
    }
}
