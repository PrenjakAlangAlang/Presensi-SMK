<?php
// app/controllers/GuruController.php
// Controller untuk peran guru: melihat kelas, membuka/tutup sesi presensi, dan laporan
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/LaporanModel.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';

class GuruController {
    private $kelasModel;
    private $presensiModel;
    private $userModel;
    private $laporanModel;
    private $presensiSesiModel;
    
    public function __construct() {
        $this->kelasModel = new KelasModel();
        $this->presensiModel = new PresensiModel();
        $this->userModel = new UserModel();
        $this->laporanModel = new LaporanModel();
        $this->presensiSesiModel = new PresensiSesiModel();
    }
    
    public function dashboard() {
        // Dashboard guru: hitung total siswa di semua kelas yang dia asuh
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        $totalSiswa = 0;
        
        foreach($kelasSaya as $kelas) {
            $siswa = $this->kelasModel->getSiswaInKelas($kelas->id);
            $totalSiswa += count($siswa);
        }
        
        // Ambil presensi terbaru untuk semua kelas yang diajar guru
        $aktivitasTerbaru = [];
        foreach($kelasSaya as $kelas) {
            $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas->id, date('Y-m-d'));
            foreach($presensi as $p) {
                if($p->status) { // Hanya yang sudah presensi
                    $p->nama_kelas = $kelas->nama_kelas;
                    $aktivitasTerbaru[] = $p;
                }
            }
        }
        
        // Urutkan berdasarkan waktu terbaru dan ambil 10 teratas
        usort($aktivitasTerbaru, function($a, $b) {
            return strtotime($b->waktu ?? '1970-01-01') - strtotime($a->waktu ?? '1970-01-01');
        });
        $aktivitasTerbaru = array_slice($aktivitasTerbaru, 0, 10);
        
    require_once __DIR__ . '/../views/guru/dashboard.php';
    }
    
    public function kelas() {
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        
        // Get siswa for each class
        // Per kelas, lampirkan daftar siswa, total, laporan hari ini, dan info sesi aktif
        foreach($kelasSaya as $kelas) {
            $kelas->siswa = $this->kelasModel->getSiswaInKelas($kelas->id);
            // total siswa (use dedicated method for efficiency)
            $kelas->total_siswa = $this->kelasModel->getTotalSiswaByKelas($kelas->id);
            $kelas->presensi_hari_ini = $this->presensiModel->getLaporanPresensiKelas($kelas->id);
            // Attach sesi aktif info from DB
            $kelas->sesi_aktif = $this->presensiSesiModel->getActiveSessionByKelas($kelas->id);
        }
        
    require_once __DIR__ . '/../views/guru/kelas.php';
    }
    
    public function laporan() {
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        
        $laporan = [];
        // allow selecting sesi via GET param
        $requested_sesi = isset($_GET['sesi_id']) ? intval($_GET['sesi_id']) : null;

    // Untuk tiap kelas, ambil data siswa, sesi, dan laporan kemajuan terkait sesi
    foreach($kelasSaya as $kelas) {
            // ensure siswa list and total are available for the view
            $kelas->siswa = $this->kelasModel->getSiswaInKelas($kelas->id);
            $kelas->total_siswa = $this->kelasModel->getTotalSiswaByKelas($kelas->id);
            // sessions for this class
            $sessions = $this->presensiSesiModel->getSessionsByKelas($kelas->id);
            $selectedSesi = null;
            if ($requested_sesi) {
                // try to find requested sesi in this kelas
                foreach($sessions as $s) {
                    if ($s->id == $requested_sesi) {
                        $selectedSesi = $s;
                        break;
                    }
                }
            }
            // default to latest session if none requested
            if (!$selectedSesi && count($sessions) > 0) {
                $selectedSesi = $sessions[0];
            }
            
            // fetch presensi for selected sesi if exists, else fall back to today's data
            if ($selectedSesi) {
                // Ambil presensi berdasarkan sesi terpilih
                $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas->id, null, $selectedSesi->id);
            } else {
                // Ambil presensi hari ini jika tidak ada sesi yang dipilih
                $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas->id, date('Y-m-d'));
            }

            // get laporan kemajuan and pick those that match the session timeframe (if any)
            $allLaporan = $this->laporanModel->getLaporanByKelas($kelas->id);
            $laporanPerSesi = [];
            if ($selectedSesi) {
                $start = $selectedSesi->waktu_buka;
                $end = $selectedSesi->waktu_tutup ?: date('Y-m-d H:i:s');
                foreach($allLaporan as $l) {
                    if (isset($l->created_at) && $l->created_at >= $start && $l->created_at <= $end) {
                        $laporanPerSesi[] = $l;
                    }
                }
            }

            $laporan[$kelas->id] = [
                'kelas' => $kelas,
                'sessions' => $sessions,
                'selected_sesi' => $selectedSesi,
                'presensi' => $presensi,
                'laporan_kemajuan' => $laporanPerSesi,
            ];
        }
        
    require_once __DIR__ . '/../views/guru/laporan.php';
    }
    
    public function bukaPresensiKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelas_id = $_POST['kelas_id'];
            $guru_id = $_SESSION['user_id'];
            
            // Persist session to DB
            $created = $this->presensiSesiModel->createSession($kelas_id, $guru_id);

            if ($created) {
                // Also set quick session in PHP session for immediate UI effect
                $_SESSION['kelas_buka_' . $kelas_id] = [
                    'waktu_buka' => time(),
                    'guru_id' => $guru_id,
                    'status' => 'buka'
                ];
                echo json_encode(['success' => true, 'message' => 'Presensi kelas dibuka!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal membuka sesi presensi']);
            }
        }
    }
    
    public function tutupPresensiKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $kelas_id = $_POST['kelas_id'];
            $guru_id = $_SESSION['user_id'];
            $catatan = $_POST['catatan'] ?? '';
            
            // Hapus session kelas yang dibuka
            unset($_SESSION['kelas_buka_' . $kelas_id]);

            // Close session in DB
            $closed = $this->presensiSesiModel->closeSession($kelas_id, $guru_id);

            // Simpan laporan kemajuan
            $saved = $this->simpanLaporanKemajuan($kelas_id, $guru_id, $catatan);

            if ($closed && $saved) {
                echo json_encode(['success' => true, 'message' => 'Presensi kelas ditutup!']);
            } else if ($saved) {
                echo json_encode(['success' => true, 'message' => 'Presensi kelas ditutup (session DB tidak berubah)']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menutup sesi atau menyimpan laporan kemajuan']);
            }
        }
    }
    
    private function simpanLaporanKemajuan($kelas_id, $guru_id, $catatan) {
        // Build data for model
        $data = [
            'kelas_id' => $kelas_id,
            'guru_id' => $guru_id,
            'catatan' => $catatan
        ];

        try {
            // Panggil model untuk menyimpan laporan kemajuan, kembalikan boolean sukses/gagal
            $result = $this->laporanModel->saveLaporanKemajuan($data);
            return $result !== false;
        } catch (Exception $e) {
            // Log error if you have a logger, for now return false
            return false;
        }
    }
    
    public function getPresensiKelas($kelas_id) {
        $sesi_id = isset($_GET['sesi_id']) ? intval($_GET['sesi_id']) : null;
        if ($sesi_id) {
            $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas_id, null, $sesi_id);
        } else {
            $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas_id, date('Y-m-d'));
        }
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