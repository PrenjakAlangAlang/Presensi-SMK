<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';

class AuthController {
    private $userModel;
    private $bukuIndukModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->bukuIndukModel = new BukuIndukModel();
    }
    
    public function login() {
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            // panggil model untuk cek kredensial
            if($this->userModel->login($email, $password)) {
                // jika sukses, redirect sesuai role user
                $this->redirectBasedOnRole($_SESSION['user_role']);
            } else {
                // jika gagal tampilkan pesan error di view login
                $error = "Email/NIS atau password salah!";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public function registerSiswa() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit();
        }

        $data = [
            'user_id' => null,
            'nama' => trim($_POST['nama'] ?? ''),
            'nis' => trim($_POST['nis'] ?? ''),
            'nisn' => trim($_POST['nisn'] ?? ''),
            'tempat_lahir' => trim($_POST['tempat_lahir'] ?? ''),
            'tanggal_lahir' => $_POST['tanggal_lahir'] ?? '',
            'alamat' => trim($_POST['alamat'] ?? ''),
            'nama_ayah' => isset($_POST['nama_ayah']) ? trim($_POST['nama_ayah']) : null,
            'nama_ibu' => isset($_POST['nama_ibu']) ? trim($_POST['nama_ibu']) : null,
            'nama_wali' => isset($_POST['nama_wali']) ? trim($_POST['nama_wali']) : null,
            'no_telp_ortu' => isset($_POST['no_telp_ortu']) ? trim($_POST['no_telp_ortu']) : null,
            'email_ortu' => isset($_POST['email_ortu']) ? trim($_POST['email_ortu']) : null,
            'dokumen_ijasah' => null,
            'dokumen_pas_foto' => null,
            'dokumen_akta_kelahiran' => null,
            'dokumen_kk' => null,
            'password' => $_POST['password'] ?? null
        ];
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($data['nama'] === '' || $data['nis'] === '' || $data['nisn'] === '' || $data['tempat_lahir'] === '' || $data['tanggal_lahir'] === '' || $data['alamat'] === '') {
            $error = 'Lengkapi data wajib untuk daftar sebagai siswa.';
            $showRegister = true;
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (strlen((string) $data['password']) < 6) {
            $error = 'Password minimal 6 karakter.';
            $showRegister = true;
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        if ($data['password'] !== $passwordConfirm) {
            $error = 'Konfirmasi password tidak cocok.';
            $showRegister = true;
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        if ($this->bukuIndukModel->getByNis($data['nis'])) {
            $error = 'NIS sudah terdaftar. Silakan login menggunakan NIS tersebut atau hubungi admin.';
            $showRegister = true;
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }

        if ($this->bukuIndukModel->upsert($data)) {
            $success = 'Pendaftaran siswa berhasil. Silakan login menggunakan NIS dan password yang Anda buat.';
        } else {
            $error = 'Gagal mendaftarkan siswa. Periksa kembali NIS atau hubungi admin.';
            $showRegister = true;
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function logout() {
        // Destroy session dan kembali ke halaman login
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?action=login');
        exit();
    }
    
    private function redirectBasedOnRole($role) {
        switch($role) {
            case 'admin':
                // admin -> dashboard admin
                header('Location: ' . BASE_URL . '/index.php?action=admin_dashboard');
                break;
            case 'admin_kesiswaan':
                header('Location: ' . BASE_URL . '/index.php?action=admin_kesiswaan_dashboard');
                break;
            case 'guru':
                // guru -> dashboard guru
                header('Location: ' . BASE_URL . '/index.php?action=guru_dashboard');
                break;
            case 'siswa':
                // siswa -> dashboard siswa
                header('Location: ' . BASE_URL . '/index.php?action=siswa_dashboard');
                break;
            default:
                // fallback ke login jika role tidak dikenali
                header('Location: ' . BASE_URL . '/index.php?action=login');
        }
        exit();
    }
}
?>
