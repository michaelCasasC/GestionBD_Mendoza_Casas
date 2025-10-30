<?php
class EstudianteModel
{
    private $db;

    public function __construct()
    {
        // Conexión simple para pruebas - reemplaza con tu conexión real
        $server = "localhost";
        $database = "SistemaReservas";
        $username = "usuario_estudiante";
        $password = "Estudiante123!";

        try {
            $this->db = new PDO(
                "sqlsrv:Server=$server;Database=$database",
                $username,
                $password
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public function getSalasDisponibles()
    {
        try {
            $query = "SELECT 
                        s.id_sala,
                        s.nombre,
                        s.capacidad,
                        s.equipamiento,
                        s.estado,
                        ts.nombre as tipo_sala
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

    public function verificarDisponibilidad($id_sala, $fecha, $hora_inicio, $hora_fin)
    {
        try {
            $query = "SELECT COUNT(*) as conflictos
                      FROM Reserva 
                      WHERE id_sala = :id_sala 
                      AND fecha = :fecha
                      AND estado IN ('confirmada', 'en_curso')
                      AND (
                          (:hora_inicio BETWEEN hora_inicio AND hora_fin) OR
                          (:hora_fin BETWEEN hora_inicio AND hora_fin) OR
                          (hora_inicio BETWEEN :hora_inicio AND :hora_fin)
                      )";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['conflictos'] == 0;

        } catch (PDOException $e) {
            throw new Exception("Error al verificar disponibilidad: " . $e->getMessage());
        }
    }

    public function crearReserva($id_usuario, $id_sala, $fecha, $hora_inicio, $hora_fin)
    {
        try {
            $query = "EXEC sp_CrearReserva @id_usuario = :id_usuario, 
                                          @id_sala = :id_sala, 
                                          @fecha = :fecha, 
                                          @hora_inicio = :hora_inicio, 
                                          @hora_fin = :hora_fin";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);

            $stmt->execute();

            // El procedimiento almacenado retorna un resultset
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['status'] === 'success') {
                return [
                    'success' => true,
                    'mensaje' => $result['mensaje'],
                    'id_reserva' => $result['id_reserva'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => $result['mensaje'] ?? 'Error al crear reserva'
                ];
            }

        } catch (PDOException $e) {
            throw new Exception("Error al crear reserva: " . $e->getMessage());
        }
    }

    public function getReservasPorEstudiante($id_usuario)
    {
        try {
            $query = "SELECT 
                        r.id_reserva,
                        r.fecha,
                        r.hora_inicio,
                        r.hora_fin,
                        r.estado,
                        s.nombre as sala,
                        s.capacidad,
                        ts.nombre as tipo_sala
                      FROM Reserva r
                      INNER JOIN Sala s ON r.id_sala = s.id_sala
                      INNER JOIN Tipo_Sala ts ON s.id_tipo = ts.id_tipo
                      WHERE r.id_usuario = :id_usuario
                      ORDER BY r.fecha DESC, r.hora_inicio DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservas: " . $e->getMessage());
        }
    }

    public function cancelarReserva($id_reserva, $id_usuario)
    {
        try {
            $query = "EXEC sp_CancelarReserva @id_reserva = :id_reserva, 
                                             @id_usuario = :id_usuario";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_reserva', $id_reserva);
            $stmt->bindParam(':id_usuario', $id_usuario);

            $stmt->execute();

            // El procedimiento almacenado retorna un resultset
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['status'] === 'success') {
                return [
                    'success' => true,
                    'mensaje' => $result['mensaje']
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => $result['mensaje'] ?? 'Error al cancelar reserva'
                ];
            }

        } catch (PDOException $e) {
            throw new Exception("Error al cancelar reserva: " . $e->getMessage());
        }
    }

    public function getSalaPorId($id_sala)
    {
        try {
            $query = "SELECT 
                        s.id_sala,
                        s.nombre,
                        s.capacidad,
                        s.equipamiento,
                        s.estado,
                        ts.nombre as tipo_sala
                      FROM Sala s
                      INNER JOIN Tipo_Sala ts ON s.id_tipo = ts.id_tipo
                      WHERE s.id_sala = :id_sala";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_sala', $id_sala);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception("Error al obtener sala: " . $e->getMessage());
        }
    }
}
?>