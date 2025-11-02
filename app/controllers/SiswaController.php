<?php
// app/controllers/SiswaController.php
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';

class SiswaController {
    private $presensiModel;
    private $locationModel;
    private $kelasModel;
    private $presensiSesiModel;
    
    public function __construct() {
        $this->presensiModel = new PresensiModel();
        $this->locationModel = new LocationModel();
        $this->kelasModel = new KelasModel();
        $this->presensiSesiModel = new PresensiSesiModel();
    }
    
    public function dashboard() {
        $user_id = $_SESSION['user_id'];
        $statistik = $this->presensiModel->getStatistikKehadiran($user_id);
        $presensiTerakhir = $this->presensiModel->getPresensiSekolahByUser($user_id, 5);
        $kelas = $this->kelasModel->getAllKelas();
        
    require_once __DIR__ . '/../views/siswa/dashboard.php';
    }
    
    public function presensi() {
        $lokasiSekolah = $this->locationModel->getLokasiSekolah();
        $kelas = $this->kelasModel->getAllKelas();
        // Attach active session info to each kelas so frontend can enable presensi per kelas
        foreach ($kelas as $k) {
            $k->sesi_aktif = $this->presensiSesiModel->getActiveSessionByKelas($k->id);
        }

        require_once __DIR__ . '/../views/siswa/presensi.php';
    }
    
    public function riwayat() {
        $user_id = $_SESSION['user_id'];
        $presensiSekolah = $this->presensiModel->getPresensiSekolahByUser($user_id);
        $presensiKelas = $this->presensiModel->getPresensiKelasByUser($user_id);
    require_once __DIR__ . '/../views/siswa/riwayat.php';
    }
    
    public function izin() {
        $user_id = $_SESSION['user_id'];
        $riwayatIzin = $this->presensiModel->getIzinBySiswa($user_id);
    require_once __DIR__ . '/../views/siswa/izin.php';
    }
    
    public function submitPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            
            $distance = $this->locationModel->getDistance($latitude, $longitude);
            $isValid = $this->locationModel->validateLocation($latitude, $longitude);
            
            $data = [
                'user_id' => $user_id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'jarak' => $distance,
                'status' => $isValid ? 'valid' : 'invalid',
                'jenis' => 'hadir'
            ];
            
            if($this->presensiModel->recordPresensiSekolah($data)) {
                echo json_encode(['success' => true, 'valid' => $isValid]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mencatat presensi']);
            }
        }
    }
    
    public function submitPresensiKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $kelas_id = $_POST['kelas_id'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            
            $distance = $this->locationModel->getDistance($latitude, $longitude);
            $isValid = $this->locationModel->validateLocation($latitude, $longitude);
            
            $data = [
                'user_id' => $user_id,
                'kelas_id' => $kelas_id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'jarak' => $distance,
                'status' => $isValid ? 'valid' : 'invalid'
            ];
            
            // Attach active presensi session id for the kelas
            $activeSession = $this->presensiSesiModel->getActiveSessionByKelas($kelas_id);
            if (!$activeSession) {
                echo json_encode(['success' => false, 'message' => 'Belum ada sesi presensi aktif untuk kelas ini.']);
                return;
            }

            // Prevent duplicate presensi for the same session
            if ($this->presensiModel->hasPresensiInSession($user_id, $activeSession->id)) {
                echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan presensi untuk sesi ini.']);
                return;
            }

            $data['presensi_sesi_id'] = $activeSession->id;

            if($this->presensiModel->recordPresensiKelas($data)) {
                echo json_encode(['success' => true, 'valid' => $isValid]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mencatat presensi kelas']);
            }
        }
    }
    
    public function ajukanIzin() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'siswa_id' => $_SESSION['user_id'],
                'tanggal' => $_POST['tanggal'],
                'alasan' => $_POST['alasan']
            ];
            
            if($this->presensiModel->ajukanIzin($data)) {
                $_SESSION['success'] = 'Izin berhasil diajukan!';
            } else {
                $_SESSION['error'] = 'Gagal mengajukan izin!';
            }
            
            header('Location: ' . BASE_URL . '/public/index.php?action=siswa_izin');
        }
    }
}
?>