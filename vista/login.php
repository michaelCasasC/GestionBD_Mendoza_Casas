<?php
session_start();

// Si ya había una sesión iniciada, la destruimos al entrar al login
// (Así no te manda directo a estudiante sin querer)
if (isset($_SESSION['user_rol'])) {
    session_destroy();
}

// Capturar errores de login (pasados por GET)
$message_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reservas de Salas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {min-height: 100vh; background: #eafde7;}
        .panel-izquierdo {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)), url('../Rename.jpg');
            background-size: cover; background-position: center; color: #eafde7; border-radius: 0 40px 40px 0;
        }
        .titulo-principal {font-size: 2.8rem; font-weight: 700; line-height: 1.2;}
        .titulo-formulario {font-size: 2rem; font-weight: 700; color: #00312D;}
        .entrada-con-icono {position: relative;}
        .entrada-con-icono i {position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;}
        .entrada-con-icono .form-control {padding-left: 45px;}
        .form-control {border: 1px solid #e0e0e0; padding: 12px 15px; border-radius: 8px; background: white;}
        .boton-ingresar {background: #00312D; border: none; padding: 14px; border-radius: 8px; font-weight: 600; color: white;}
        .seccion-demostracion {background: #f0f0f0; border-radius: 12px; padding: 20px;}
        .boton-demo {border: 1px solid #ddd; padding: 10px 20px; border-radius: 8px; font-weight: 500;}
        .demo1 {background-color: #00312D; color:white;}
        .demo2 {background-color: #3A7817; color:white;}
        .demo3 {background-color: #72BF00; color:white;}
        @media (max-width: 768px) {
            .panel-izquierdo {border-radius: 20px 20px 0 0;}
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">

            <!-- Panel Izquierdo -->
            <div class="col-md-6 panel-izquierdo d-flex flex-column justify-content-center p-5">
                <h1 class="titulo-principal mb-3">Sistema de<br>Reservas de Salas</h1>
                <p class="subtitulo mb-0">Gestiona tus reservas<br>de manera eficiente</p>
            </div>

            <!-- Panel Derecho -->
            <div class="col-md-6 p-5">
                <h2 class="titulo-formulario mb-4">Iniciar Sesión</h2>

                <form id="formularioLogin" method="POST" action="../controlador/procesarlogin.php">
                    <div class="mb-3">
                        <label for="correo" class="form-label fw-medium text-secondary">Email</label>
                        <div class="entrada-con-icono">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="contrasena" class="form-label fw-medium text-secondary">Contraseña</label>
                        <div class="entrada-con-icono">
                            <i class="fas fa-lock"></i>
                            <input id="password" type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" id="submit" class="btn boton-ingresar w-100 mb-4">
                        Ingresar al Sistema
                    </button>

                    <?php if ($message_error): ?>
                        <div class="alert alert-danger text-center"><?= $message_error ?></div>
                    <?php endif; ?>

                    <div class="seccion-demostracion">
                        <p class="text-center text-secondary mb-3 fw-medium">¿Quieres probar? Usa estas credenciales:</p>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn boton-demo demo1" id="btnEstudiante">Estudiante</button>
                            <button type="button" class="btn boton-demo demo2" id="btnDocente"><a href="administracion.html" style="colo: inherit;">Docente</a> </button>
                            <button type="button" class="btn boton-demo demo3" id="btnAdmin"><a href="admin.html">Admin</a> </button>
                        </div>
                    </div>

                    <div class="col text-center mt-3">
                        <a href="registro.php">Registrarse</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Auto completar demo
    document.getElementById('btnEstudiante').onclick = () => {
        document.getElementById('email').value = "estudiante@universidad.edu";
        document.getElementById('password').value = "123456";
        document.getElementById('submit').click();
    };
    document.getElementById('btnDocente').onclick = () => {
        document.getElementById('email').value = "docente@universidad.edu";
        document.getElementById('password').value = "123456";
        document.getElementById('submit').click();
    };
    document.getElementById('btnAdmin').onclick = () => {
       window.location.href = "";
    };
    </script>
</body>
</html>
