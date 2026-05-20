<?php

require_once __DIR__ . '/../models/MataPelajaranModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/LaporanModel.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';

class GuruController {
    private $mataPelajaranModel;
    private $presensiModel;
    private $userModel;
    private $laporanModel;
    private $presensiSesiModel;
    private $bukuIndukModel;
    
    public function __construct() {
        $this->mataPelajaranModel = new MataPelajaranModel();
        $this->presensiModel = new PresensiModel();
        $this->userModel = new UserModel();
        $this->laporanModel = new LaporanModel();
        $this->presensiSesiModel = new PresensiSesiModel();
        $this->bukuIndukModel = new BukuIndukModel();
    }
    
    public function dashboard() {
        // Dashboard guru: hitung total siswa di semua mata pelajaran yang dia ampu
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->mataPelajaranModel->getMataPelajaranByGuru($guru_id);
        $totalSiswa = 0;
        $presensiAktif = 0;
        
        foreach($kelasSaya as $kelas) {
            $siswa = $this->mataPelajaranModel->getSiswaInMataPelajaran($kelas->id);
            $kelas->siswa = $siswa; // Attach siswa to kelas object for view
            $kelas->total_siswa = count($siswa); // Attach count to kelas object
            $totalSiswa += count($siswa);
        }
        
        $aktivitasTerbaru = [];
        
    require_once __DIR__ . '/../views/guru/dashboard.php';
    }
    
    public function kelas() {
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->mataPelajaranModel->getMataPelajaranByGuru($guru_id);
        
        
        // Per mata pelajaran, lampirkan daftar siswa, total, laporan hari ini, dan info sesi aktif
        foreach($kelasSaya as $kelas) {
            $kelas->siswa = $this->mataPelajaranModel->getSiswaInMataPelajaran($kelas->id);
            // total siswa (use dedicated method for efficiency)
            $kelas->total_siswa = $this->mataPelajaranModel->getTotalSiswaByMataPelajaran($kelas->id);
        }
        
    require_once __DIR__ . '/../views/guru/kelas.php';
    }

    public function presensiMapel() {
        $this->presensiSesiModel->closeExpiredSessions();
        $guru_id = $_SESSION['user_id'];
        $mapelSaya = $this->presensiSesiModel->getManageForGuru($guru_id);
        $selectedJadwalId = (int) ($_GET['kelas_id'] ?? 0);
        $selectedSesiId = (int) ($_GET['sesi_id'] ?? 0);
        $sesiMapel = [];
        $detailPresensi = [];
        $selectedJadwal = null;
        $selectedSesi = null;

        if ($selectedJadwalId) {
            foreach ($mapelSaya as $jadwal) {
                if ((int) $jadwal->id === $selectedJadwalId) {
                    $selectedJadwal = $jadwal;
                    break;
                }
            }
            if ($selectedJadwal) {
                $sesiMapel = $this->presensiSesiModel->getSessionsWithStatsByKelas($selectedJadwalId, $guru_id);
            }
            if ($selectedJadwal && $selectedSesiId) {
                $selectedSesi = $this->presensiSesiModel->getSessionForGuru($selectedSesiId, $guru_id);
                $belongsToSelectedGroup = false;
                if ($selectedSesi) {
                    foreach ($sesiMapel as $sesi) {
                        if ((int) $sesi->id === (int) $selectedSesi->id) {
                            $belongsToSelectedGroup = true;
                            break;
                        }
                    }
                }
                if (!$selectedSesi || !$belongsToSelectedGroup) {
                    $selectedSesi = null;
                    $selectedSesiId = 0;
                }
            }
            if ($selectedJadwal && $selectedSesi) {
                $detailPresensi = $this->presensiModel->getLaporanPresensiKelas($selectedJadwalId, null, $selectedSesiId);
            }
        }

        require_once __DIR__ . '/../views/guru/presensi_mapel.php';
    }
    
