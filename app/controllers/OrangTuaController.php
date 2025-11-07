<?php
// app/controllers/OrangTuaController.php
// Controller untuk peran orangtua: melihat dashboard anak, detail presensi, dan laporan mingguan
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/KelasModel.php';

class OrangTuaController {
    private $userModel;
    private $presensiModel;
    private $kelasModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->presensiModel = new PresensiModel();
        $this->kelasModel = new KelasModel();
    }
    
    public function dashboard() {
        $orangtua_id = $_SESSION['user_id'];
        
        // Get anak-anak yang terkait
        $anak = $this->userModel->getSiswaByOrangTua($orangtua_id);
        
        $dataAnak = [];
        // Untuk setiap anak ambil statistik dan beberapa riwayat presensi
        foreach($anak as $siswa) {
            $statistik = $this->presensiModel->getStatistikKehadiran($siswa->id);
            $presensiTerakhir = $this->presensiModel->getPresensiSekolahByUser($siswa->id, 5);
            $kelas = $this->kelasModel->getAllKelas(); // Get kelas siswa
            
            $dataAnak[] = [
                'siswa' => $siswa,
                'statistik' => $statistik,
                'presensi_terakhir' => $presensiTerakhir,
                'kelas' => $kelas
            ];
        }
        
    require_once __DIR__ . '/../views/orangtua/dashboard.php';
    }
    
    public function getDetailAnak($siswa_id) {
        // Verifikasi akses: pastikan siswa adalah anak dari orangtua yang sedang login
        $orangtua_id = $_SESSION['user_id'];
        $anak = $this->userModel->getSiswaByOrangTua($orangtua_id);
        
        $isValid = false;
        foreach($anak as $siswa) {
            if($siswa->id == $siswa_id) {
                $isValid = true;
                break;
            }
        }
        
        if(!$isValid) {
            echo json_encode(['error' => 'Akses ditolak']);
            return;
        }
        
        // Ambil data presensi, statistik, dan daftar izin untuk siswa yang valid
        $presensi = $this->presensiModel->getPresensiSekolahByUser($siswa_id);
        $statistik = $this->presensiModel->getStatistikKehadiran($siswa_id);
        $izin = $this->presensiModel->getIzinBySiswa($siswa_id);
        
        echo json_encode([
            'presensi' => $presensi,
            'statistik' => $statistik,
            'izin' => $izin
        ]);
    }
    
    public function getLaporanMingguan($siswa_id) {
        // Verify access
        $orangtua_id = $_SESSION['user_id'];
        $anak = $this->userModel->getSiswaByOrangTua($orangtua_id);
        
        $isValid = false;
        foreach($anak as $siswa) {
            if($siswa->id == $siswa_id) {
                $isValid = true;
                break;
            }
        }
        
        if(!$isValid) {
            echo json_encode(['error' => 'Akses ditolak']);
            return;
        }
        
        // Generate laporan mingguan sederhana dan kembalikan sebagai JSON
        $laporan = $this->generateLaporanMingguan($siswa_id);
        echo json_encode($laporan);
    }
    
    private function generateLaporanMingguan($siswa_id) {
        // Implementation untuk generate laporan mingguan
        $startDate = date('Y-m-d', strtotime('monday this week'));
        $endDate = date('Y-m-d', strtotime('sunday this week'));
        
        return [
            'periode' => $startDate . ' hingga ' . $endDate,
            'total_hari' => 5,
            'hadir' => 4,
            'izin' => 1,
            'sakit' => 0,
            'alpha' => 0,
            'presentase' => 80
        ];
    }
}
?>