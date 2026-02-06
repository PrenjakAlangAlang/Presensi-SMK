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
        $presensiAktif = 0;
        
        foreach($kelasSaya as $kelas) {
            $siswa = $this->kelasModel->getSiswaInKelas($kelas->id);
            $totalSiswa += count($siswa);
            
            // Hitung kelas dengan sesi presensi aktif
            if($this->presensiSesiModel->isSessionActive($kelas->id)) {
                $presensiAktif++;
            }
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

            // Get active session to mark absent students as alpha
            $activeSession = $this->presensiSesiModel->getActiveSessionByKelas($kelas_id);
            $alphaCount = 0;
            
            if ($activeSession) {
                // Mark absent students as alpha before closing
                $alphaCount = $this->presensiModel->markAbsentStudentsAsAlphaKelas($kelas_id, $activeSession->id);
            }

            // Close session in DB
            $closed = $this->presensiSesiModel->closeSession($kelas_id, $guru_id);

            // Simpan laporan kemajuan
            $saved = $this->simpanLaporanKemajuan($kelas_id, $guru_id, $catatan);

            if ($closed && $saved) {
                $message = 'Presensi kelas ditutup!';
                if ($alphaCount > 0) {
                    $message .= " $alphaCount siswa ditandai alpha.";
                }
                echo json_encode(['success' => true, 'message' => $message, 'alpha_count' => $alphaCount]);
            } else if ($saved) {
                echo json_encode(['success' => true, 'message' => 'Presensi kelas ditutup (session DB tidak berubah)', 'alpha_count' => $alphaCount]);
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
    
    public function exportExcel() {
        $guru_id = $_SESSION['user_id'];
        $kelas_id = $_GET['kelas_id'] ?? null;
        $sesi_id = $_GET['sesi_id'] ?? null;
        
        if (!$kelas_id) {
            die('Kelas tidak dipilih');
        }
        
        // Validasi guru mengajar kelas ini
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        $isMyClass = false;
        $selectedKelas = null;
        foreach($kelasSaya as $kelas) {
            if ($kelas->id == $kelas_id) {
                $isMyClass = true;
                $selectedKelas = $kelas;
                break;
            }
        }
        
        if (!$isMyClass) {
            die('Anda tidak mengajar kelas ini');
        }
        
        // Get presensi data
        if ($sesi_id) {
            $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas_id, null, $sesi_id);
            $session = $this->presensiSesiModel->getSessionById($sesi_id);
            $periode_text = $session ? date('d/m/Y H:i', strtotime($session->waktu_buka)) : 'Sesi Dipilih';
        } else {
            $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas_id, date('Y-m-d'));
            $periode_text = date('d F Y');
        }
        
        // Get laporan kemajuan
        $laporan_kemajuan = [];
        if ($sesi_id) {
            $allLaporan = $this->laporanModel->getLaporanByKelas($kelas_id);
            $session = $this->presensiSesiModel->getSessionById($sesi_id);
            if ($session) {
                $start = $session->waktu_buka;
                $end = $session->waktu_tutup ?: date('Y-m-d H:i:s');
                foreach($allLaporan as $l) {
                    if ($l->tanggal >= $start && $l->tanggal <= $end) {
                        $laporan_kemajuan[] = $l;
                    }
                }
            }
        }
        
        // Calculate statistics
        $hadir = 0;
        $izin = 0;
        $sakit = 0;
        $alpha = 0;
        
        foreach($presensi as $p) {
            if ($p->status == 'valid') {
                if ($p->jenis == 'hadir') $hadir++;
                elseif ($p->jenis == 'izin') $izin++;
                elseif ($p->jenis == 'sakit') $sakit++;
            } elseif ($p->status == null) {
                $alpha++;
            }
        }
        
        $totalSiswa = $this->kelasModel->getTotalSiswaByKelas($kelas_id);
        $belumPresensi = $totalSiswa - ($hadir + $izin + $sakit + $alpha);
        $alpha += $belumPresensi;
        
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Presensi_' . str_replace(' ', '_', $selectedKelas->nama_kelas) . '_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');
        
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; }';
        echo 'th, td { border: 1px solid black; padding: 8px; text-align: left; }';
        echo 'th { background-color: #f2f2f2; font-weight: bold; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        // Kop Surat
        echo '<div style="text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px;">';
        echo '<h1 style="margin: 5px 0; font-size: 18px; text-transform: uppercase;">SMK NEGERI 7 Yogyakarta</h1>';
      
        echo '<p style="margin: 3px 0; font-size: 10px;">Jalan Gowongan Kidul Blok JT3 No.416, Gowongan, Kec. Jetis, Kota Yogyakarta, DIY 55232</p>';
        echo '<p style="margin: 3px 0; font-size: 10px;">Telp: (0274) 512403 | Email: smknegeri7jogja@smkn7jogja.sch.id | Website: https://www.smkn7jogja.sch.id/</p>';
        echo '</div>';
        
        echo '<h1>Laporan Presensi Kelas</h1>';
        echo '<h2>' . htmlspecialchars($selectedKelas->nama_kelas) . '</h2>';
        echo '<p>Periode: ' . htmlspecialchars($periode_text) . '</p>';
        echo '<br/>';
        
        echo '<h3>Ringkasan Kehadiran</h3>';
        echo '<table>';
        echo '<tr><th>Kategori</th><th>Jumlah</th></tr>';
        echo '<tr><td>Total Siswa</td><td>' . $totalSiswa . '</td></tr>';
        echo '<tr><td>Hadir</td><td>' . $hadir . '</td></tr>';
        echo '<tr><td>Izin</td><td>' . $izin . '</td></tr>';
        echo '<tr><td>Sakit</td><td>' . $sakit . '</td></tr>';
        echo '<tr><td>Alpha</td><td>' . $alpha . '</td></tr>';
        echo '</table>';
        echo '<br/>';
        
        if (!empty($laporan_kemajuan)) {
            echo '<h3>Laporan Kemajuan</h3>';
            echo '<table>';
            echo '<tr><th>Tanggal</th><th>Guru</th><th>Catatan</th></tr>';
            foreach($laporan_kemajuan as $l) {
                echo '<tr>';
                echo '<td>' . date('d/m/Y H:i', strtotime($l->tanggal)) . '</td>';
                echo '<td>' . htmlspecialchars($l->guru_nama ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($l->catatan ?? '-') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<br/>';
        }
        
        echo '<h3>Detail Presensi</h3>';
        echo '<table>';
        echo '<tr><th>No</th><th>Nama Siswa</th><th>Status</th><th>Waktu</th><th>Lokasi</th><th>Keterangan</th></tr>';
        
        $no = 1;
        foreach($presensi as $p) {
            $jenis = $p->jenis ?? 'hadir';
            $jenisMap = [
                'hadir' => 'Hadir',
                'izin' => 'Izin',
                'sakit' => 'Sakit',
                'alpha' => 'Alpha'
            ];
            $statusText = $jenisMap[$jenis] ?? 'Tidak Hadir';
            
            if (!$p->status) {
                $statusText = 'Belum Presensi';
            }
            
            $lokasi = '-';
            if ($jenis == 'hadir') {
                if ($p->status == 'valid') $lokasi = 'Valid';
                elseif ($p->status == 'invalid') $lokasi = 'Invalid';
            }
            
            $keterangan = '-';
            if (($jenis == 'izin' || $jenis == 'sakit') && $p->alasan) {
                $keterangan = $p->alasan;
            }
            
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($p->nama ?? 'Siswa ' . $no) . '</td>';
            echo '<td>' . $statusText . '</td>';
            echo '<td>' . ($p->waktu ? date('H:i', strtotime($p->waktu)) : '-') . '</td>';
            echo '<td>' . $lokasi . '</td>';
            echo '<td>' . htmlspecialchars($keterangan) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</body></html>';
        exit;
    }
    
    public function exportPDF() {
        $guru_id = $_SESSION['user_id'];
        $kelas_id = $_GET['kelas_id'] ?? null;
        $sesi_id = $_GET['sesi_id'] ?? null;
        
        if (!$kelas_id) {
            die('Kelas tidak dipilih');
        }
        
        // Validasi guru mengajar kelas ini
        $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
        $isMyClass = false;
        $selectedKelas = null;
        foreach($kelasSaya as $kelas) {
            if ($kelas->id == $kelas_id) {
                $isMyClass = true;
                $selectedKelas = $kelas;
                break;
            }
        }
        
        if (!$isMyClass) {
            die('Anda tidak mengajar kelas ini');
        }
        
        // Get presensi data
        if ($sesi_id) {
            $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas_id, null, $sesi_id);
            $session = $this->presensiSesiModel->getSessionById($sesi_id);
            $periode_text = $session ? date('d/m/Y H:i', strtotime($session->waktu_buka)) : 'Sesi Dipilih';
        } else {
            $presensi = $this->presensiModel->getLaporanPresensiKelas($kelas_id, date('Y-m-d'));
            $periode_text = date('d F Y');
        }
        
        // Get laporan kemajuan
        $laporan_kemajuan = [];
        if ($sesi_id) {
            $allLaporan = $this->laporanModel->getLaporanByKelas($kelas_id);
            $session = $this->presensiSesiModel->getSessionById($sesi_id);
            if ($session) {
                $start = $session->waktu_buka;
                $end = $session->waktu_tutup ?: date('Y-m-d H:i:s');
                foreach($allLaporan as $l) {
                    if ($l->tanggal >= $start && $l->tanggal <= $end) {
                        $laporan_kemajuan[] = $l;
                    }
                }
            }
        }
        
        // Calculate statistics
        $hadir = 0;
        $izin = 0;
        $sakit = 0;
        $alpha = 0;
        
        foreach($presensi as $p) {
            if ($p->status == 'valid') {
                if ($p->jenis == 'hadir') $hadir++;
                elseif ($p->jenis == 'izin') $izin++;
                elseif ($p->jenis == 'sakit') $sakit++;
            } elseif ($p->status == null) {
                $alpha++;
            }
        }
        
        $totalSiswa = $this->kelasModel->getTotalSiswaByKelas($kelas_id);
        $belumPresensi = $totalSiswa - ($hadir + $izin + $sakit + $alpha);
        $alpha += $belumPresensi;
        
        ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Kelas - <?php echo htmlspecialchars($selectedKelas->nama_kelas); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 5px 0; font-size: 24px; color: #000; text-transform: uppercase; }
        
        .kop-surat p { margin: 3px 0; font-size: 12px; color: #555; }
        .kop-surat .separator { border-top: 2px solid #000; margin-top: 10px; }
        h1 { text-align: center; color: #333; }
        h2 { text-align: center; color: #666; margin-top: 5px; }
        h3 { color: #444; border-bottom: 2px solid #ddd; padding-bottom: 5px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .summary { display: flex; justify-content: space-around; margin: 20px 0; }
        .summary-item { text-align: center; padding: 15px; }
        .summary-item h4 { margin: 5px 0; font-size: 24px; }
        .summary-item p { margin: 5px 0; color: #666; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .print-btn:hover { background: #0056b3; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Cetak / Simpan PDF</button>
    
    <!-- Kop Surat -->
    <div class="kop-surat">
        <h1>SMK NEGERI 7 Yogyakarta</h1>
        
        <p>Jalan Gowongan Kidul Blok JT3 No.416, Gowongan, Kec. Jetis, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55232</p>
        <p>Telp: (0274) 512403 | Email: smknegeri7jogja@smkn7jogja.sch.id | Website: https://www.smkn7jogja.sch.id/</p>
        <div class="separator"></div>
    </div>
    
    <h1>Laporan Presensi Kelas</h1>
    <h2><?php echo htmlspecialchars($selectedKelas->nama_kelas); ?></h2>
    <h3>Periode: <?php echo htmlspecialchars($periode_text); ?></h3>
    
    <h3>Ringkasan Kehadiran</h3>
    <div class="summary">
        <div class="summary-item">
            <h4><?php echo $totalSiswa; ?></h4>
            <p>Total Siswa</p>
        </div>
        <div class="summary-item">
            <h4><?php echo $hadir; ?></h4>
            <p>Hadir</p>
        </div>
        <div class="summary-item">
            <h4><?php echo $izin; ?></h4>
            <p>Izin</p>
        </div>
        <div class="summary-item">
            <h4><?php echo $sakit; ?></h4>
            <p>Sakit</p>
        </div>
        <div class="summary-item">
            <h4><?php echo $alpha; ?></h4>
            <p>Alpha</p>
        </div>
    </div>
    
    <?php if (!empty($laporan_kemajuan)): ?>
    <h3>Laporan Kemajuan</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Guru</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($laporan_kemajuan as $l): ?>
            <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($l->tanggal)); ?></td>
                <td><?php echo htmlspecialchars($l->guru_nama ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($l->catatan ?? '-'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    
    <h3>Detail Presensi</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Status</th>
                <th>Waktu</th>
                <th>Lokasi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($presensi as $p): 
                $jenis = $p->jenis ?? 'hadir';
                $jenisMap = [
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpha' => 'Alpha'
                ];
                $statusText = $jenisMap[$jenis] ?? 'Tidak Hadir';
                
                if (!$p->status) {
                    $statusText = 'Belum Presensi';
                }
                
                $lokasi = '-';
                if ($jenis == 'hadir') {
                    if ($p->status == 'valid') $lokasi = 'Valid';
                    elseif ($p->status == 'invalid') $lokasi = 'Invalid';
                }
                
                $keterangan = '-';
                if (($jenis == 'izin' || $jenis == 'sakit') && isset($p->alasan) && $p->alasan) {
                    $keterangan = $p->alasan;
                }
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($p->nama ?? 'Siswa ' . $no); ?></td>
                <td><?php echo $statusText; ?></td>
                <td><?php echo $p->waktu ? date('H:i', strtotime($p->waktu)) : '-'; ?></td>
                <td><?php echo $lokasi; ?></td>
                <td><?php echo htmlspecialchars($keterangan); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script>
        // Auto print dialog on load for PDF export
    </script>
</body>
</html>
        <?php
        exit;
    }
    
    public function ubahStatusPresensi() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'] ?? null;
            $kelas_id = $_POST['kelas_id'] ?? null;
            $jenis = $_POST['jenis'] ?? 'hadir';
            $alasan = $_POST['alasan'] ?? null;
            $foto_bukti = $_POST['foto_bukti'] ?? null;
            $sesi_id = $_POST['sesi_id'] ?? null;
            $guru_id = $_SESSION['user_id'];
            
            // Validasi input
            if (!$siswa_id || !$kelas_id) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                return;
            }
            
            // Validasi guru mengajar kelas ini
            $kelasSaya = $this->kelasModel->getKelasByGuru($guru_id);
            $isMyClass = false;
            foreach($kelasSaya as $kelas) {
                if ($kelas->id == $kelas_id) {
                    $isMyClass = true;
                    break;
                }
            }
            
            if (!$isMyClass) {
                echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke kelas ini']);
                return;
            }
            
            // Validasi alasan untuk izin/sakit
            if (($jenis === 'izin' || $jenis === 'sakit') && empty($alasan)) {
                echo json_encode(['success' => false, 'message' => 'Alasan harus diisi untuk status izin/sakit']);
                return;
            }
            
            // Update atau buat presensi
            $result = $this->presensiModel->createOrUpdatePresensiKelas(
                $siswa_id,
                $kelas_id,
                $jenis,
                $alasan,
                $foto_bukti,
                $sesi_id
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Status presensi berhasil diubah']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengubah status presensi']);
            }
        }
    }
}
?>