    public function laporan() {
        $this->presensiSesiModel->closeExpiredSessions();
        $guru_id = $_SESSION['user_id'];
        $kelasSaya = $this->presensiSesiModel->getManageForGuru($guru_id);
        $kelas_id = (int) ($_GET['kelas_id'] ?? 0);
        $selected_sesi_id = (int) ($_GET['sesi_id'] ?? 0);
        $laporan = [];

        foreach ($kelasSaya as $kelas) {
            $kelas->siswa = $this->mataPelajaranModel->getSiswaInMataPelajaran($kelas->id);
        }

        if ($kelas_id) {
            $report = $this->buildGuruMapelReport($guru_id, $kelas_id, $selected_sesi_id);
            if (!$report) {
                $_SESSION['error'] = 'Mata pelajaran atau sesi tidak ditemukan.';
                header('Location: ' . BASE_URL . '/index.php?action=guru_laporan');
                exit();
            }

            $laporan[$report['selected_jadwal']->id] = [
                'sessions' => $report['sessions'],
                'selected_sesi' => $report['selected_sesi'],
                'presensi' => $report['presensi'],
                'laporan_kemajuan' => $this->getLaporanKemajuanSesiRows($report['selected_sesi'])
            ];
        }

        require_once __DIR__ . '/../views/guru/laporan.php';
    }

    private function buildGuruMapelReport($guru_id, $jadwal_id, $sesi_id = 0) {
        $selectedJadwal = null;
        foreach ($this->presensiSesiModel->getManageForGuru($guru_id) as $jadwal) {
            if ((int) $jadwal->id === (int) $jadwal_id) {
                $selectedJadwal = $jadwal;
                break;
            }
        }

        if (!$selectedJadwal) {
            return null;
        }

        $sessions = $this->presensiSesiModel->getSessionsWithStatsByKelas($selectedJadwal->id, $guru_id);
        $selectedSesi = null;
        if ($sesi_id) {
            foreach ($sessions as $session) {
                if ((int) $session->id === (int) $sesi_id) {
                    $selectedSesi = $this->presensiSesiModel->getSessionForGuru($sesi_id, $guru_id);
                    break;
                }
            }
            if (!$selectedSesi) {
                return null;
            }
        } elseif (!empty($sessions)) {
            $selectedSesi = $sessions[0];
        }

        $reportJadwalId = $selectedSesi ? (int) $selectedSesi->jadwal_mata_pelajaran_id : (int) $selectedJadwal->id;
        $presensi = $selectedSesi
            ? $this->presensiModel->getLaporanPresensiKelas($reportJadwalId, null, $selectedSesi->id)
            : $this->presensiModel->getLaporanPresensiKelas($reportJadwalId, date('Y-m-d'));

        return [
            'selected_jadwal' => $selectedJadwal,
            'sessions' => $sessions,
            'selected_sesi' => $selectedSesi,
            'presensi' => $presensi,
            'summary' => $this->summarizePresensiMapel($presensi),
            'periode_text' => $this->formatReportPeriode($selectedSesi),
            'report_jadwal_id' => $reportJadwalId
        ];
    }

    private function summarizePresensiMapel($presensi) {
        $summary = [
            'total_siswa' => count($presensi),
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0
        ];

        foreach ($presensi as $row) {
            $jenis = $row->jenis ?? null;
            if (!$row->status || !$jenis) {
                $summary['alpha']++;
            } elseif ($jenis === 'hadir' && $row->status === 'valid') {
                $summary['hadir']++;
            } elseif (isset($summary[$jenis])) {
                $summary[$jenis]++;
            }
        }

        return $summary;
    }

    private function formatReportPeriode($session) {
        if (!$session) {
            return date('d/m/Y');
        }

        return date('d/m/Y H:i', strtotime($session->waktu_buka)) . ' - ' . date('H:i', strtotime($session->waktu_tutup));
    }

    private function getPresensiMapelStatusText($row) {
        if (!$row->status || !$row->jenis) {
            return 'Belum Presensi';
        }

        $jenisMap = [
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha'
        ];

        return $jenisMap[$row->jenis] ?? ucfirst($row->jenis);
    }

    private function getPresensiMapelLokasiText($row) {
        if (($row->jenis ?? '') !== 'hadir') {
            return '-';
        }

        if ($row->status === 'valid') {
            return 'Valid';
        }

        if ($row->status === 'invalid') {
            return 'Invalid';
        }

        return '-';
    }

