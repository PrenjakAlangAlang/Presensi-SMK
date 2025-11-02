<?php
// app/controllers/GuruController.php
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class GuruController {
    private $kelasModel;
    private $presensiModel;
    private $userModel;
    
    public function __construct() {
        $this->kelasModel = new KelasModel();
        $this->presensiModel = new PresensiModel();
        $this->userModel = new UserModel();
    }
    
    public function dashboard() {
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        $totalSiswa = 0;
        
        foreach($kelasSaya as $kelas) {
            $siswa = $this->kelasModel->getSiswaInKelas($kelas->id);
            $totalSiswa += count($siswa);
        }
        
    require_once __DIR__ . '/../views/guru/dashboard.php';
    }
    
    public function kelas() {
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        
        // Get siswa for each class
        foreach($kelasSaya as $kelas) {
            $kelas->siswa = $this->kelasModel->getSiswaInKelas($kelas->id);
            $kelas->presensi_hari_ini = $this->presensiModel->getLaporanPresensiKelas($kelas->id);
        }
        
    require_once __DIR__ . '/../views/guru/kelas.php';
    }
    
    public function laporan() {
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        
        $laporan = [];
        foreach($kelasSaya as $kelas) {
            $laporan[$kelas->id] = [
                'kelas' => $kelas,
                'presensi' => $this->presensiModel->getLaporanPresensiKelas($kelas->id, date('Y-m-d'))
            ];
        }
        
    require_once __DIR__ . '/../views/guru/laporan.php';
    }
    
    public function bukaPresensiKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelas_id = $_POST['kelas_id'];
            $guru_id = $_SESSION['user_id'];
            
            // Simpan session untuk kelas yang dibuka
            $_SESSION['kelas_buka_' . $kelas_id] = [
                'waktu_buka' => time(),
                'guru_id' => $guru_id,
                'status' => 'buka'
            ];
            
            echo json_encode(['success' => true, 'message' => 'Presensi kelas dibuka!']);
        }
    }
    
    public function tutupPresensiKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelas_id = $_POST['kelas_id'];
            $guru_id = $_SESSION['user_id'];
            $catatan = $_POST['catatan'] ?? '';
            
            // Hapus session kelas yang dibuka
            unset($_SESSION['kelas_buka_' . $kelas_id]);
            
            // Simpan laporan kemajuan
            $this->simpanLaporanKemajuan($kelas_id, $guru_id, $catatan);
            
            echo json_encode(['success' => true, 'message' => 'Presensi kelas ditutup!']);
        }
    }
    
    private function simpanLaporanKemajuan($kelas_id, $guru_id, $catatan) {
        // Implementation untuk menyimpan laporan kemajuan
        // Ini adalah placeholder - implementasi database sebenarnya
        return true;
    }
    
    public function getPresensiKelas($kelas_id) {
        $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas_id);
        echo json_encode($presensi);
    }
    
    public function exportLaporan() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelas_id = $_POST['kelas_id'];
            $tanggal_mulai = $_POST['tanggal_mulai'];
            $tanggal_selesai = $_POST['tanggal_selesai'];
            
            // Generate laporan PDF/Excel
            $this->generateLaporan($kelas_id, $tanggal_mulai, $tanggal_selesai);
        }
    }
    
    private function generateLaporan($kelas_id, $tanggal_mulai, $tanggal_selesai) {
        // Implementation untuk generate laporan
        // Placeholder untuk fungsi export
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="laporan_presensi.pdf"');
        // Generate PDF content here
        exit();
    }
}
?>