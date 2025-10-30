<?php
class AuthController {


    public function login() {
        // if ($_POST['email'] && $_POST['password']) {
        //     $email = $_POST['email'];
        //     $password = $_POST['password'];
        
        //     // if ($this->verificarCredenciales($email, $password)) {
        //     //     $usuario = $this->obtenerUsuario($email);
        //     //     $rol = $this->determinarRol($email);
                
        //     //     // Establecer todas las variables
        //     //     $_SESSION['user_id'] = $usuario['id'];
        //     //     $_SESSION['user_email'] = $email;
        //     //     $_SESSION['user_nombre'] = $usuario['nombre'];
        //     //     $_SESSION['user_role'] = $rol;
        //     //     $_SESSION['logged_in'] = true;
                
        //     //     error_log("LOGIN EXITOSO: " . $email . " - ID: " . $usuario['id'] . " - Session: " . session_id());
        //     //      session_regenerate_id(true);
        //     //     $this->redirigirSegunRol($rol);
        //     // } else {
        //     //     header('Location: ../views/login.html?error=1');
        //     // }
        //      echo $email;
        // echo $password;
        if ($_SERVER["REQUEST_METHOD"] == "POST"){
     
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            if($this->verificarCredenciales($email, $password)){
                if(strcmp($email, "estudiante@universidad.edu")===0){
                    header('Location: http://localhost/GestionDBProyecto/vista/estudiante.html');
                }
                if(strcmp($email, "docente@universidad.edu")===0){
                    header('Location: http://localhost/GestionDBProyecto/vista/administracion.html');
                }
                if(strcmp($email, "admin@universidad.edu")===0){
                    header('Location: http://localhost/GestionDBProyecto/vista/admin.html');
                }
                
               return true;
            }else{
                return false;
            }
           
        }
        //}
    }
    
    public function verificarCredenciales($email, $password) {
        
        

        $usuarios = [
             'estudiante@universidad.edu' => '123456',
             'docente@universidad.edu' => '123456', 
             'admin@universidad.edu' => '123456'
         ];
        
        return isset($usuarios[$email]) && $usuarios[$email] === $password;
    }
    
    private function obtenerUsuario($email) {
        $usuarios = [
            'estudiante@universidad.edu' => [
                'id' => 1,
                'nombre' => 'Juan Estudiante'
            ],
            'docente@universidad.edu' => [
                'id' => 2, 
                'nombre' => 'María Docente'
            ],
            'admin@universidad.edu' => [
                'id' => 3,
                'nombre' => 'Carlos Admin'
            ]
        ];
        
        return $usuarios[$email] ?? ['id' => 0, 'nombre' => 'Usuario'];
    }

}