    private function getPresensiMapelKeteranganText($row) {
        if (!empty($row->alasan)) {
            return $row->alasan;
        }

        if (($row->jenis ?? '') === 'hadir') {
            return 'Presensi Normal';
        }

        return '-';
    }

    private function getLaporanKemajuanSesiRows($session) {
        if (!$session || empty($session->laporan_kemajuan)) {
            return [];
        }

        return [(object) [
            'tanggal' => $session->waktu_buka,
            'created_at' => $session->waktu_buka,
            'guru_nama' => $_SESSION['user_nama'] ?? '-',
            'catatan' => $session->laporan_kemajuan
        ]];
    }

    private function getGuruMonthlyMapelRows($guru_id, $selectedJadwal, $startDate, $endDate) {
        $db = new Database();
        $sql = 'SELECT COALESCE(pm.id, 0) as id,
                       bi.id as user_id,
                       bi.nis,
                       bi.nama,
                       pm.status,
                       COALESCE(pm.waktu, s.waktu_buka) as waktu,
                       pm.jenis,
                       pm.alasan,
                       s.id as sesi_id,
                       j.nama_mata_pelajaran,
                       k.nama_kelas,
                       u.nama as guru_nama
                FROM presensi_mapel_sesi s
                INNER JOIN jadwal_mata_pelajaran j ON s.jadwal_mata_pelajaran_id = j.id
                INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                LEFT JOIN users u ON j.guru_pengampu = u.id
                INNER JOIN jadwal_mata_pelajaran_siswa js ON js.jadwal_mata_pelajaran_id = j.id
                INNER JOIN buku_induk bi ON js.siswa_id = bi.id
                LEFT JOIN presensi_mapel pm ON pm.presensi_sesi_id = s.id AND pm.user_id = bi.id
                WHERE DATE(s.waktu_buka) BETWEEN :start_date AND :end_date
                  AND j.kelas_jadwal_id = :kelas_jadwal_id
                  AND j.nama_mata_pelajaran = :nama_mata_pelajaran
                  AND j.guru_pengampu = :guru_id
                ORDER BY bi.nama ASC, s.waktu_buka ASC';
        $db->query($sql);
        $db->bind(':start_date', $startDate);
        $db->bind(':end_date', $endDate);
        $db->bind(':kelas_jadwal_id', (int) $selectedJadwal->kelas_jadwal_id);
        $db->bind(':nama_mata_pelajaran', $selectedJadwal->nama_mata_pelajaran);
        $db->bind(':guru_id', (int) $guru_id);
        return $db->resultSet();
    }

