<?php
// app/controllers/AdminController.php
// Controller untuk fitur administratif: manajemen user, kelas, lokasi, dan laporan
// Menyediakan endpoint untuk view admin dan beberapa API JSON untuk AJAX
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/PresensiSekolahSesiModel.php';

class AdminController {
    private $userModel;
    private $kelasModel;
    private $locationModel;
    private $presensiModel;
    private $presensiSekolahSesiModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->kelasModel = new KelasModel();
        $this->locationModel = new LocationModel();
        $this->presensiModel = new PresensiModel();
        $this->presensiSekolahSesiModel = new PresensiSekolahSesiModel();
    }

    /**
     * Admin: halaman manajemen sesi presensi sekolah
     * Menampilkan daftar sesi (auto dan manual) dan form CRUD sederhana
     */
    public function presensiSekolah() {
        $this->presensiSekolahSesiModel->closeExpiredSessions();
        $sessions = $this->presensiSekolahSesiModel->getSessions();
        require_once __DIR__ . '/../views/admin/presensi_sekolah.php';
    }

    // API: buat sesi presensi sekolah (manual override)
    public function createPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $waktu_buka = $_POST['waktu_buka'];
            $waktu_tutup = $_POST['waktu_tutup'];
            $note = $_POST['note'] ?? null;
            $created_by = $_SESSION['user_id'] ?? null;
            $id = $this->presensiSekolahSesiModel->createSession($waktu_buka, $waktu_tutup, $created_by, $note);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$id, 'id' => $id]);
            exit;
        }
    }

    // API: extend / perpanjang sesi
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

    // API: close session (admin)
    public function closePresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
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
    }

    // API: status sesi sekolah (dipanggil oleh client siswa)
    public function getPresensiSekolahStatus() {
        // Pastikan expired sessions ditutup dulu
        $this->presensiSekolahSesiModel->closeExpiredSessions();
        $active = $this->presensiSekolahSesiModel->getActiveSession();
        header('Content-Type: application/json');
        if ($active) {
            // jika ada user yang sedang terautentikasi, cek apakah sudah presensi pada sesi ini
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
    
    public function dashboard() {
        // Hitung statistik singkat untuk ditampilkan di dashboard admin
        $totalSiswa = count($this->userModel->getUsersByRole('siswa'));
        $totalGuru = count($this->userModel->getUsersByRole('guru'));
        $totalKelas = count($this->kelasModel->getAllKelas());
        
    require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function users() {
        // Tampilkan halaman manajemen user
        $users = $this->userModel->getAllUsers();
    require_once __DIR__ . '/../views/admin/users.php';
    }
    
    public function kelas() {
        // Halaman manajemen kelas (tampilkan list kelas, guru, dll.)
        $kelas = $this->kelasModel->getAllKelas();
        $guru = $this->userModel->getUsersByRole('guru');
        $totalSiswa = count($this->userModel->getUsersByRole('siswa'));
        $totalGuru = count($this->userModel->getUsersByRole('guru'));
        // expose the model to the view so it can call helper methods
        $kelasModel = $this->kelasModel;
    require_once __DIR__ . '/../views/admin/kelas.php';
    }

    // Create Kelas (dari form tambah)
    public function createKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nama_kelas' => $_POST['nama_kelas'],
                'tahun_ajaran' => $_POST['tahun_ajaran'],
                'wali_kelas' => $_POST['wali_kelas'] ?? null,
                'jadwal' => $_POST['jadwal'] ?? null
            ];

            if($this->kelasModel->createKelas($data)) {
                // set pesan sukses untuk ditampilkan setelah redirect
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
                'wali_kelas' => $_POST['wali_kelas'] ?? null,
                'jadwal' => $_POST['jadwal'] ?? null
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
    
    public function lokasi() {
        // Halaman pengaturan lokasi sekolah
        $lokasi = $this->locationModel->getLokasiSekolah();
    require_once __DIR__ . '/../views/admin/lokasi.php';
    }
    
    public function laporan() {
        // Halaman laporan admin - presensi sekolah dan kelas
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
            } else {
                $presensi = [];
                $statistik = null;
                $total_records = 0;
                $total_pages = 0;
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

    public function exportExcel() {
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $filter_status = $_GET['status'] ?? null;
        $tipe = $_GET['tipe'] ?? 'sekolah';
        $kelas_id = $_GET['kelas_id'] ?? null;
        
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
            echo '<td>' . (isset($p->alasan) && $p->alasan ? htmlspecialchars($p->alasan) : '-') . '</td>';
            echo '<td>' . (isset($p->foto_bukti) && $p->foto_bukti ? 'Ada' : '-') . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</body></html>';
        exit;
    }

    public function exportPDF() {
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $filter_status = $_GET['status'] ?? null;
        $tipe = $_GET['tipe'] ?? 'sekolah';
        $kelas_id = $_GET['kelas_id'] ?? null;
        
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