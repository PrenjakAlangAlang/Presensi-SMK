<?php
// app/controllers/AdminKesiswaanController.php
// Peran admin kesiswaan: kelola buku induk seluruh siswa dan kelola sesi presensi sekolah
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';
require_once __DIR__ . '/../models/PresensiSekolahSesiModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../models/LaporanModel.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';

class AdminKesiswaanController {
    private $userModel;
    private $bukuIndukModel;
    private $presensiSekolahSesiModel;
    private $presensiModel;
    private $kelasModel;
    private $locationModel;
    private $laporanModel;
    private $presensiSesiModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->bukuIndukModel = new BukuIndukModel();
        $this->presensiSekolahSesiModel = new PresensiSekolahSesiModel();
        $this->presensiModel = new PresensiModel();
        $this->kelasModel = new KelasModel();
        $this->locationModel = new LocationModel();
        $this->laporanModel = new LaporanModel();
        $this->presensiSesiModel = new PresensiSesiModel();
    }

    public function dashboard() {
        // Auto-create sesi presensi jika hari kerja dan belum ada sesi hari ini
        $this->presensiSekolahSesiModel->autoCreateDailySesi();
        
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
        
        // Attach documents to each record
        foreach($records as $record) {
            $record->dokumen = $this->bukuIndukModel->getDokumen($record->id);
        }
        
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
            'nama_ayah' => isset($_POST['nama_ayah']) ? trim($_POST['nama_ayah']) : null,
            'nama_ibu' => isset($_POST['nama_ibu']) ? trim($_POST['nama_ibu']) : null,
            'nama_wali' => isset($_POST['nama_wali']) ? trim($_POST['nama_wali']) : null,
            'no_telp_ortu' => isset($_POST['no_telp_ortu']) ? trim($_POST['no_telp_ortu']) : null,
            'email_ortu' => isset($_POST['email_ortu']) ? trim($_POST['email_ortu']) : null,
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
            // Get the buku induk record to get its ID
            $record = $this->bukuIndukModel->getByUserId($data['user_id']);
            
            // Handle multiple PDF uploads
            if(isset($_FILES['dokumen_files']) && !empty($_FILES['dokumen_files']['name'][0])) {
                $fileCount = count($_FILES['dokumen_files']['name']);
                for($i = 0; $i < $fileCount; $i++) {
                    if($_FILES['dokumen_files']['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['dokumen_files']['name'][$i],
                            'type' => $_FILES['dokumen_files']['type'][$i],
                            'tmp_name' => $_FILES['dokumen_files']['tmp_name'][$i],
                            'error' => $_FILES['dokumen_files']['error'][$i],
                            'size' => $_FILES['dokumen_files']['size'][$i]
                        ];
                        
                        $upload = $this->handlePdfUpload($file);
                        if($upload['success']) {
                            $keterangan = isset($_POST['keterangan'][$i]) ? trim($_POST['keterangan'][$i]) : null;
                            $this->bukuIndukModel->addDokumen([
                                'buku_induk_id' => $record->id,
                                'nama_file' => $file['name'],
                                'path_file' => $upload['path'],
                                'keterangan' => $keterangan
                            ]);
                        }
                    }
                }
            }
            
            $_SESSION['success'] = 'Buku induk berhasil disimpan.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan buku induk.';
        }

        header('Location: ' . BASE_URL . '/public/index.php?action=admin_kesiswaan_buku_induk');
        exit();
    }

    public function deleteDokumen() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $id = $_POST['dokumen_id'] ?? null;
        if(!$id) {
            $_SESSION['error'] = 'ID dokumen tidak valid.';
        } else {
            $dokumen = $this->bukuIndukModel->getDokumenById($id);
            if($dokumen) {
                // Delete file from server
                $filePath = str_replace(BASE_URL . '/public/', __DIR__ . '/../../public/', $dokumen->path_file);
                if(file_exists($filePath)) {
                    unlink($filePath);
                }
                
                // Delete from database
                if($this->bukuIndukModel->deleteDokumen($id)) {
                    $_SESSION['success'] = 'Dokumen berhasil dihapus.';
                } else {
                    $_SESSION['error'] = 'Gagal menghapus dokumen.';
                }
            } else {
                $_SESSION['error'] = 'Dokumen tidak ditemukan.';
            }
        }
        
        header('Location: ' . BASE_URL . '/public/index.php?action=admin_kesiswaan_buku_induk');
        exit();
    }

    // Presensi sekolah (sama seperti admin)
    public function presensiSekolah() {
        // Auto-create sesi presensi jika hari kerja dan belum ada sesi hari ini
        $this->presensiSekolahSesiModel->autoCreateDailySesi();
        $this->presensiSekolahSesiModel->closeExpiredSessions();
        $sessions = $this->presensiSekolahSesiModel->getSessions();
        require_once __DIR__ . '/../views/admin_kesiswaan/presensi_sekolah.php';
    }

    public function createPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $waktu_buka = $_POST['waktu_buka'] ?? null;
            $waktu_tutup = $_POST['waktu_tutup'] ?? null;
            $note = $_POST['note'] ?? null;
            $created_by = $_SESSION['user_id'] ?? null;
            
            if (!$waktu_buka || !$waktu_tutup) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Waktu buka dan tutup harus diisi']);
                exit;
            }
            
            // Convert datetime-local format to MySQL datetime format if needed
            $waktu_buka_formatted = str_replace('T', ' ', $waktu_buka);
            $waktu_tutup_formatted = str_replace('T', ' ', $waktu_tutup);
            if (strlen($waktu_buka_formatted) == 16) $waktu_buka_formatted .= ':00';
            if (strlen($waktu_tutup_formatted) == 16) $waktu_tutup_formatted .= ':00';
            
            $id = $this->presensiSekolahSesiModel->createSession($waktu_buka_formatted, $waktu_tutup_formatted, $created_by, $note);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$id, 'id' => $id]);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    public function extendPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $new_waktu_tutup = $_POST['waktu_tutup'] ?? null;
            
            if (!$id || !$new_waktu_tutup) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID atau waktu tutup tidak valid']);
                exit;
            }
            
            // Convert datetime-local format to MySQL datetime format if needed
            $waktu_tutup_formatted = str_replace('T', ' ', $new_waktu_tutup);
            if (strlen($waktu_tutup_formatted) == 16) {
                $waktu_tutup_formatted .= ':00';
            }
            
            $ok = $this->presensiSekolahSesiModel->extendSession($id, $waktu_tutup_formatted);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    public function closePresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            
            if (!$id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
                exit;
            }
            
            // Mark absent students as alpha before closing
            $alphaCount = $this->presensiModel->markAbsentStudentsAsAlphaSekolah($id);
            
            // Close the session
            $ok = $this->presensiSekolahSesiModel->closeSession($id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => (bool)$ok,
                'alpha_count' => $alphaCount,
                'message' => $alphaCount > 0 ? "Sesi ditutup. $alphaCount siswa ditandai alpha." : 'Sesi ditutup.'
            ]);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    public function getPresensiSekolahStatus() {
        // Auto-create sesi presensi jika hari kerja dan belum ada sesi hari ini
        $this->presensiSekolahSesiModel->autoCreateDailySesi();
        $this->presensiSekolahSesiModel->closeExpiredSessions();
        $active = $this->presensiSekolahSesiModel->getActiveSession();
        header('Content-Type: application/json');
        if ($active) {
            $already = false;
            if (isset($_SESSION['user_id'])) {
                $uid = $_SESSION['user_id'];
                $presensi = $this->presensiModel->getPresensiInSchoolSession($uid, $active->id);
                // Siswa dianggap sudah presensi HANYA jika jenisnya bukan alpha
                // Jika alpha, siswa masih bisa presensi ulang (ketika sesi diperpanjang)
                $already = $presensi && $presensi->jenis !== 'alpha';
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
        // Halaman laporan admin kesiswaan - presensi sekolah dan kelas
        // Ambil tipe laporan (sekolah atau kelas)
        $tipe_laporan = $_GET['tipe'] ?? 'sekolah';
        
        // Ambil list kelas untuk dropdown
        $kelas_list = $this->kelasModel->getAllKelas();
        
        // Ambil parameter filter periode
        $periode = $_GET['periode'] ?? 'bulanan';
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $minggu = $_GET['minggu'] ?? date('W');
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $kelas_id = $_GET['kelas_id'] ?? null;
        $filter_status = $_GET['status'] ?? null;
        
        // Calculate date range based on periode
        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
        } elseif ($periode === 'mingguan') {
            $startDate = date('Y-m-d', strtotime($tahun . 'W' . str_pad($minggu, 2, '0', STR_PAD_LEFT)));
            $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
        } else {
            // bulanan - first and last day of month
            $startDate = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
        }
        
        // Pagination settings
        $limit = 50; // Items per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $offset = ($page - 1) * $limit;
        
        if ($tipe_laporan === 'kelas') {
            // Laporan presensi kelas
            if ($kelas_id) {
                // Get all presensi kelas for the period
                $db = new Database();
                $db->query('SELECT pk.*, u.nama, u.email, k.nama_kelas
                            FROM presensi_kelas pk
                            JOIN users u ON pk.user_id = u.id
                            JOIN kelas k ON pk.kelas_id = k.id
                            WHERE pk.kelas_id = :kelas_id 
                            AND DATE(pk.waktu) BETWEEN :start_date AND :end_date
                            ORDER BY pk.waktu DESC');
                $db->bind(':kelas_id', $kelas_id);
                $db->bind(':start_date', $startDate);
                $db->bind(':end_date', $endDate);
                $all_presensi = $db->resultSet();
                
                // Apply status filter if provided
                if ($filter_status) {
                    $all_presensi = array_filter($all_presensi, function($p) use ($filter_status) {
                        return $p->jenis == $filter_status;
                    });
                }
                
                $total_records = count($all_presensi);
                $total_pages = ceil($total_records / $limit);
                
                // Paginate
                $presensi = array_slice($all_presensi, $offset, $limit);
                
                // Get statistics from filtered data
                $statistik = new stdClass();
                $statistik->total_siswa = count($all_presensi);
                $statistik->hadir = 0;
                $statistik->izin = 0;
                $statistik->sakit = 0;
                $statistik->alpha = 0;
                
                foreach ($all_presensi as $p) {
                    if (isset($p->jenis)) {
                        if ($p->jenis == 'hadir') $statistik->hadir++;
                        elseif ($p->jenis == 'izin') $statistik->izin++;
                        elseif ($p->jenis == 'sakit') $statistik->sakit++;
                        elseif ($p->jenis == 'alpha') $statistik->alpha++;
                    }
                }
                
                // Get sessions for this class
                $sessions = $this->presensiSesiModel->getSessionsByKelas($kelas_id);
                
                // Get laporan kemajuan for this class with date range filter
                $laporan_kemajuan = $this->laporanModel->getLaporanByKelasWithDateRange($kelas_id, $startDate, $endDate);
            } else {
                $presensi = [];
                $statistik = null;
                $total_records = 0;
                $total_pages = 0;
                $sessions = [];
                $laporan_kemajuan = [];
            }
        } else {
            // Laporan presensi sekolah (default)
            // Get all presensi sekolah for the period
            $db = new Database();
            $db->query('SELECT ps.*, u.nama, u.email 
                        FROM presensi_sekolah ps 
                        JOIN users u ON ps.user_id = u.id 
                        WHERE DATE(ps.waktu) BETWEEN :start_date AND :end_date
                        ORDER BY ps.waktu DESC');
            $db->bind(':start_date', $startDate);
            $db->bind(':end_date', $endDate);
            $all_presensi_sekolah = $db->resultSet();
            
            // Apply status filter if provided
            if ($filter_status) {
                $all_presensi_sekolah = array_filter($all_presensi_sekolah, function($p) use ($filter_status) {
                    return $p->jenis == $filter_status;
                });
            }
            
            // Calculate statistics
            $statistik = new stdClass();
            $statistik->total_siswa = count($all_presensi_sekolah);
            $statistik->hadir = 0;
            $statistik->izin = 0;
            $statistik->sakit = 0;
            $statistik->alpha = 0;
            
            foreach ($all_presensi_sekolah as $p) {
                if (isset($p->jenis)) {
                    if ($p->jenis == 'hadir') $statistik->hadir++;
                    elseif ($p->jenis == 'izin') $statistik->izin++;
                    elseif ($p->jenis == 'sakit') $statistik->sakit++;
                    elseif ($p->jenis == 'alpha') $statistik->alpha++;
                }
            }
            
            $total_records = count($all_presensi_sekolah);
            $total_pages = ceil($total_records / $limit);
            
            // Paginate
            $presensi = array_slice($all_presensi_sekolah, $offset, $limit);
        }
        
        require_once __DIR__ . '/../views/admin_kesiswaan/laporan.php';
    }

    public function exportExcel() {
        $periode = $_GET['periode'] ?? 'bulanan';
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $minggu = $_GET['minggu'] ?? date('W');
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $filter_status = $_GET['status'] ?? null;
        $tipe = $_GET['tipe'] ?? 'sekolah';
        $kelas_id = $_GET['kelas_id'] ?? null;
        
        // Calculate date range based on periode
        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
        } elseif ($periode === 'mingguan') {
            $startDate = date('Y-m-d', strtotime($tahun . 'W' . str_pad($minggu, 2, '0', STR_PAD_LEFT)));
            $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
        } else {
            $startDate = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
        }
        
        // Get all data (no pagination for export)
        if ($tipe === 'kelas' && $kelas_id) {
            $db = new Database();
            $db->query('SELECT pk.*, u.nama, u.email, k.nama_kelas
                        FROM presensi_kelas pk
                        JOIN users u ON pk.user_id = u.id
                        JOIN kelas k ON pk.kelas_id = k.id
                        WHERE pk.kelas_id = :kelas_id 
                        AND MONTH(pk.waktu) = :bulan 
                        AND YEAR(pk.waktu) = :tahun
                        ORDER BY pk.waktu DESC');
            $db->bind(':kelas_id', $kelas_id);
            $db->bind(':bulan', $bulan);
            $db->bind(':tahun', $tahun);
            $presensi = $db->resultSet();
            
            $kelas_info = $this->kelasModel->getKelasById($kelas_id);
            $report_title = 'Laporan Presensi Kelas ' . ($kelas_info ? $kelas_info->nama_kelas : $kelas_id);
            
            // Calculate statistics from actual data
            $statistik = new stdClass();
            $statistik->total_siswa = count($presensi);
            $statistik->hadir = 0;
            $statistik->izin = 0;
            $statistik->sakit = 0;
            $statistik->alpha = 0;
            foreach ($presensi as $p) {
                if (isset($p->jenis)) {
                    if ($p->jenis == 'hadir') $statistik->hadir++;
                    elseif ($p->jenis == 'izin') $statistik->izin++;
                    elseif ($p->jenis == 'sakit') $statistik->sakit++;
                    elseif ($p->jenis == 'alpha') $statistik->alpha++;
                }
            }
        } else {
            $db = new Database();
            $db->query('SELECT ps.*, u.nama, u.email 
                        FROM presensi_sekolah ps 
                        JOIN users u ON ps.user_id = u.id 
                        WHERE MONTH(ps.waktu) = :bulan AND YEAR(ps.waktu) = :tahun
                        ORDER BY ps.waktu DESC');
            $db->bind(':bulan', $bulan);
            $db->bind(':tahun', $tahun);
            $presensi = $db->resultSet();
            
            // Apply status filter if provided
            if ($filter_status) {
                $presensi = array_filter($presensi, function($p) use ($filter_status) {
                    return $p->jenis == $filter_status;
                });
            }
            
            $report_title = 'Laporan Presensi Sekolah';
            $statistik = $this->presensiModel->getStatistikPresensiSekolah(null, $bulan, $tahun, null);
        }
        
        // Set headers for Excel download
        $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulan_name = $bulan_names[intval($bulan) - 1];
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Presensi_' . $bulan_name . '_' . $tahun . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output HTML table format for Excel
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
        echo '<body>';
        echo '<h2>' . $report_title . '</h2>';
        echo '<p>Periode: ' . $bulan_name . ' ' . $tahun . '</p>';
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
        
        // Laporan Kemajuan (only for class reports)
        if ($tipe === 'kelas' && $kelas_id && !empty($laporan_kemajuan)) {
            echo '<h3>Laporan Kemajuan</h3>';
            echo '<table border="1" cellpadding="5">';
            echo '<tr><th>No</th><th>Tanggal</th><th>Guru</th><th>Catatan</th></tr>';
            $no = 1;
            foreach($laporan_kemajuan as $l) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($l->tanggal)) . '</td>';
                echo '<td>' . htmlspecialchars($l->guru_nama ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($l->catatan) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<br><br>';
        }
        
        // Detail Data
        echo '<h3>Detail Presensi</h3>';
        echo '<table border="1" cellpadding="5">';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Nama Siswa</th>';
        echo '<th>Email</th>';
        if ($tipe === 'kelas') {
            echo '<th>Kelas</th>';
        }
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
            if ($tipe === 'kelas') {
                echo '<td>' . htmlspecialchars($p->nama_kelas ?? '') . '</td>';
            }
            
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
        $periode = $_GET['periode'] ?? 'bulanan';
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $minggu = $_GET['minggu'] ?? date('W');
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $filter_status = $_GET['status'] ?? null;
        $tipe = $_GET['tipe'] ?? 'sekolah';
        $kelas_id = $_GET['kelas_id'] ?? null;
        
        // Calculate date range based on periode
        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
        } elseif ($periode === 'mingguan') {
            $startDate = date('Y-m-d', strtotime($tahun . 'W' . str_pad($minggu, 2, '0', STR_PAD_LEFT)));
            $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));
        } else {
            $startDate = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
        }
        
        // Get all data (no pagination for export)
        if ($tipe === 'kelas' && $kelas_id) {
            $db = new Database();
            $db->query('SELECT pk.*, u.nama, u.email, k.nama_kelas
                        FROM presensi_kelas pk
                        JOIN users u ON pk.user_id = u.id
                        JOIN kelas k ON pk.kelas_id = k.id
                        WHERE pk.kelas_id = :kelas_id 
                        AND DATE(pk.waktu) BETWEEN :start_date AND :end_date' . 
                        ($filter_status ? ' AND pk.jenis = :status' : '') . '
                        ORDER BY pk.waktu DESC');
            $db->bind(':kelas_id', $kelas_id);
            $db->bind(':start_date', $startDate);
            $db->bind(':end_date', $endDate);
            if ($filter_status) {
                $db->bind(':status', $filter_status);
            }
            $presensi = $db->resultSet();
            
            $kelas_info = $this->kelasModel->getKelasById($kelas_id);
            $report_title = 'Laporan Presensi Kelas ' . ($kelas_info ? $kelas_info->nama_kelas : $kelas_id);
            
            // Get laporan kemajuan for the selected date range
            $laporan_kemajuan = $this->laporanModel->getLaporanByKelasWithDateRange($kelas_id, $startDate, $endDate);
            
            // Calculate statistics from actual data
            $statistik = new stdClass();
            $statistik->total_siswa = count($presensi);
            $statistik->hadir = 0;
            $statistik->izin = 0;
            $statistik->sakit = 0;
            $statistik->alpha = 0;
            foreach ($presensi as $p) {
                if (isset($p->jenis)) {
                    if ($p->jenis == 'hadir') $statistik->hadir++;
                    elseif ($p->jenis == 'izin') $statistik->izin++;
                    elseif ($p->jenis == 'sakit') $statistik->sakit++;
                    elseif ($p->jenis == 'alpha') $statistik->alpha++;
                }
            }
        } else {
            $db = new Database();
            $db->query('SELECT ps.*, u.nama, u.email 
                        FROM presensi_sekolah ps 
                        JOIN users u ON ps.user_id = u.id 
                        WHERE DATE(ps.waktu) BETWEEN :start_date AND :end_date' . 
                        ($filter_status ? ' AND ps.jenis = :status' : '') . '
                        ORDER BY ps.waktu DESC');
            $db->bind(':start_date', $startDate);
            $db->bind(':end_date', $endDate);
            if ($filter_status) {
                $db->bind(':status', $filter_status);
            }
            $presensi = $db->resultSet();
            
            $report_title = 'Laporan Presensi Sekolah';
            
            // Calculate statistics from filtered data
            $statistik = new stdClass();
            $statistik->total_siswa = count($presensi);
            $statistik->hadir = 0;
            $statistik->izin = 0;
            $statistik->sakit = 0;
            $statistik->alpha = 0;
            foreach ($presensi as $p) {
                if (isset($p->jenis)) {
                    if ($p->jenis == 'hadir') $statistik->hadir++;
                    elseif ($p->jenis == 'izin') $statistik->izin++;
                    elseif ($p->jenis == 'sakit') $statistik->sakit++;
                    elseif ($p->jenis == 'alpha') $statistik->alpha++;
                }
            }
        }
        
        $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulan_name = $bulan_names[intval($bulan) - 1];
        
        // For PDF, we'll create a print-friendly HTML page
        ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $report_title; ?> - <?php echo $bulan_name . ' ' . $tahun; ?></title>
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
    
    <h1><?php echo $report_title; ?></h1>
    <h3>Periode: <?php echo $bulan_name . ' ' . $tahun; ?></h3>
    
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
    
    <?php if ($tipe === 'kelas' && $kelas_id && !empty($laporan_kemajuan)): ?>
    <h2>Laporan Kemajuan</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Guru</th>
                <th style="width: 60%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no_lk = 1; foreach($laporan_kemajuan as $l): ?>
            <tr>
                <td><?php echo $no_lk++; ?></td>
                <td><?php echo date('d/m/Y', strtotime($l->tanggal)); ?></td>
                <td><?php echo htmlspecialchars($l->guru_nama ?? '-'); ?></td>
                <td style="white-space: pre-line;"><?php echo htmlspecialchars($l->catatan); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    
    <h2>Detail Presensi</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Email</th>
                <?php if ($tipe === 'kelas'): ?>
                <th>Kelas</th>
                <?php endif; ?>
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
                <?php if ($tipe === 'kelas'): ?>
                <td><?php echo htmlspecialchars($p->nama_kelas ?? ''); ?></td>
                <?php endif; ?>
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
            $presensi_id = $_POST['presensi_id'] ?? null;
            $user_id = $_POST['user_id'] ?? null;
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $jenis = $_POST['jenis'] ?? 'hadir';
            $alasan = $_POST['alasan'] ?? null;
            $foto_bukti = $_POST['foto_bukti'] ?? null;
            $sesi_id = $_POST['sesi_id'] ?? null;
            
            // Validasi input
            if (!$presensi_id || !$user_id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID presensi atau user tidak valid']);
                exit;
            }
            
            // Validasi alasan untuk izin/sakit
            if (($jenis === 'izin' || $jenis === 'sakit') && empty($alasan)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Alasan harus diisi untuk status izin/sakit']);
                exit;
            }
            
            // Update presensi yang sudah ada berdasarkan ID
            $db = new Database();
            $db->query('UPDATE presensi_sekolah SET 
                        jenis = :jenis,
                        alasan = :alasan,
                        foto_bukti = :foto_bukti
                        WHERE id = :id AND user_id = :user_id');
            $db->bind(':jenis', $jenis);
            $db->bind(':alasan', $alasan);
            $db->bind(':foto_bukti', $foto_bukti);
            $db->bind(':id', $presensi_id);
            $db->bind(':user_id', $user_id);
            
            if ($db->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status presensi sekolah berhasil diubah']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal mengubah status presensi']);
            }
            exit;
        }
    }

    public function ubahStatusPresensiKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $presensi_id = $_POST['presensi_id'] ?? null;
            $user_id = $_POST['user_id'] ?? null;
            $kelas_id = $_POST['kelas_id'] ?? null;
            $jenis = $_POST['jenis'] ?? 'hadir';
            $alasan = $_POST['alasan'] ?? null;
            $foto_bukti = $_POST['foto_bukti'] ?? null;
            $sesi_id = $_POST['sesi_id'] ?? null;
            
            // Validasi input
            if (!$presensi_id || !$user_id || !$kelas_id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID presensi, user, atau kelas tidak valid']);
                exit;
            }
            
            // Validasi alasan untuk izin/sakit
            if (($jenis === 'izin' || $jenis === 'sakit') && empty($alasan)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Alasan harus diisi untuk status izin/sakit']);
                exit;
            }
            
            // Update presensi kelas yang sudah ada berdasarkan ID
            $db = new Database();
            $db->query('UPDATE presensi_kelas SET 
                        jenis = :jenis,
                        alasan = :alasan,
                        foto_bukti = :foto_bukti
                        WHERE id = :id AND user_id = :user_id AND kelas_id = :kelas_id');
            $db->bind(':jenis', $jenis);
            $db->bind(':alasan', $alasan);
            $db->bind(':foto_bukti', $foto_bukti);
            $db->bind(':id', $presensi_id);
            $db->bind(':user_id', $user_id);
            $db->bind(':kelas_id', $kelas_id);
            
            if ($db->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status presensi kelas berhasil diubah']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal mengubah status presensi']);
            }
            exit;
        }
    }

    // Hapus satu sesi presensi
    public function deletePresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $success = $this->presensiSekolahSesiModel->deleteSesi($id);
                header('Content-Type: application/json');
                echo json_encode(['success' => $success]);
                exit;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit;
    }

    // Hapus multiple sesi presensi
    public function deleteMultiplePresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ids = $_POST['ids'] ?? [];
            if (!empty($ids) && is_array($ids)) {
                $success = $this->presensiSekolahSesiModel->deleteMultipleSesi($ids);
                header('Content-Type: application/json');
                echo json_encode(['success' => $success, 'count' => count($ids)]);
                exit;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit;
    }
}
?>
