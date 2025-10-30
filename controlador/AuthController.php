<?php
session_start();

class AuthController {
    
    public function login() {
        if ($_POST['email'] && $_POST['password']) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            // Verificar credenciales
            if ($this->verificarCredenciales($email, $password)) {
                $rol = $this->determinarRol($email);
                
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $rol;
                
                // Redirigir según el rol
                $this->redirigirSegunRol($rol);
            } else {
                header('Location: ../views/login.html?error=1');
            }
        }
    }
    
    private function verificarCredenciales($email, $password) {
        // Usuarios de prueba
        $usuarios = [
            'estudiante@universidad.edu' => '123456',
            'docente@universidad.edu' => '123456', 
            'admin@universidad.edu' => '123456'
        ];
        
        return isset($usuarios[$email]) && $usuarios[$email] === $password;
    }
    
    private function determinarRol($email) {
        if ($email === 'estudiante@universidad.edu') return 'estudiante';
        if ($email === 'docente@universidad.edu') return 'docente';
        if ($email === 'admin@universidad.edu') return 'admin';
        return 'estudiante';
    }
    
    private function redirigirSegunRol($rol) {
        switch($rol) {
            case 'estudiante': header('Location: ../views/estudiante.html'); break;
            case 'docente': header('Location: ../views/docente.html'); break;
            case 'admin': header('Location: ../views/admin.html'); break;
        }
        exit();
    }
    
    public function logout() {
        session_destroy();
        header('Location: ../views/login.html');
        exit();
    }
}

// Ejecutar acción
if ($_GET['action'] === 'login') {
    (new AuthController())->login();
} elseif ($_GET['action'] === 'logout') {
    (new AuthController())->logout();
}
?>