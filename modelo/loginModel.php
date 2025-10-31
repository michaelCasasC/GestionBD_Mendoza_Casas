<?php
require_once '../config/database.php';

class LoginModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($email, $password) {
        try {
            // ✅ Nombre correcto de la tabla: Usuario (como en tu SQL)
            $query = "SELECT * FROM Usuario WHERE correo = :correo AND activo = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':correo', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                return [
                    'id_usuario' => $user['id_usuario'],
                    'nombre' => $user['nombre'],
                    'rol' => $user['id_rol']
                ];
            }

            return false;

        } catch (PDOException $e) {
            error_log("❌ Error en loginModel: " . $e->getMessage());
            return false;
        }
    }
}
?>
