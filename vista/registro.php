<?php
require_once '../modelo/RegistroModel.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $id_rol = $_POST['rol']; // 1 = estudiante, 2 = administrativo, 3 = admin

    $registroModel = new RegistroModel();
    $resultado = $registroModel->registrarUsuario($nombre, $apellido, $correo, $password, $id_rol);
    $mensaje = $resultado['mensaje'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #00312D;
            --primary-green: #3A7817;
            --accent-green: #72BF00;
            --light-bg: #f8f9fa;
            --border-color: #e0e0e0;
            --text-dark: #333333;
        }
        
        body {
            background: linear-gradient(135deg, #00312D 0%, #3A7817 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .register-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 800px;
        }
        
        .register-header {
            background: linear-gradient(to right, var(--primary-dark), var(--primary-green));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .register-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
        }
        
        .register-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            border-radius: 8px;
            background: white;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 102, 119, 0.1);
            border-color: var(--primary-dark);
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            color: #b7b9cc;
        }
        
        .boton-registrar {
            background: #00312D;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            color: white;
            font-size: 1.05rem;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .boton-registrar:hover {
            background: #00312D;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: var(--text-dark);
        }
        
        .login-link a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .role-icon {
            font-size: 18px;
            margin-right: 8px;
        }
        
        .form-select {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            border-radius: 8px;
            background: white;
            font-size: 15px;
        }
        
        .form-select:focus {
            box-shadow: 0 0 0 3px rgba(13, 102, 119, 0.1);
            border-color: var(--primary-dark);
        }
        
        .alert-message {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--accent-green);
            background-color: rgba(114, 191, 0, 0.1);
        }
        
        .seccion-demostracion {
            background: #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .demo-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }
        
        .boton-demo {
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: white;
            transition: all 0.3s;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .demo1 {
            background-color: #00312D;
        }
        
        .demo2 {
            background-color: #3A7817;
        }
        
        .demo3 {
            background-color: #72BF00;
        }
        
        .boton-demo:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            color: white;
        }
        
        @media (max-width: 768px) {
            .register-body {
                padding: 30px 20px;
            }
            
            .register-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1><i class="fas fa-user-plus me-2"></i>Registro de Usuario</h1>
            <p>Crea tu cuenta en el sistema de reservas</p>
        </div>
        
        <div class="register-body">
            <?php if ($mensaje): ?>
                <div class="alert-message">
                    <i class="fas fa-info-circle me-2"></i><?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                            <i class="fas fa-user-tag input-icon"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="rol" class="form-label">Tipo de Usuario</label>
                    <select class="form-select" id="rol" name="rol" required>
                        <option value="">Selecciona un rol</option>
                        <option value="1"><i class="fas fa-user-graduate role-icon"></i> Estudiante</option>
                        <option value="2"><i class="fas fa-user-tie role-icon"></i> Administrativo</option>
                        <option value="3"><i class="fas fa-user-shield role-icon"></i> Administrador</option>
                    </select>
                </div>
                
                <button type="submit" class="boton-registrar">
                    <i class="fas fa-user-plus me-2"></i>Registrar Usuario
                </button>
                
                <div class="login-link">
                    ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
                </div>
            </form>
            
            <div class="seccion-demostracion">
                <div class="demo-title">Datos de demostración:</div>
                <button type="button" class="boton-demo demo1" onclick="rellenarDemo(1)">
                    <i class="fas fa-user-graduate me-1"></i> Estudiante Demo
                </button>
                <button type="button" class="boton-demo demo2" onclick="rellenarDemo(2)">
                    <i class="fas fa-user-tie me-1"></i> Administrativo Demo
                </button>
                <button type="button" class="boton-demo demo3" onclick="rellenarDemo(3)">
                    <i class="fas fa-user-shield me-1"></i> Admin Demo
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function rellenarDemo(tipo) {
            const nombres = ['Ana', 'Carlos', 'María', 'José', 'Laura'];
            const apellidos = ['García', 'Martínez', 'López', 'Rodríguez', 'Pérez'];
            const dominios = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com'];
            
            const nombreAleatorio = nombres[Math.floor(Math.random() * nombres.length)];
            const apellidoAleatorio = apellidos[Math.floor(Math.random() * apellidos.length)];
            const dominioAleatorio = dominios[Math.floor(Math.random() * dominios.length)];
            
            document.getElementById('nombre').value = nombreAleatorio;
            document.getElementById('apellido').value = apellidoAleatorio;
            document.getElementById('correo').value = `${nombreAleatorio.toLowerCase()}.${apellidoAleatorio.toLowerCase()}@${dominioAleatorio}`;
            document.getElementById('password').value = 'Demo123!';
            document.getElementById('rol').value = tipo;
            
            // Mostrar mensaje de confirmación
            const roles = {1: 'Estudiante', 2: 'Administrativo', 3: 'Administrador'};
            alert(`Formulario rellenado con datos de demostración para ${roles[tipo]}`);
        }
    </script>
</body>
</html>