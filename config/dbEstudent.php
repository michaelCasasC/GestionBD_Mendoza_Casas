<?php
class dbEstudent {
    private $host = "USUARIO\\SQLEXPRESS"; 
    private $db_name = "SistemaReservas";
    private $user = "login_estudiante";
    private $password = "Estudiante123!";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("sqlsrv:Server={$this->host};Database={$this->db_name}", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            die("Error de conexión: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>