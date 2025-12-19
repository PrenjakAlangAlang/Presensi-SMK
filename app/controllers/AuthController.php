<?php
// app/controllers/AuthController.php
// Controller untuk autentikasi: login dan logout
// Menangani validasi login, penyimpanan session, dan redirect berdasarkan role
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function login() {
        // jika form login disubmit lakukan proses autentikasi
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            // panggil model untuk cek kredensial
            if($this->userModel->login($email, $password)) {
                // jika sukses, redirect sesuai role user
                $this->redirectBasedOnRole($_SESSION['user_role']);
            } else {
                // jika gagal tampilkan pesan error di view login
                $error = "Email atau password salah!";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }
    
    public function logout() {
        // Destroy session dan kembali ke halaman login
        session_destroy();
        header('Location: ' . BASE_URL . '/public/index.php?action=login');
        exit();
    }
    
    private function redirectBasedOnRole($role) {
        switch($role) {
            case 'admin':
                // admin -> dashboard admin
                header('Location: ' . BASE_URL . '/public/index.php?action=admin_dashboard');
                break;
            case 'admin_kesiswaan':
                header('Location: ' . BASE_URL . '/public/index.php?action=admin_kesiswaan_dashboard');
                break;
            case 'guru':
                // guru -> dashboard guru
                header('Location: ' . BASE_URL . '/public/index.php?action=guru_dashboard');
                break;
            case 'siswa':
                // siswa -> dashboard siswa
                header('Location: ' . BASE_URL . '/public/index.php?action=siswa_dashboard');
                break;
            case 'orangtua':
                // orangtua -> dashboard orangtua
                header('Location: ' . BASE_URL . '/public/index.php?action=orangtua_dashboard');
                break;
            default:
                // fallback ke login jika role tidak dikenali
                header('Location: ' . BASE_URL . '/public/index.php?action=login');
        }
        exit();
    }
}
?>