    private function renderMonthlyAttendanceExport($presensi, $report_title, $bulan, $tahun, $asPdf = false) {
        $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulan_name = $bulan_names[(int) $bulan - 1] ?? $bulan;
        $daysInMonth = (int) date('t', strtotime($tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01'));
        $rows = $this->buildMonthlyAttendanceRows($presensi, $daysInMonth);

        if ($asPdf) {
            ?><!DOCTYPE html>
<html><head><meta charset="utf-8"><title><?php echo htmlspecialchars($report_title); ?></title>
<style>body{font-family:Arial,sans-serif;font-size:11px;margin:16px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #333;padding:4px;text-align:center}.name{text-align:left;min-width:180px}.kop{text-align:center;border-bottom:3px solid #000;margin-bottom:12px;padding-bottom:8px}@media print{.no-print{display:none}body{margin:0}}</style>
</head><body><button class="no-print" onclick="window.print()">Cetak / Simpan PDF</button>
<div class="kop"><h2>SMK NEGERI 7 Yogyakarta</h2><p>Jalan Gowongan Kidul Blok JT3 No.416, Gowongan, Kec. Jetis, Kota Yogyakarta, DIY 55232</p></div>
<h3><?php echo htmlspecialchars($report_title); ?></h3><p>Bulan: <?php echo htmlspecialchars($bulan_name . ' ' . $tahun); ?></p>
<?php $this->echoMonthlyAttendanceTable($rows, $daysInMonth); ?></body></html><?php
            exit;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Bulanan_' . $bulan_name . '_' . $tahun . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>table{border-collapse:collapse}th,td{border:1px solid #333;padding:4px;text-align:center}.name{text-align:left;min-width:180px}</style></head><body>';
        echo '<h2>SMK NEGERI 7 Yogyakarta</h2><h3>' . htmlspecialchars($report_title) . '</h3><p>Bulan: ' . htmlspecialchars($bulan_name . ' ' . $tahun) . '</p>';
        $this->echoMonthlyAttendanceTable($rows, $daysInMonth);
        echo '</body></html>';
        exit;
    }

    private function buildMonthlyAttendanceRows($presensi, $daysInMonth) {
        $students = [];
        foreach ($presensi as $row) {
            $studentId = $row->user_id ?? $row->siswa_id ?? $row->id ?? $row->nama;
            if (!isset($students[$studentId])) {
                $students[$studentId] = [
                    'nis' => $row->nis ?? $studentId,
                    'nama' => $row->nama ?? '-',
                    'days' => array_fill(1, $daysInMonth, ''),
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0
                ];
            }
            if (empty($row->waktu)) continue;
            $day = (int) date('j', strtotime($row->waktu));
            if ($day < 1 || $day > $daysInMonth) continue;
            $code = $this->getAttendanceExportCode($row);
            if ($code === '') continue;
            $existing = $students[$studentId]['days'][$day];
            $students[$studentId]['days'][$day] = ($existing === '' || strpos($existing, $code) !== false) ? ($existing ?: $code) : $existing . '/' . $code;
        }
        foreach ($students as &$student) {
            foreach ($student['days'] as $code) {
                if (strpos($code, 'H') !== false) $student['hadir']++;
                elseif (strpos($code, 'I') !== false) $student['izin']++;
                elseif (strpos($code, 'S') !== false) $student['sakit']++;
                elseif (strpos($code, 'A') !== false) $student['alpha']++;
            }
        }
        return array_values($students);
    }

    private function getAttendanceExportCode($row) {
        $jenis = $row->jenis ?? null;
        if (!$jenis && empty($row->status)) return 'A';
        if ($jenis === 'hadir') return 'H';
        if ($jenis === 'izin') return 'I';
        if ($jenis === 'sakit') return 'S';
        if ($jenis === 'alpha') return 'A';
        return '';
    }

    private function echoMonthlyAttendanceTable($rows, $daysInMonth) {
        echo '<table><tr><th rowspan="2">Urut</th><th rowspan="2">NIPD/NIS</th><th rowspan="2" class="name">Nama Lengkap</th><th rowspan="2">L/P</th><th colspan="' . $daysInMonth . '">Tanggal</th><th colspan="4">Jumlah</th></tr><tr>';
        for ($day = 1; $day <= $daysInMonth; $day++) echo '<th>' . $day . '</th>';
        echo '<th>H</th><th>I</th><th>S</th><th>A</th></tr>';
        $no = 1;
        foreach ($rows as $row) {
            echo '<tr><td>' . $no++ . '</td><td>' . htmlspecialchars($row['nis']) . '</td><td class="name">' . htmlspecialchars($row['nama']) . '</td><td></td>';
            for ($day = 1; $day <= $daysInMonth; $day++) echo '<td>' . htmlspecialchars($row['days'][$day]) . '</td>';
            echo '<td>' . $row['hadir'] . '</td><td>' . $row['izin'] . '</td><td>' . $row['sakit'] . '</td><td>' . $row['alpha'] . '</td></tr>';
        }
        echo '</table><p>Ket: H = Hadir, I = Izin, S = Sakit, A = Alpha</p>';
    }
    
    public function bukaPresensiKelas() {
        header('Content-Type: application/json');
        $guru_id = $_SESSION['user_id'];
        $mapel_id = (int) ($_POST['kelas_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $multiple = isset($_POST['repeat_enabled']) && $_POST['repeat_enabled'] === '1';
        if (!$mapel_id) {
            echo json_encode(['success' => false, 'message' => 'Mata pelajaran tidak dipilih.']);
            exit;
        }

        if ($multiple) {
            $tanggalSelesai = $_POST['repeat_until'] ?? null;
            $repeatEveryWeeks = $_POST['repeat_every_weeks'] ?? 1;
            if (!$tanggal || !$tanggalSelesai) {
                echo json_encode(['success' => false, 'message' => 'Tanggal mulai dan selesai wajib diisi.']);
                exit;
            }
            $created = $this->presensiSesiModel->createMultipleSessions($mapel_id, $guru_id, $tanggal, $tanggalSelesai, $repeatEveryWeeks);
            echo json_encode([
                'success' => $created !== false && $created > 0,
                'count' => (int) $created,
                'message' => $created ? $created . ' sesi presensi berhasil dibuat sesuai jadwal.' : 'Tidak ada sesi dibuat. Pastikan rentang tanggal memuat hari jadwal mapel.'
            ]);
            exit;
        }

        $ok = $this->presensiSesiModel->createSession($mapel_id, $guru_id, $tanggal);
        echo json_encode(['success' => (bool) $ok, 'message' => $ok ? 'Sesi presensi dibuat sesuai jadwal.' : 'Sesi gagal dibuat. Pastikan tanggal sesuai hari jadwal mapel dan Anda guru pengampu.']);
        exit;
    }
    
    public function tutupPresensiKelas() {
        header('Content-Type: application/json');
        $guru_id = $_SESSION['user_id'];
        $sesi_id = (int) ($_POST['sesi_id'] ?? 0);
        $mapel_id = (int) ($_POST['kelas_id'] ?? 0);
        if ($sesi_id) {
            $ok = $this->presensiSesiModel->closeSessionByIdForGuru($sesi_id, $guru_id);
            echo json_encode(['success' => (bool) $ok, 'message' => $ok ? 'Sesi presensi ditutup.' : 'Sesi tidak ditemukan atau sudah ditutup.']);
            exit;
        }

        if (!$mapel_id) {
            echo json_encode(['success' => false, 'message' => 'Sesi tidak dipilih.']);
            exit;
        }
        $ok = $this->presensiSesiModel->closeSession($mapel_id, $guru_id);
        echo json_encode(['success' => (bool) $ok, 'message' => $ok ? 'Sesi presensi ditutup.' : 'Tidak ada sesi aktif yang bisa ditutup.']);
        exit;
    }

    public function hapusPresensiMapelSesi() {
        header('Content-Type: application/json');
        $guru_id = $_SESSION['user_id'];
        $sesi_id = (int) ($_POST['sesi_id'] ?? 0);

        if (!$sesi_id) {
            echo json_encode(['success' => false, 'message' => 'Sesi tidak dipilih.']);
            exit;
        }

        $ok = $this->presensiSesiModel->deleteSessionForGuru($sesi_id, $guru_id);
        echo json_encode(['success' => (bool) $ok, 'message' => $ok ? 'Sesi presensi berhasil dihapus.' : 'Sesi tidak ditemukan atau bukan milik Anda.']);
        exit;
    }

    public function simpanLaporanKemajuanMapel() {
        header('Content-Type: application/json');
        $guru_id = $_SESSION['user_id'];
        $sesi_id = (int) ($_POST['sesi_id'] ?? 0);
        $laporan_kemajuan = $_POST['laporan_kemajuan'] ?? '';

        if (!$sesi_id) {
            echo json_encode(['success' => false, 'message' => 'Sesi tidak dipilih.']);
            exit;
        }

        $ok = $this->presensiSesiModel->updateLaporanKemajuanForGuru($sesi_id, $guru_id, $laporan_kemajuan);
        echo json_encode(['success' => (bool) $ok, 'message' => $ok ? 'Laporan kemajuan berhasil disimpan.' : 'Sesi tidak ditemukan atau bukan milik Anda.']);
        exit;
    }
    
    private function simpanLaporanKemajuan($mata_pelajaran_id, $guru_id, $catatan) {
        // Build data for model
        $data = [
            'mata_pelajaran_id' => $mata_pelajaran_id,
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
    
    public function getPresensiKelas($mata_pelajaran_id) {
        header('Content-Type: application/json');
        $sesi_id = $_GET['sesi_id'] ?? null;
        echo json_encode($this->presensiModel->getLaporanPresensiKelas($mata_pelajaran_id, null, $sesi_id));
        exit;
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
        $mata_pelajaran_id = $_GET['kelas_id'] ?? null; // frontend sends kelas_id but it's actually mata_pelajaran_id
        $sesi_id = $_GET['sesi_id'] ?? null;
        
        if (!$mata_pelajaran_id) {
            die('Mata pelajaran tidak dipilih');
        }

        if (!$sesi_id) {
            die('Sesi presensi tidak dipilih');
        }

        $report = $this->buildGuruMapelReport($guru_id, (int) $mata_pelajaran_id, (int) $sesi_id);
        if (!$report) {
            die('Anda tidak mengajar mata pelajaran atau sesi ini tidak ditemukan');
        }

        $selectedKelas = $report['selected_jadwal'];
        $presensi = $report['presensi'];
        $periode_text = $report['periode_text'];
        $session = $report['selected_sesi'];
        $summary = $report['summary'];
        $totalSiswa = $summary['total_siswa'];
        $hadir = $summary['hadir'];
        $izin = $summary['izin'];
        $sakit = $summary['sakit'];
        $alpha = $summary['alpha'];
        
        $laporan_kemajuan = $this->getLaporanKemajuanSesiRows($session);
        
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Presensi_' . str_replace(' ', '_', $selectedKelas->nama_mata_pelajaran) . '_' . date('Y-m-d') . '.xls"');
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
        echo '<h2>' . htmlspecialchars($selectedKelas->nama_mata_pelajaran) . '</h2>';
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
        $mata_pelajaran_id = $_GET['kelas_id'] ?? null; // frontend sends kelas_id but it's actually mata_pelajaran_id
        $sesi_id = $_GET['sesi_id'] ?? null;
        
        if (!$mata_pelajaran_id) {
            die('Mata pelajaran tidak dipilih');
        }

        if (!$sesi_id) {
            die('Sesi presensi tidak dipilih');
        }

        $report = $this->buildGuruMapelReport($guru_id, (int) $mata_pelajaran_id, (int) $sesi_id);
        if (!$report) {
            die('Anda tidak mengajar mata pelajaran atau sesi ini tidak ditemukan');
        }

        $selectedKelas = $report['selected_jadwal'];
        $presensi = $report['presensi'];
        $periode_text = $report['periode_text'];
        $session = $report['selected_sesi'];
        $summary = $report['summary'];
        $totalSiswa = $summary['total_siswa'];
        $hadir = $summary['hadir'];
        $izin = $summary['izin'];
        $sakit = $summary['sakit'];
        $alpha = $summary['alpha'];
        
        $laporan_kemajuan = $this->getLaporanKemajuanSesiRows($session);
        
        ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Mata Pelajaran - <?php echo htmlspecialchars($selectedKelas->nama_mata_pelajaran); ?></title>
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
    <h2><?php echo htmlspecialchars($selectedKelas->nama_mata_pelajaran); ?></h2>
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
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
        <?php
        exit;
    }
    
    public function ubahStatusPresensi() {
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'] ?? null;
            $mata_pelajaran_id = $_POST['kelas_id'] ?? null; // frontend sends kelas_id but it's actually mata_pelajaran_id
            $jenis = $_POST['jenis'] ?? 'hadir';
            $alasan = $_POST['alasan'] ?? null;
            $foto_bukti = $_POST['foto_bukti'] ?? null;
            $sesi_id = $_POST['sesi_id'] ?? null;
            $guru_id = $_SESSION['user_id'];
            
            // Validasi input
            if (!$siswa_id || !$mata_pelajaran_id) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                return;
            }
            
            // Validasi guru mengajar mata pelajaran ini
            $kelasSaya = $this->mataPelajaranModel->getMataPelajaranByGuru($guru_id);
            $isMyClass = false;
            foreach($kelasSaya as $kelas) {
                if ($kelas->id == $mata_pelajaran_id) {
                    $isMyClass = true;
                    break;
                }
            }
            
            if (!$isMyClass) {
                echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke mata pelajaran ini']);
                return;
            }
            
            // Update atau buat presensi
            $result = $this->presensiModel->createOrUpdatePresensiKelas(
                $siswa_id,
                $mata_pelajaran_id,
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
