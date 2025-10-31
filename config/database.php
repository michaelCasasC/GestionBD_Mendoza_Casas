<?php
class Database {
    // Datos de conexión
    private $host = "USUARIO\\SQLEXPRESS"; 
    private $db_name = "SistemaReservas";
    private $user = "login_admin";
    private $password = "Admin123!";
    private $conn;

    // Método para obtener la conexión
    public function getConnection() {
        $this->conn = null;

        try {
            $connectionString = "sqlsrv:Server={$this->host};Database={$this->db_name}";
            
            // Crear la conexión PDO
            $this->conn = new PDO($connectionString, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // DEBUG opcional: se puede dejar en error_log sin afectar headers
            error_log("✅ Conexión exitosa a la base de datos");

        } catch (PDOException $exception) {
            // Solo registrar el error, no usar die() ni echo
            error_log("❌ Error de conexión: " . $exception->getMessage());

            // Lanzar excepción para que el controlador lo maneje
            throw new Exception("No se pudo conectar a la base de datos");
        }

        return $this->conn;
    }
}
?>
