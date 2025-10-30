<?php
class Database
{
    private $server;
    private $database = 'SistemaReservas';
    private $user = 'login_estudiante';
    private $password = 'Estudiante123!';
    private $conn;

    public function __construct()
    {
        // Elegir servidor disponible
        if ($this->servidorDisponible('USUARIO\\SQLEXPRESS')) {
            $this->server = 'USUARIO\\SQLEXPRESS';
        } else {
            $this->server = 'localhost';
        }

        // Conectar
        $this->connect();
    }

    // Método para verificar disponibilidad del servidor
    private function servidorDisponible($serverName)
    {
        // Esto es un ejemplo simple usando @ para suprimir errores de conexión
        $connectionInfo = array("Database" => $this->database, "UID" => $this->user, "PWD" => $this->password);
        $connTest = @sqlsrv_connect($serverName, $connectionInfo);
        if ($connTest) {
            sqlsrv_close($connTest);
            return true;
        }
        return false;
    }

    private function connect()
    {
        try {
            $connectionString = "sqlsrv:Server={$this->server};Database={$this->database}";
            $this->conn = new PDO($connectionString, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
?>