<?php
require_once 'config/database.php';

echo "<h3>🧪 Probando conexiones a SQL Server</h3>";

// Probar conexión con cada rol
$roles = ['estudiante', 'administrativo', 'admin'];

foreach ($roles as $rol) {
    echo "<h4>Probando conexión como: $rol</h4>";
    
    try {
        $db = new Database($rol);
        $conn = $db->getConnection();
        
        // Consulta simple que funciona en SQL Server
        $stmt = $conn->query("SELECT DB_NAME() as db_name");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "✅ <strong style='color: green;'>CONEXIÓN EXITOSA</strong><br>";
        echo "Base de datos: " . $result['db_name'] . "<br>";
        echo "Usuario: " . $rol . "<br><br>";
        
    } catch (PDOException $e) {
        echo "❌ <strong style='color: red;'>ERROR:</strong> " . $e->getMessage() . "<br><br>";
    }
}
?>