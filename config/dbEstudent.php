<?php
$server = "localhost\\SQLEXPRESS";
$database = "SistemaReservas";
$username = "login_estudiante"; // ajusta tu usuario
$password = "Estudiante123!";     // ajusta tu contraseña

try {
    $pdo = new PDO(
        "sqlsrv:Server=$server;Database=$database",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conexión exitosa"; // opcional
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>