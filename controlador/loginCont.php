<?php
session_start();
require_once '../modelo/LoginModel.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $loginModel = new LoginModel();
    $resultado = $loginModel->verificarUsuario($correo, $password);

    if ($resultado['success']) {
        $usuario = $resultado['usuario'];

        // Guardar datos en sesión
        $_SESSION['user_id'] = $usuario['id_usuario'];
        $_SESSION['user_nombre'] = $usuario['nombre'];
        $_SESSION['user_email'] = $usuario['correo'];
        $_SESSION['user_rol'] = $usuario['id_rol'];

        // Redirigir según rol
        switch ($usuario['id_rol']) {
            case 1: // estudiante
                header("Location: ../vista/estudiante.php");
                exit;
            case 2: // administrativo
                header("Location: ../vista/administrativo.php");
                exit;
            case 3: // admin
                header("Location: ../vista/admin.php");
                exit;
            default:
                $mensaje = 'Rol no reconocido';
        }
    } else {
        $mensaje = $resultado['mensaje'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($mensaje) echo "<p>$mensaje</p>"; ?>

    <form method="POST" action="">
        <label>Correo:</label><br>
        <input type="email" name="correo" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Iniciar sesión</button>
    </form>
</body>
</html>
