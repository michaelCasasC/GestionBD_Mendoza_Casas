<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../modelo/loginModel.php';

class AuthController {
    private $loginModel;

    public function __construct() {
        $this->loginModel = new LoginModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->loginModel->login($email, $password);

            if ($user) {
                // Guardar todos los datos necesarios en la sesión
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['user_nombre'] = $user['nombre'];
                $_SESSION['user_email'] = $email;  //importante
                $_SESSION['user_rol'] = $user['rol'];

                // Redirigir según rol
                switch ($user['rol']) {
                    case 1:
                        header("Location: ../vista/estudiante.php");
                        exit;
                    case 2:
                        header("Location: ../vista/administracion.php");
                        exit;
                    case 3:
                        header("Location: ../vista/admin.php");
                        exit;
                }
            } else {
               // header("Location: ../vista/login.php?error=Credenciales+incorrectas");
                //exit;
            }
        }
    }
}
?>
