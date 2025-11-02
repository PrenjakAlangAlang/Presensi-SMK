<?php
// app/controllers/AuthController.php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function login() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            if($this->userModel->login($email, $password)) {
                $this->redirectBasedOnRole($_SESSION['user_role']);
            } else {
                $error = "Email atau password salah!";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/public/index.php?action=login');
        exit();
    }
    
    private function redirectBasedOnRole($role) {
        switch($role) {
            case 'admin':
                header('Location: ' . BASE_URL . '/public/index.php?action=admin_dashboard');
                break;
            case 'guru':
                header('Location: ' . BASE_URL . '/public/index.php?action=guru_dashboard');
                break;
            case 'siswa':
                header('Location: ' . BASE_URL . '/public/index.php?action=siswa_dashboard');
                break;
            case 'orangtua':
                header('Location: ' . BASE_URL . '/public/index.php?action=orangtua_dashboard');
                break;
            default:
                header('Location: ' . BASE_URL . '/public/index.php?action=login');
        }
        exit();
    }
}
?>