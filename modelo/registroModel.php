<?php
require_once '../config/database.php';

class RegistroModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function registrarUsuario($nombre, $apellido, $correo, $password, $id_rol) {
        try {
            // Hashear contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO Usuario (nombre, apellido, correo, password_hash, id_rol)
                      VALUES (:nombre, :apellido, :correo, :password_hash, :id_rol)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':correo' => $correo,
                ':password_hash' => $password_hash,
                ':id_rol' => $id_rol
            ]);

            return ['success' => true, 'mensaje' => 'Usuario registrado exitosamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'mensaje' => 'Error al registrar usuario: ' . $e->getMessage()];
        }
    }
}
?>
