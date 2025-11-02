<?php
// app/controllers/AdminController.php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';

class AdminController {
    private $userModel;
    private $kelasModel;
    private $locationModel;
    private $presensiModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->kelasModel = new KelasModel();
        $this->locationModel = new LocationModel();
        $this->presensiModel = new PresensiModel();
    }
    
    public function dashboard() {
        $totalSiswa = count($this->userModel->getUsersByRole('siswa'));
        $totalGuru = count($this->userModel->getUsersByRole('guru'));
        $totalKelas = count($this->kelasModel->getAllKelas());
        
    require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function users() {
        $users = $this->userModel->getAllUsers();
    require_once __DIR__ . '/../views/admin/users.php';
    }
    
    public function kelas() {
        $kelas = $this->kelasModel->getAllKelas();
        $guru = $this->userModel->getUsersByRole('guru');
        $totalSiswa = count($this->userModel->getUsersByRole('siswa'));
        // expose the model to the view so it can call helper methods
        $kelasModel = $this->kelasModel;
    require_once __DIR__ . '/../views/admin/kelas.php';
    }

    // Create Kelas (from add form)
    public function createKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama_kelas' => $_POST['nama_kelas'],
                'tahun_ajaran' => $_POST['tahun_ajaran'],
                'wali_kelas' => $_POST['wali_kelas'] ?? null
            ];

            if($this->kelasModel->createKelas($data)) {
                $_SESSION['success'] = 'Kelas berhasil dibuat!';
            } else {
                $_SESSION['error'] = 'Gagal membuat kelas!';
            }

            header('Location: ' . BASE_URL . '/public/index.php?action=admin_kelas');
            exit;
        }
    }

    // Update kelas
    public function updateKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $_POST['id'],
                'nama_kelas' => $_POST['nama_kelas'],
                'tahun_ajaran' => $_POST['tahun_ajaran'],
                'wali_kelas' => $_POST['wali_kelas'] ?? null
            ];

            if($this->kelasModel->updateKelas($data)) {
                $_SESSION['success'] = 'Kelas berhasil diperbarui!';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui kelas!';
            }

            header('Location: ' . BASE_URL . '/public/index.php?action=admin_kelas');
            exit;
        }
    }

    // Delete kelas
    public function deleteKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            if($this->kelasModel->deleteKelas($id)) {
                $_SESSION['success'] = 'Kelas berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus kelas!';
            }
            header('Location: ' . BASE_URL . '/public/index.php?action=admin_kelas');
            exit;
        }
    }

    // API: get siswa in kelas (JSON)
    public function getSiswaDalamKelas() {
        if(isset($_GET['kelas_id'])) {
            $kelas_id = $_GET['kelas_id'];
            $siswa = $this->kelasModel->getSiswaInKelas($kelas_id);
            header('Content-Type: application/json');
            echo json_encode($siswa);
            exit;
        }
    }

    // API: get siswa available (JSON)
    public function getSiswaTersedia() {
        $siswa = $this->kelasModel->getAvailableSiswa();
        header('Content-Type: application/json');
        echo json_encode($siswa);
        exit;
    }

    // API: add siswa to kelas
    public function addSiswaToKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'];
            $kelas_id = $_POST['kelas_id'];
            $ok = $this->kelasModel->addSiswaToKelas($siswa_id, $kelas_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }

    // API: remove siswa from kelas
    public function removeSiswaFromKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'];
            $kelas_id = $_POST['kelas_id'];
            $ok = $this->kelasModel->removeSiswaFromKelas($siswa_id, $kelas_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }

    // API: get siswa assigned to an orangtua (JSON)
    public function getSiswaOrangtua() {
        if(isset($_GET['orangtua_id'])) {
            $orangtua_id = $_GET['orangtua_id'];
            $siswa = $this->userModel->getSiswaByOrangTua($orangtua_id);
            header('Content-Type: application/json');
            echo json_encode($siswa);
            exit;
        }
    }

    // API: get siswa available to be assigned to an orangtua (not yet assigned)
    public function getSiswaTersediaOrangtua() {
        $siswa = $this->userModel->getSiswaWithoutOrangtua();
        header('Content-Type: application/json');
        echo json_encode($siswa);
        exit;
    }

    // API: add siswa to orangtua
    public function addSiswaToOrangtua() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'];
            $orangtua_id = $_POST['orangtua_id'];
            $ok = $this->userModel->addSiswaToOrangtua($siswa_id, $orangtua_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }

    // API: remove siswa from orangtua
    public function removeSiswaFromOrangtua() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'];
            $orangtua_id = $_POST['orangtua_id'];
            $ok = $this->userModel->removeSiswaFromOrangtua($siswa_id, $orangtua_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }
    
    public function lokasi() {
        $lokasi = $this->locationModel->getLokasiSekolah();
    require_once __DIR__ . '/../views/admin/lokasi.php';
    }
    
    public function laporan() {
        $presensi = $this->presensiModel->getLaporanPresensiKelas(1); // Default kelas 1
    require_once __DIR__ . '/../views/admin/laporan.php';
    }
    
    public function createUser() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama' => $_POST['nama'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];
            
            if($this->userModel->createUser($data)) {
                $_SESSION['success'] = 'User berhasil dibuat!';
            } else {
                $_SESSION['error'] = 'Gagal membuat user!';
            }
            
            header('Location: ' . BASE_URL . '/public/index.php?action=admin_users');
        }
    }

    public function updateUser() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $_POST['id'],
                'nama' => $_POST['nama'],
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ];

            if($this->userModel->updateUser($data)) {
                $_SESSION['success'] = 'User berhasil diperbarui!';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui user!';
            }

            header('Location: ' . BASE_URL . '/public/index.php?action=admin_users');
            exit;
        }
    }
    
    public function updateLokasi() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama_sekolah' => $_POST['nama_sekolah'],
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude'],
                'radius_presensi' => $_POST['radius_presensi'],
                'updated_by' => $_SESSION['user_id']
            ];
            
            if($this->locationModel->updateLokasiSekolah($data)) {
                $_SESSION['success'] = 'Lokasi sekolah berhasil diperbarui!';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui lokasi sekolah!';
            }
            
            header('Location: ' . BASE_URL . '/public/index.php?action=admin_lokasi');
        }
    }
}
?>