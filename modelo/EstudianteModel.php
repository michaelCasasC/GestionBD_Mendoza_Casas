<?php
class EstudianteModel
{
    private $db;

    public function __construct()
    {
        // Conexión centralizada desde archivo de configuración
        require '../config/dbEstudent.php'; // debe devolver un PDO en $pdo

        if (!isset($pdo)) {
            throw new Exception("Error: variable \$pdo no definida en dbEstudent.php");
        }

        $this->db = $pdo; 
    }

    // ===============================
    // 🔹 1. Obtener salas disponibles
    // ===============================
    public function getSalasDisponibles()
    {
        try {
            $query = "SELECT 
                        s.id_sala,
                        s.nombre,
                        s.capacidad,
                        s.equipamiento,
                        s.estado,
                        ts.nombre AS tipo_sala
                      FROM Sala s
                      INNER JOIN Tipo_Sala ts ON s.id_tipo = ts.id_tipo
                      WHERE s.estado = 'activa'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener salas: " . $e->getMessage());
        }
    }

    // ===============================
    // 🔹 2. Verificar disponibilidad
    // ===============================
    public function verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin)
{
    try {
        // Detectar cualquier solapamiento de horarios
        $query = "
            SELECT COUNT(*) AS conflictos
            FROM Reserva
            WHERE id_sala = :id_sala
              AND fecha = :fecha
              AND estado IN ('confirmada','en_curso')
              AND (
                  hora_inicio < CAST(:hora_fin AS TIME)
                  AND hora_fin > CAST(:hora_inicio AS TIME)
              )
        ";

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
    // 🔹 3. Crear reserva
    // ===============================
    public function crearReserva($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin)
    {
        try {
            $query = "INSERT INTO Reserva (id_usuario, id_sala, fecha, hora_inicio, hora_fin, estado)
                      VALUES (:id_usuario, :id_sala, :fecha, :hora_inicio, :hora_fin, 'confirmada')";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id_usuario' => $id_usuario,
                ':id_sala' => $id_sala,
                ':fecha' => $fecha,
                ':hora_inicio' => $hora_inicio,
                ':hora_fin' => $hora_fin
            ]);

            return [
                'success' => true,
                'mensaje' => 'Reserva creada exitosamente'
            ];
        } catch (PDOException $e) {
            throw new Exception("Error al crear reserva: " . $e->getMessage());
        }
    }

    // ===============================
    // 🔹 4. Reservas del estudiante
    // ===============================
    public function getReservasPorEstudiante($id_usuario)
    {
        try {
            $query = "SELECT 
                        r.id_reserva,
                        r.fecha,
                        r.hora_inicio,
                        r.hora_fin,
                        r.estado,
                        s.nombre AS sala,
                        s.capacidad,
                        ts.nombre AS tipo_sala
                      FROM Reserva r
                      INNER JOIN Sala s ON r.id_sala = s.id_sala
                      INNER JOIN Tipo_Sala ts ON s.id_tipo = ts.id_tipo
                      WHERE r.id_usuario = :id_usuario
                      ORDER BY r.fecha DESC, r.hora_inicio DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservas: " . $e->getMessage());
        }
    }

    // ===============================
    // 🔹 5. Cancelar reserva
    // ===============================
    public function cancelarReserva($id_reserva, $id_usuario)
    {
        try {
            $query = "UPDATE Reserva 
                      SET estado = 'cancelada'
                      WHERE id_reserva = :id_reserva AND id_usuario = :id_usuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id_reserva' => $id_reserva,
                ':id_usuario' => $id_usuario
            ]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'mensaje' => 'Reserva cancelada correctamente'];
            } else {
                return ['success' => false, 'mensaje' => 'No se encontró la reserva o no pertenece al usuario'];
            }
        } catch (PDOException $e) {
            throw new Exception("Error al cancelar reserva: " . $e->getMessage());
        }
    }

    // ===============================
    // 🔹 6. Sala por ID
    // ===============================
    public function getSalaPorId($id_sala)
    {
        try {
            $query = "SELECT 
                        s.id_sala,
                        s.nombre,
                        s.capacidad,
                        s.equipamiento,
                        s.estado,
                        ts.nombre AS tipo_sala
                      FROM Sala s
                      INNER JOIN Tipo_Sala ts ON s.id_tipo = ts.id_tipo
                      WHERE s.id_sala = :id_sala";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id_sala' => $id_sala]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener sala: " . $e->getMessage());
        }
    }
}
?>
