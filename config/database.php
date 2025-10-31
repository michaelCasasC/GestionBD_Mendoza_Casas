<?php
class Database {
    private $host = "USUARIO\\SQLEXPRESS"; 
    private $db_name = "SistemaReservas";
    private $user = "login_admin";
    private $password = "Admin123!";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $connectionString = "sqlsrv:Server={$this->host};Database={$this->db_name}";
            error_log("Intentando conectar: " . $connectionString);
            
            $this->conn = new PDO($connectionString, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            error_log("✅ Conexión exitosa a la base de datos");
            
        } catch (PDOException $exception) {
            error_log("❌ Error de conexión: " . $exception->getMessage());
            die("Error de conexión: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>