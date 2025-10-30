<?php
class Database {
    private $server = 'USUARIO\SQLEXPRESS';
    private $database = 'SistemaReservas';
    private $user = 'login_estudiante';
    private $password = 'Estudiante123!';
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $connectionString = "sqlsrv:Server={$this->server};Database={$this->database}";
            $this->conn = new PDO($connectionString, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>