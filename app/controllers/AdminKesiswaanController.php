<?php
// app/controllers/AdminKesiswaanController.php
// Peran admin kesiswaan: kelola buku induk seluruh siswa dan kelola sesi presensi sekolah
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';
require_once __DIR__ . '/../models/PresensiSekolahSesiModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/LocationModel.php';

class AdminKesiswaanController {
    private $userModel;
    private $bukuIndukModel;
    private $presensiSekolahSesiModel;
    private $presensiModel;
    private $kelasModel;
    private $locationModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->bukuIndukModel = new BukuIndukModel();
        $this->presensiSekolahSesiModel = new PresensiSekolahSesiModel();
        $this->presensiModel = new PresensiModel();
        $this->kelasModel = new KelasModel();
        $this->locationModel = new LocationModel();
    }

    public function dashboard() {
        $totalSiswa = count($this->userModel->getUsersByRole('siswa'));
        $totalGuru = count($this->userModel->getUsersByRole('guru'));
        $sessions = $this->presensiSekolahSesiModel->getSessions();
        
        // Pagination variables
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;
        $total_records = count($sessions);
        $total_pages = ceil($total_records / $limit);
        
        require_once __DIR__ . '/../views/admin_kesiswaan/dashboard.php';
    }

    public function bukuInduk() {
        $siswa = $this->userModel->getUsersByRole('siswa');
        $records = $this->bukuIndukModel->getAll();
        require_once __DIR__ . '/../views/admin_kesiswaan/buku_induk.php';
    }

    public function saveBukuInduk() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $data = [
            'user_id' => $_POST['user_id'],
            'nama' => trim($_POST['nama']),
            'nis' => trim($_POST['nis']),
            'nisn' => trim($_POST['nisn']),
            'tempat_lahir' => trim($_POST['tempat_lahir']),
            'tanggal_lahir' => $_POST['tanggal_lahir'],
            'alamat' => trim($_POST['alamat']),
            'dokumen_pdf' => null
        ];

        // Handle upload dokumen PDF opsional
        if(isset($_FILES['dokumen_pdf']) && $_FILES['dokumen_pdf']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handlePdfUpload($_FILES['dokumen_pdf']);
            if(!$uploadResult['success']) {
                $_SESSION['error'] = $uploadResult['message'];
                header('Location: ' . BASE_URL . '/public/index.php?action=admin_kesiswaan_buku_induk');
                exit();
            }
            $data['dokumen_pdf'] = $uploadResult['path'];
        } else {
            // jika tidak ada upload, pakai path lama bila disediakan
            $data['dokumen_pdf'] = $_POST['existing_pdf'] ?? null;
        }

        if($this->bukuIndukModel->upsert($data)) {
            $_SESSION['success'] = 'Buku induk berhasil disimpan.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan buku induk.';
        }

        header('Location: ' . BASE_URL . '/public/index.php?action=admin_kesiswaan_buku_induk');
        exit();
    }

    // Presensi sekolah (sama seperti admin)
    public function presensiSekolah() {
        $this->presensiSekolahSesiModel->closeExpiredSessions();
        $sessions = $this->presensiSekolahSesiModel->getSessions();
        require_once __DIR__ . '/../views/admin_kesiswaan/presensi_sekolah.php';
    }

    public function createPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $waktu_buka = $_POST['waktu_buka'];
            $waktu_tutup = $_POST['waktu_tutup'];
            $note = $_POST['note'] ?? null;
            $created_by = $_SESSION['user_id'] ?? null;
            $id = $this->presensiSekolahSesiModel->createSession($waktu_buka, $waktu_tutup, $created_by, 1, $note);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$id, 'id' => $id]);
            exit;
        }
    }

    public function extendPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $new_waktu_tutup = $_POST['waktu_tutup'];
            $ok = $this->presensiSekolahSesiModel->extendSession($id, $new_waktu_tutup);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }

    public function closePresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $ok = $this->presensiSekolahSesiModel->closeSession($id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }

    public function getPresensiSekolahStatus() {
        $this->presensiSekolahSesiModel->closeExpiredSessions();
        $active = $this->presensiSekolahSesiModel->getActiveSession();
        header('Content-Type: application/json');
        if ($active) {
            $already = false;
            if (isset($_SESSION['user_id'])) {
                $uid = $_SESSION['user_id'];
                $already = $this->presensiModel->hasPresensiInSchoolSession($uid, $active->id);
            }
            echo json_encode(['active' => true, 'session' => $active, 'already_presenced' => (bool)$already]);
        } else {
            echo json_encode(['active' => false, 'already_presenced' => false]);
        }
        exit;
    }

    private function handlePdfUpload($file) {
        $allowed = ['application/pdf'];
        if(!in_array($file['type'], $allowed)) {
            return ['success' => false, 'message' => 'Hanya file PDF yang diperbolehkan.'];
        }
        $uploadDir = __DIR__ . '/../../public/uploads/buku_induk';
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $safeName = uniqid('buku-induk-') . '.pdf';
        $target = $uploadDir . '/' . $safeName;
        if(move_uploaded_file($file['tmp_name'], $target)) {
            $relative = BASE_URL . '/public/uploads/buku_induk/' . $safeName;
            return ['success' => true, 'path' => $relative];
        }
        return ['success' => false, 'message' => 'Gagal mengunggah dokumen.'];
    }

    public function laporan() {
        // Halaman laporan admin kesiswaan - presensi sekolah
        // Ambil list tanggal yang tersedia
        $tanggal_list = $this->presensiModel->getTanggalPresensiSekolah();
        
        // Ambil parameter dari GET, default ke tanggal pertama yang tersedia atau hari ini
        if (!empty($tanggal_list)) {
            $default_tanggal = $tanggal_list[0]->tanggal;
        } else {
            $default_tanggal = date('Y-m-d');
        }
        $tanggal = $_GET['tanggal'] ?? $default_tanggal;
        $filter_status = $_GET['status'] ?? null;
        $sesi_id = $_GET['sesi_id'] ?? null;
        
        // Ambil semua sesi untuk dropdown dan filter berdasarkan tanggal
        $all_sessions = $this->presensiSekolahSesiModel->getSessions();
        $sessions = [];
        if ($tanggal) {
            foreach($all_sessions as $s) {
                $session_date = date('Y-m-d', strtotime($s->waktu_buka));
                if ($session_date == $tanggal) {
                    $sessions[] = $s;
                }
            }
        }
        
        // Pagination settings
        $limit = 10; // Items per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $total_records = $this->presensiModel->countLaporanPresensiSekolah($tanggal, $sesi_id, $filter_status);
        $total_pages = ceil($total_records / $limit);
        
        // Ambil data presensi sekolah dan statistik
        $presensi = $this->presensiModel->getLaporanPresensiSekolah($tanggal, $sesi_id, $filter_status, $limit, $offset);
        $statistik = $this->presensiModel->getStatistikPresensiSekolah($tanggal);
        
        require_once __DIR__ . '/../views/admin_kesiswaan/laporan.php';
    }

    public function exportExcel() {
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $filter_status = $_GET['status'] ?? null;
        
        // Get all data (no pagination for export)
        $presensi = $this->presensiModel->getLaporanPresensiSekolah($tanggal, null, $filter_status);
        $statistik = $this->presensiModel->getStatistikPresensiSekolah($tanggal);
        
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Presensi_' . $tanggal . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output HTML table format for Excel
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
        echo '<body>';
        echo '<h2>Laporan Presensi Sekolah</h2>';
        echo '<p>Tanggal: ' . date('d F Y', strtotime($tanggal)) . '</p>';
        echo '<br>';
        
        // Statistik Summary
        echo '<h3>Ringkasan</h3>';
        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>Total Siswa</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpha</th><th>Persentase Kehadiran</th></tr>';
        $total_siswa = $statistik->total_siswa ?? 0;
        $hadir = $statistik->hadir ?? 0;
        $persentase = $total_siswa > 0 ? round(($hadir / $total_siswa) * 100, 2) : 0;
        echo '<tr>';
        echo '<td>' . $total_siswa . '</td>';
        echo '<td>' . ($statistik->hadir ?? 0) . '</td>';
        echo '<td>' . ($statistik->izin ?? 0) . '</td>';
        echo '<td>' . ($statistik->sakit ?? 0) . '</td>';
        echo '<td>' . ($statistik->alpha ?? 0) . '</td>';
        echo '<td>' . $persentase . '%</td>';
        echo '</tr></table>';
        echo '<br><br>';
        
        // Detail Data
        echo '<h3>Detail Presensi</h3>';
        echo '<table border="1" cellpadding="5">';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Nama Siswa</th>';
        echo '<th>Email</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Waktu</th>';
        echo '<th>Status</th>';
        echo '<th>Jarak (m)</th>';
        echo '<th>Alasan</th>';
        echo '<th>Bukti</th>';
        echo '</tr>';
        
        $no = 1;
        foreach($presensi as $p) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($p->nama ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($p->email ?? '') . '</td>';
            
            $waktuTs = (isset($p->waktu) && $p->waktu) ? strtotime($p->waktu) : null;
            echo '<td>' . ($waktuTs ? date('d M Y', $waktuTs) : '-') . '</td>';
            echo '<td>' . ($waktuTs ? date('H:i', $waktuTs) : '-') . '</td>';
            
            if (isset($p->status) && $p->status) {
                if ($p->status == 'valid') {
                    echo '<td>' . (isset($p->jenis) ? ucfirst($p->jenis) : 'Hadir') . '</td>';
                } else {
                    echo '<td>Tidak Valid</td>';
                }
            } else {
                echo '<td>Belum Presensi</td>';
            }
            
            echo '<td>' . (isset($p->jarak) ? round($p->jarak, 2) : '-') . '</td>';
            echo '<td>' . (isset($p->alasan) ? htmlspecialchars($p->alasan) : '-') . '</td>';
            echo '<td>' . (isset($p->foto_bukti) && $p->foto_bukti ? 'Ada' : '-') . '</td>';
            echo '</tr>';
        }
        
        echo '</table';
        echo '</body></html>';
        exit;
    }

    public function exportPDF() {
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $filter_status = $_GET['status'] ?? null;
        
        // Get all data (no pagination for export)
        $presensi = $this->presensiModel->getLaporanPresensiSekolah($tanggal, null, $filter_status);
        $statistik = $this->presensiModel->getStatistikPresensiSekolah($tanggal);
        
        // For PDF, we'll create a print-friendly HTML page
        ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi - <?php echo date('d F Y', strtotime($tanggal)); ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        h1, h2, h3 { color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .summary-box {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .print-btn {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Cetak / Simpan PDF</button>
    
    <h1>Laporan Presensi Sekolah</h1>
    <h3>Tanggal: <?php echo date('d F Y', strtotime($tanggal)); ?></h3>
    
    <h2>Ringkasan Kehadiran</h2>
    <div>
        <?php
        $total_siswa = $statistik->total_siswa ?? 0;
        $hadir = $statistik->hadir ?? 0;
        $izin = $statistik->izin ?? 0;
        $sakit = $statistik->sakit ?? 0;
        $alpha = $statistik->alpha ?? 0;
        $persentase = $total_siswa > 0 ? round(($hadir / $total_siswa) * 100, 2) : 0;
        ?>
        <div class="summary-box">
            <strong>Total Siswa:</strong> <?php echo $total_siswa; ?>
        </div>
        <div class="summary-box">
            <strong>Hadir:</strong> <?php echo $hadir; ?> (<?php echo $persentase; ?>%)
        </div>
        <div class="summary-box">
            <strong>Izin:</strong> <?php echo $izin; ?>
        </div>
        <div class="summary-box">
            <strong>Sakit:</strong> <?php echo $sakit; ?>
        </div>
        <div class="summary-box">
            <strong>Alpha:</strong> <?php echo $alpha; ?>
        </div>
    </div>
    
    <h2>Detail Presensi</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Email</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Jarak (m)</th>
                <th>Alasan</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($presensi as $p): 
                $waktuTs = (isset($p->waktu) && $p->waktu) ? strtotime($p->waktu) : null;
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($p->nama ?? ''); ?></td>
                <td><?php echo htmlspecialchars($p->email ?? ''); ?></td>
                <td><?php echo $waktuTs ? date('d M Y', $waktuTs) : '-'; ?></td>
                <td><?php echo $waktuTs ? date('H:i', $waktuTs) : '-'; ?></td>
                <td>
                    <?php 
                    if (isset($p->status) && $p->status) {
                        if ($p->status == 'valid') {
                            echo isset($p->jenis) ? ucfirst($p->jenis) : 'Hadir';
                        } else {
                            echo 'Tidak Valid';
                        }
                    } else {
                        echo 'Belum Presensi';
                    }
                    ?>
                </td>
                <td><?php echo isset($p->jarak) ? round($p->jarak, 2) : '-'; ?></td>
                <td><?php echo isset($p->alasan) && $p->alasan ? htmlspecialchars($p->alasan) : '-'; ?></td>
                <td><?php echo isset($p->foto_bukti) && $p->foto_bukti ? 'Ada' : '-'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
        <?php
        exit;
    }

    public function ubahStatusPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'] ?? null;
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $jenis = $_POST['jenis'] ?? 'hadir';
            $alasan = $_POST['alasan'] ?? null;
            $foto_bukti = $_POST['foto_bukti'] ?? null;
            $sesi_id = $_POST['sesi_id'] ?? null;
            
            // Validasi input
            if (!$siswa_id) {
                echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
                return;
            }
            
            // Validasi alasan untuk izin/sakit
            if (($jenis === 'izin' || $jenis === 'sakit') && empty($alasan)) {
                echo json_encode(['success' => false, 'message' => 'Alasan harus diisi untuk status izin/sakit']);
                return;
            }
            
            // Update atau buat presensi
            $result = $this->presensiModel->createOrUpdatePresensiSekolah(
                $siswa_id,
                $tanggal,
                $jenis,
                $alasan,
                $foto_bukti,
                $sesi_id
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Status presensi sekolah berhasil diubah']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mengubah status presensi sekolah']);
            }
        }
    }
}
?>
