<?php
// app/controllers/SiswaController.php
// Controller untuk peran siswa: melihat dashboard, presensi, riwayat, dan mengajukan izin
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';
require_once __DIR__ . '/../models/PresensiSekolahSesiModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';

class SiswaController {
    private $presensiModel;
    private $locationModel;
    private $kelasModel;
    private $presensiSesiModel;
    private $presensiSekolahSesiModel;
    private $bukuIndukModel;
    
    public function __construct() {
        $this->presensiModel = new PresensiModel();
        $this->locationModel = new LocationModel();
        $this->kelasModel = new KelasModel();
        $this->presensiSesiModel = new PresensiSesiModel();
        $this->presensiSekolahSesiModel = new PresensiSekolahSesiModel();
        $this->bukuIndukModel = new BukuIndukModel();
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
            // Pastikan sesi sekolah yang sudah kadaluarsa ditutup
            $this->presensiSekolahSesiModel->closeExpiredSessions();
            $activeSession = $this->presensiSekolahSesiModel->getActiveSession();

            if (!$activeSession) {
                // Tidak ada sesi aktif -> tolak penyimpanan
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tidak ada sesi presensi sekolah aktif saat ini.']);
                return;
            }

            // Cek lokasi dan jarak
            $distance = $this->locationModel->getDistance($latitude, $longitude);
            $isValid = $this->locationModel->validateLocation($latitude, $longitude);

            // Cegah duplikat presensi untuk session yang sama
            if ($this->presensiModel->hasPresensiInSchoolSession($user_id, $activeSession->id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan presensi untuk sesi sekolah ini.']);
                return;
            }

            $data = [
                'presensi_sekolah_sesi_id' => $activeSession->id,
                'user_id' => $user_id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'jarak' => $distance,
                'status' => $isValid ? 'valid' : 'invalid',
                'jenis' => 'hadir'
            ];

            // Simpan presensi dan kembalikan hasil serta apakah lokasi valid
            header('Content-Type: application/json');
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
            
            // Ajukan izin melalui model, set flash message lalu redirect
            if($this->presensiModel->ajukanIzin($data)) {
                $_SESSION['success'] = 'Izin berhasil diajukan!';
            } else {
                $_SESSION['error'] = 'Gagal mengajukan izin!';
            }
            
            header('Location: ' . BASE_URL . '/public/index.php?action=siswa_izin');
        }
    }

    public function bukuInduk() {
        $user_id = $_SESSION['user_id'];
        $record = $this->bukuIndukModel->getByUserId($user_id);
        require_once __DIR__ . '/../views/siswa/buku_induk.php';
    }

    public function saveBukuInduk() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $user_id = $_SESSION['user_id'];
        $data = [
            'user_id' => $user_id,
            'nama' => trim($_POST['nama']),
            'nis' => trim($_POST['nis']),
            'nisn' => trim($_POST['nisn']),
            'tempat_lahir' => trim($_POST['tempat_lahir']),
            'tanggal_lahir' => $_POST['tanggal_lahir'],
            'alamat' => trim($_POST['alamat']),
            'dokumen_pdf' => null
        ];

        if(isset($_FILES['dokumen_pdf']) && $_FILES['dokumen_pdf']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->handlePdfUpload($_FILES['dokumen_pdf']);
            if(!$upload['success']) {
                $_SESSION['error'] = $upload['message'];
                header('Location: ' . BASE_URL . '/public/index.php?action=siswa_buku_induk');
                exit();
            }
            $data['dokumen_pdf'] = $upload['path'];
        } else {
            $data['dokumen_pdf'] = $_POST['existing_pdf'] ?? null;
        }

        if($this->bukuIndukModel->upsert($data)) {
            $_SESSION['success'] = 'Data buku induk diperbarui.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan data.';
        }

        header('Location: ' . BASE_URL . '/public/index.php?action=siswa_buku_induk');
        exit();
    }

    private function handlePdfUpload($file) {
        $allowed = ['application/pdf'];
        if(!in_array($file['type'], $allowed)) {
            return ['success' => false, 'message' => 'File harus PDF.'];
        }
        $uploadDir = __DIR__ . '/../../public/uploads/buku_induk';
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $safeName = uniqid('buku-induk-') . '.pdf';
        $target = $uploadDir . '/' . $safeName;
        if(move_uploaded_file($file['tmp_name'], $target)) {
            $relative = BASE_URL . '/public/uploads/buku_induk/' . $safeName;
            return ['success' => true, 'path' => $relative];
        }
        return ['success' => false, 'message' => 'Gagal upload dokumen.'];
    }
}
?>