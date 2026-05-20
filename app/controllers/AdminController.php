<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/MataPelajaranModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/PresensiSekolahSesiModel.php';
require_once __DIR__ . '/../models/LaporanModel.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';

class AdminController {
    private $userModel;
    private $mataPelajaranModel;
    private $locationModel;
    private $presensiModel;
    private $presensiSekolahSesiModel;
    private $laporanModel;
    private $presensiSesiModel;
    private $bukuIndukModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->mataPelajaranModel = new MataPelajaranModel();
        $this->locationModel = new LocationModel();
        $this->presensiModel = new PresensiModel();
        $this->presensiSekolahSesiModel = new PresensiSekolahSesiModel();
        $this->laporanModel = new LaporanModel();
        $this->presensiSesiModel = new PresensiSesiModel();
        $this->bukuIndukModel = new BukuIndukModel();
    }

   
    public function presensiSekolah() {
        $this->presensiModel->closeExpiredSekolahSessions();
        $sessions = $this->presensiSekolahSesiModel->getSessions();
        require_once __DIR__ . '/../views/admin/presensi_sekolah.php';
    }

    
    public function createPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $waktu_buka = $_POST['waktu_buka'] ?? null;
            $waktu_tutup = $_POST['waktu_tutup'] ?? null;
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

            if (strtotime($waktu_tutup_formatted) <= strtotime($waktu_buka_formatted)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Waktu tutup harus setelah waktu buka']);
                exit;
            }
            
            $repeatEnabled = isset($_POST['repeat_enabled']) && $_POST['repeat_enabled'] === '1';
            if ($repeatEnabled) {
                $repeatDays = $_POST['repeat_days'] ?? [];
                $repeatEveryWeeks = $_POST['repeat_every_weeks'] ?? 1;
                $repeatUntil = $_POST['repeat_until'] ?? null;

                if (empty($repeatDays) || !$repeatUntil) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Pilih hari pengulangan dan tanggal selesai']);
                    exit;
                }

                $createdCount = $this->presensiSekolahSesiModel->createMultipleSessions($waktu_buka_formatted, $waktu_tutup_formatted, $repeatDays, $repeatEveryWeeks, $repeatUntil, $created_by);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $createdCount !== false && $createdCount > 0,
                    'count' => (int) $createdCount,
                    'message' => $createdCount ? "$createdCount sesi berhasil dibuat." : 'Tidak ada sesi yang dibuat. Periksa jadwal pengulangan.'
                ]);
                exit;
            }

            $id = $this->presensiSekolahSesiModel->createSession($waktu_buka_formatted, $waktu_tutup_formatted, $created_by);
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
            
            // Mark absent students as alpha before closing (notifications sent automatically)
            $alphaCount = $this->presensiModel->markAbsentStudentsAsAlphaSekolah($id);
            
            // Close the session
            $ok = $this->presensiSekolahSesiModel->closeSession($id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => (bool)$ok,
                'alpha_count' => $alphaCount,
                'message' => $alphaCount > 0 ? "Sesi ditutup. $alphaCount siswa ditandai alpha. Notifikasi sedang dikirim." : 'Sesi ditutup.'
            ]);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    
    public function getPresensiSekolahStatus() {
        $this->presensiModel->closeExpiredSekolahSessions();
        $active = $this->presensiSekolahSesiModel->getActiveSession();
        header('Content-Type: application/json');
        if ($active) {
            // jika ada user yang sedang terautentikasi, cek apakah sudah presensi pada sesi ini
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
    
    public function dashboard() {
        // Hitung statistik singkat untuk ditampilkan di dashboard admin
        $totalSiswa = count($this->userModel->getUsersByRole('siswa'));
        $totalGuru = count($this->userModel->getUsersByRole('guru'));
        $totalMataPelajaran = count($this->mataPelajaranModel->getAllMataPelajaran());
        $sessions = $this->presensiSekolahSesiModel->getSessions();
        
        // Pagination variables
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;
        $total_records = count($sessions);
        $total_pages = ceil($total_records / $limit);
        
    require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function users() {
        // Tampilkan halaman manajemen user
        $users = $this->userModel->getAllUsers();
    require_once __DIR__ . '/../views/admin/users.php';
    }
    
    public function kelas() {
        header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran');
        exit;
    }
    
    public function jadwalMataPelajaran() {
        $kelasList = $this->mataPelajaranModel->getAllKelasJadwal();
        $selectedKelasId = $_GET['kelas_id'] ?? null;
        $selectedKelas = $selectedKelasId ? $this->mataPelajaranModel->getKelasJadwalById($selectedKelasId) : null;
        $mataPelajaran = $selectedKelas
            ? $this->mataPelajaranModel->getJadwalByKelas($selectedKelas->id)
            : [];
        $guru = $this->userModel->getUsersByRole('guru');
        $mataPelajaranModel = $this->mataPelajaranModel;
        require_once __DIR__ . '/../views/admin/jadwal_mata_pelajaran.php';
    }

    public function mataPelajaran() {
        $this->jadwalMataPelajaran();
    }

  
    public function createKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaKelas = trim($_POST['nama_kelas'] ?? '');
            $tahunAjaran = trim($_POST['tahun_ajaran'] ?? '');
            $semester = trim($_POST['semester'] ?? '');

            if ($namaKelas && $this->mataPelajaranModel->createKelasJadwal($namaKelas, $tahunAjaran ?: null, $semester ?: null)) {
                $_SESSION['success'] = 'Kelas berhasil dibuat!';
            } else {
                $_SESSION['error'] = 'Gagal membuat kelas!';
            }
        }
        header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran');
        exit;
    }

    public function updateKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $namaKelas = trim($_POST['nama_kelas'] ?? '');
            $tahunAjaran = trim($_POST['tahun_ajaran'] ?? '');
            $semester = trim($_POST['semester'] ?? '');

            if ($id && $namaKelas && $this->mataPelajaranModel->updateKelasJadwal($id, $namaKelas, $tahunAjaran ?: null, $semester ?: null)) {
                $_SESSION['success'] = 'Kelas berhasil diperbarui!';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui kelas!';
            }
        }
        header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran');
        exit;
    }

    public function deleteKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id && $this->mataPelajaranModel->deleteKelasJadwal($id)) {
                $_SESSION['success'] = 'Kelas berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus kelas!';
            }
        }
        header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran');
        exit;
    }

    public function toggleKelasStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? '';

            if ($id && $this->mataPelajaranModel->updateKelasStatus($id, $status)) {
                $_SESSION['success'] = $status === 'archived'
                    ? 'Kelas berhasil dinonaktifkan dan diarsipkan.'
                    : 'Kelas berhasil diaktifkan kembali.';
            } else {
                $_SESSION['error'] = 'Gagal mengubah status kelas.';
            }
        }
        header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran');
        exit;
    }
    
    
    public function createMataPelajaran() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST['kelas_id'])) {
                $kelas = $this->mataPelajaranModel->getKelasJadwalById($_POST['kelas_id']);
                $_POST['nama_kelas'] = $kelas->nama_kelas ?? ($_POST['nama_kelas'] ?? '');
                if ($this->isKelasArchived($kelas)) {
                    $this->redirectJadwalKelas($_POST['kelas_id'], 'Kelas arsip tidak bisa ditambah jadwal baru.');
                }
            }

            $baseData = [
                'kelas_jadwal_id' => $_POST['kelas_id'] ?? null,
                'nama_kelas' => $_POST['nama_kelas'],
                'nama_mata_pelajaran' => $_POST['nama_mata_pelajaran'],
                'guru_pengampu' => $_POST['guru_pengampu'] ?? null
            ];

            $hariList = is_array($_POST['hari'] ?? null) ? $_POST['hari'] : [$_POST['hari'] ?? null];
            $jamMulaiList = is_array($_POST['jam_mulai'] ?? null) ? $_POST['jam_mulai'] : [$_POST['jam_mulai'] ?? null];
            $jamSelesaiList = is_array($_POST['jam_selesai'] ?? null) ? $_POST['jam_selesai'] : [$_POST['jam_selesai'] ?? null];
            $ruangList = is_array($_POST['ruang'] ?? null) ? $_POST['ruang'] : [$_POST['ruang'] ?? null];

            $createdCount = 0;
            foreach ($hariList as $index => $hari) {
                $jamMulai = $jamMulaiList[$index] ?? null;
                $jamSelesai = $jamSelesaiList[$index] ?? null;

                if (!$hari || !$jamMulai || !$jamSelesai) {
                    continue;
                }

                $data = $baseData + [
                    'hari' => $hari,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'ruang' => $ruangList[$index] ?? null
                ];

                if ($this->mataPelajaranModel->createMataPelajaran($data)) {
                    $createdCount++;
                }
            }

            if($createdCount > 0) {
                $_SESSION['success'] = $createdCount . ' jadwal mata pelajaran berhasil dibuat!';
            } else {
                $_SESSION['error'] = 'Gagal membuat jadwal mata pelajaran!';
            }

            $redirectKelas = $_POST['kelas_id'] ?? null;
            header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran' . ($redirectKelas ? '&kelas_id=' . urlencode($redirectKelas) : ''));
            exit;
        }
    }

    public function updateMataPelajaran() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST['kelas_id'])) {
                $kelas = $this->mataPelajaranModel->getKelasJadwalById($_POST['kelas_id']);
                $_POST['nama_kelas'] = $kelas->nama_kelas ?? ($_POST['nama_kelas'] ?? '');
                if ($this->isKelasArchived($kelas)) {
                    $this->redirectJadwalKelas($_POST['kelas_id'], 'Kelas arsip tidak bisa diubah jadwalnya.');
                }
            }

            $baseData = [
                'kelas_jadwal_id' => $_POST['kelas_id'] ?? null,
                'nama_kelas' => $_POST['nama_kelas'],
                'nama_mata_pelajaran' => $_POST['nama_mata_pelajaran'],
                'guru_pengampu' => $_POST['guru_pengampu'] ?? null
            ];

            $hariList = is_array($_POST['hari'] ?? null) ? $_POST['hari'] : [$_POST['hari'] ?? null];
            $jamMulaiList = is_array($_POST['jam_mulai'] ?? null) ? $_POST['jam_mulai'] : [$_POST['jam_mulai'] ?? null];
            $jamSelesaiList = is_array($_POST['jam_selesai'] ?? null) ? $_POST['jam_selesai'] : [$_POST['jam_selesai'] ?? null];
            $ruangList = is_array($_POST['ruang'] ?? null) ? $_POST['ruang'] : [$_POST['ruang'] ?? null];
            $idList = is_array($_POST['jadwal_id'] ?? null) ? $_POST['jadwal_id'] : [$_POST['id'] ?? null];

            $slots = [];
            foreach ($hariList as $index => $hari) {
                $slots[] = [
                    'id' => $idList[$index] ?? null,
                    'hari' => $hari,
                    'jam_mulai' => $jamMulaiList[$index] ?? null,
                    'jam_selesai' => $jamSelesaiList[$index] ?? null,
                    'ruang' => $ruangList[$index] ?? null
                ];
            }

            if($this->mataPelajaranModel->updateJadwalGroup($baseData, $slots, $_POST['id'])) {
                $_SESSION['success'] = 'Jadwal mata pelajaran berhasil diperbarui!';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui jadwal mata pelajaran!';
            }

            $redirectKelas = $_POST['kelas_id'] ?? null;
            header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran' . ($redirectKelas ? '&kelas_id=' . urlencode($redirectKelas) : ''));
            exit;
        }
    }

    public function deleteMataPelajaran() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            if ($this->isJadwalInArchivedKelas($id)) {
                $this->redirectJadwalKelas($_POST['kelas_id'] ?? null, 'Kelas arsip tidak bisa dihapus jadwalnya.');
            }
            if($this->mataPelajaranModel->deleteMataPelajaran($id)) {
                $_SESSION['success'] = 'Jadwal mata pelajaran berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus jadwal mata pelajaran!';
            }
            $redirectKelas = $_POST['kelas_id'] ?? null;
            header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran' . ($redirectKelas ? '&kelas_id=' . urlencode($redirectKelas) : ''));
            exit;
        }
    }

    
    public function getSiswaDalamMapel() {
        if(isset($_GET['mapel_id'])) {
            $mapel_id = $_GET['mapel_id'];
            $siswa = $this->mataPelajaranModel->getSiswaInMataPelajaran($mapel_id);
            header('Content-Type: application/json');
            echo json_encode($siswa);
            exit;
        }
    }


    public function getSiswaTersediaMapel() {
        $mapel_id = $_GET['mapel_id'] ?? null;
        $filters = [
            'kelas' => isset($_GET['kelas']) ? trim($_GET['kelas']) : '',
            'jurusan' => isset($_GET['jurusan']) ? trim($_GET['jurusan']) : '',
            'agama' => isset($_GET['agama']) ? trim($_GET['agama']) : '',
            'search' => isset($_GET['search']) ? trim($_GET['search']) : '',
        ];
        $siswa = $this->mataPelajaranModel->getAvailableSiswa($mapel_id, $filters);
        header('Content-Type: application/json');
        echo json_encode($siswa);
        exit;
    }

    
    public function addSiswaToMapel() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'];
            $mapel_id = $_POST['mapel_id'];
            if ($this->isJadwalInArchivedKelas($mapel_id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Kelas arsip tidak bisa diubah pesertanya.']);
                exit;
            }
            $ok = $this->mataPelajaranModel->addSiswaToMataPelajaran($siswa_id, $mapel_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }

    public function addMultipleSiswaToMapel() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $mapel_id = $_POST['mapel_id'] ?? null;
            $siswa_ids = $_POST['siswa_ids'] ?? [];
            if ($mapel_id && $this->isJadwalInArchivedKelas($mapel_id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'count' => 0, 'message' => 'Kelas arsip tidak bisa diubah pesertanya.']);
                exit;
            }
            if (!is_array($siswa_ids)) {
                $siswa_ids = explode(',', $siswa_ids);
            }

            $siswa_ids = array_values(array_unique(array_filter(array_map('intval', $siswa_ids))));
            if (!$mapel_id || empty($siswa_ids)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'count' => 0]);
                exit;
            }

            $successCount = 0;
            foreach ($siswa_ids as $siswa_id) {
                if ($this->mataPelajaranModel->addSiswaToMataPelajaran($siswa_id, $mapel_id)) {
                    $successCount++;
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => $successCount > 0, 'count' => $successCount]);
            exit;
        }
    }

    
    public function removeSiswaFromMapel() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $siswa_id = $_POST['siswa_id'];
            $mapel_id = $_POST['mapel_id'];
            if ($this->isJadwalInArchivedKelas($mapel_id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Kelas arsip tidak bisa diubah pesertanya.']);
                exit;
            }
            $ok = $this->mataPelajaranModel->removeSiswaFromMataPelajaran($siswa_id, $mapel_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok]);
            exit;
        }
    }
    
   
    public function getMataPelajaranDalamKelas() {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }

    
    public function getMataPelajaranTersediaKelas() {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }

    public function addMataPelajaranToKelas() {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Relasi kelas lama sudah diganti jadwal mata pelajaran.']);
        exit;
    }

    
    public function removeMataPelajaranFromKelas() {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Relasi kelas lama sudah diganti jadwal mata pelajaran.']);
        exit;
    }

    private function isKelasArchived($kelas) {
        return $kelas && ($kelas->status ?? 'active') === 'archived';
    }

    private function isJadwalInArchivedKelas($jadwalId) {
        $jadwal = $this->mataPelajaranModel->getMataPelajaranById($jadwalId);
        return $jadwal && ($jadwal->kelas_status ?? 'active') === 'archived';
    }

    private function redirectJadwalKelas($kelasId, $message) {
        $_SESSION['error'] = $message;
        header('Location: ' . BASE_URL . '/index.php?action=admin_jadwal_mata_pelajaran' . ($kelasId ? '&kelas_id=' . urlencode($kelasId) : ''));
        exit;
    }
    
    public function lokasi() {
        $lokasi = $this->locationModel->getLokasiSekolah();
    require_once __DIR__ . '/../views/admin/lokasi.php';
    }
    
    public function laporan() {
        $tipe_laporan = $_GET['tipe'] ?? 'sekolah';
        if (!in_array($tipe_laporan, ['sekolah', 'kelas'], true)) {
            $tipe_laporan = 'sekolah';
        }

        $kelas_filter_id = $_GET['kelas_filter_id'] ?? '';
        $tahun_ajaran_filter = $_GET['tahun_ajaran_filter'] ?? '';
        $semester_filter = $_GET['semester_filter'] ?? '';
        $mapel_id = $_GET['mapel_id'] ?? ($_GET['kelas_id'] ?? '');
        $tahun_ajaran_list = $this->getTahunAjaranFilterList();
        $semester_list = $this->getSemesterFilterList();
        $kelas_list = $this->getKelasFilterList($tahun_ajaran_filter, $semester_filter);
        $mapel_list = $this->getMapelFilterList($kelas_filter_id, $tahun_ajaran_filter, $semester_filter);
        
        // Ambil parameter filter periode
        $periode = $_GET['periode'] ?? 'bulanan';
        if (!in_array($periode, ['harian', 'bulanan'], true)) {
            $periode = 'bulanan';
        }
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $kelas_id = $mapel_id;
        $filter_status = $_GET['status'] ?? null;
        
        // Calculate date range based on periode
        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
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
            $all_presensi = $this->getPresensiMapelReportRows($startDate, $endDate, $kelas_filter_id, $mapel_id, $filter_status, $tahun_ajaran_filter, $semester_filter);
            $statistik = $this->buildPresensiMapelStats($all_presensi);
            $total_records = count($all_presensi);
            $total_pages = ceil($total_records / $limit);
            $presensi = array_slice($all_presensi, $offset, $limit);
            $sessions = [];
            $laporan_kemajuan = $this->getLaporanKemajuanMapelRows($startDate, $endDate, $kelas_filter_id, $mapel_id, $tahun_ajaran_filter, $semester_filter);
        } else {
            // Laporan presensi sekolah (default)
            // Get all presensi sekolah for the period
            $db = new Database();
            $db->query('SELECT ps.*, bi.nis, bi.nama, COALESCE(bi.email_ortu, "") AS email 
                        FROM presensi_sekolah ps 
                        JOIN buku_induk bi ON ps.user_id = bi.id 
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

    private function getTahunAjaranFilterList() {
        $db = new Database();
        $db->query('SELECT DISTINCT tahun_ajaran
                    FROM kelas
                    WHERE tahun_ajaran IS NOT NULL AND tahun_ajaran <> ""
                    ORDER BY tahun_ajaran DESC');
        return $db->resultSet();
    }

    private function getSemesterFilterList() {
        $db = new Database();
        $db->query('SELECT DISTINCT semester
                    FROM kelas
                    WHERE semester IS NOT NULL AND semester <> ""
                    ORDER BY semester ASC');
        return $db->resultSet();
    }

    private function getKelasFilterList($tahun_ajaran_filter = '', $semester_filter = '') {
        $db = new Database();
        $sql = 'SELECT id, nama_kelas, tahun_ajaran, semester
                FROM kelas
                WHERE 1=1';
        if ($tahun_ajaran_filter) {
            $sql .= ' AND tahun_ajaran = :tahun_ajaran_filter';
        }
        if ($semester_filter) {
            $sql .= ' AND semester = :semester_filter';
        }
        $sql .= ' ORDER BY tahun_ajaran DESC, semester ASC, nama_kelas ASC';
        $db->query($sql);
        if ($tahun_ajaran_filter) {
            $db->bind(':tahun_ajaran_filter', $tahun_ajaran_filter);
        }
        if ($semester_filter) {
            $db->bind(':semester_filter', $semester_filter);
        }
        return $db->resultSet();
    }

    private function getMapelFilterList($kelas_filter_id = '', $tahun_ajaran_filter = '', $semester_filter = '') {
        $db = new Database();
        $sql = 'SELECT MIN(j.id) as id, j.kelas_jadwal_id, k.nama_kelas, j.nama_mata_pelajaran, u.nama as guru_nama,
                       COUNT(*) as jumlah_pertemuan
                FROM jadwal_mata_pelajaran j
                INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                LEFT JOIN users u ON j.guru_pengampu = u.id
                WHERE 1=1';
        if ($kelas_filter_id) {
            $sql .= ' AND j.kelas_jadwal_id = :kelas_filter_id';
        }
        if ($tahun_ajaran_filter) {
            $sql .= ' AND k.tahun_ajaran = :tahun_ajaran_filter';
        }
        if ($semester_filter) {
            $sql .= ' AND k.semester = :semester_filter';
        }
        $sql .= ' GROUP BY j.kelas_jadwal_id, k.nama_kelas, j.nama_mata_pelajaran, j.guru_pengampu, u.nama
                  ORDER BY k.nama_kelas ASC, j.nama_mata_pelajaran ASC';
        $db->query($sql);
        if ($kelas_filter_id) {
            $db->bind(':kelas_filter_id', (int) $kelas_filter_id);
        }
        if ($tahun_ajaran_filter) {
            $db->bind(':tahun_ajaran_filter', $tahun_ajaran_filter);
        }
        if ($semester_filter) {
            $db->bind(':semester_filter', $semester_filter);
        }
        return $db->resultSet();
    }

    private function getSelectedMapelGroup($mapel_id) {
        if (!$mapel_id) return null;
        $db = new Database();
        $db->query('SELECT kelas_jadwal_id, nama_mata_pelajaran, guru_pengampu
                    FROM jadwal_mata_pelajaran
                    WHERE id = :mapel_id
                    LIMIT 1');
        $db->bind(':mapel_id', (int) $mapel_id);
        return $db->single();
    }

    private function getPresensiMapelReportRows($startDate, $endDate, $kelas_filter_id = '', $mapel_id = '', $filter_status = null, $tahun_ajaran_filter = '', $semester_filter = '') {
        $selectedMapel = $this->getSelectedMapelGroup($mapel_id);
        $db = new Database();
        $sql = 'SELECT COALESCE(pm.id, 0) as id,
                       bi.id as user_id,
                       bi.nis,
                       bi.nama,
                       COALESCE(bi.email_ortu, "") as email,
                       pm.status,
                       COALESCE(pm.waktu, s.waktu_buka) as waktu,
                       pm.jarak,
                       pm.latitude,
                       pm.longitude,
                       pm.jenis,
                       pm.alasan,
                       pm.foto_bukti,
                       s.id as sesi_id,
                       s.waktu_buka as sesi_waktu_buka,
                       s.waktu_tutup as sesi_waktu_tutup,
                       j.id as kelas_id,
                       j.id as jadwal_mata_pelajaran_id,
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
                WHERE DATE(s.waktu_buka) BETWEEN :start_date AND :end_date';
        if ($kelas_filter_id) {
            $sql .= ' AND j.kelas_jadwal_id = :kelas_filter_id';
        }
        if ($tahun_ajaran_filter) {
            $sql .= ' AND k.tahun_ajaran = :tahun_ajaran_filter';
        }
        if ($semester_filter) {
            $sql .= ' AND k.semester = :semester_filter';
        }
        if ($selectedMapel) {
            $sql .= ' AND j.kelas_jadwal_id = :mapel_kelas_id
                      AND j.nama_mata_pelajaran = :nama_mata_pelajaran
                      AND (j.guru_pengampu <=> :guru_pengampu)';
        }
        $sql .= ' ORDER BY s.waktu_buka DESC, k.nama_kelas ASC, j.nama_mata_pelajaran ASC, bi.nama ASC';
        $db->query($sql);
        $db->bind(':start_date', $startDate);
        $db->bind(':end_date', $endDate);
        if ($kelas_filter_id) {
            $db->bind(':kelas_filter_id', (int) $kelas_filter_id);
        }
        if ($tahun_ajaran_filter) {
            $db->bind(':tahun_ajaran_filter', $tahun_ajaran_filter);
        }
        if ($semester_filter) {
            $db->bind(':semester_filter', $semester_filter);
        }
        if ($selectedMapel) {
            $db->bind(':mapel_kelas_id', (int) $selectedMapel->kelas_jadwal_id);
            $db->bind(':nama_mata_pelajaran', $selectedMapel->nama_mata_pelajaran);
            $db->bind(':guru_pengampu', $selectedMapel->guru_pengampu !== null ? (int) $selectedMapel->guru_pengampu : null);
        }
        $rows = $db->resultSet();
        if ($filter_status) {
            $rows = array_values(array_filter($rows, function($row) use ($filter_status) {
                $jenis = $row->jenis ?: 'alpha';
                return $jenis === $filter_status;
            }));
        }
        return $rows;
    }

    private function buildPresensiMapelStats($rows) {
        $statistik = new stdClass();
        $statistik->total_siswa = count($rows);
        $statistik->hadir = 0;
        $statistik->izin = 0;
        $statistik->sakit = 0;
        $statistik->alpha = 0;
        foreach ($rows as $row) {
            $jenis = $row->jenis ?: 'alpha';
            if ($jenis === 'hadir') $statistik->hadir++;
            elseif ($jenis === 'izin') $statistik->izin++;
            elseif ($jenis === 'sakit') $statistik->sakit++;
            elseif ($jenis === 'alpha') $statistik->alpha++;
        }
        return $statistik;
    }

    private function renderMonthlyAttendanceExport($presensi, $report_title, $bulan, $tahun, $asPdf = false) {
        $bulan_names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulan_name = $bulan_names[(int) $bulan - 1] ?? $bulan;
        $daysInMonth = (int) date('t', strtotime($tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01'));
        $rows = $this->buildMonthlyAttendanceRows($presensi, $daysInMonth);

        if ($asPdf) {
            ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($report_title . ' - ' . $bulan_name . ' ' . $tahun); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px; text-align: center; }
        th.name, td.name { text-align: left; min-width: 180px; }
        .kop { text-align: center; border-bottom: 3px solid #000; margin-bottom: 12px; padding-bottom: 8px; }
        .print-btn { margin-bottom: 12px; padding: 8px 12px; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Cetak / Simpan PDF</button>
    <div class="kop">
        <h2>SMK NEGERI 7 Yogyakarta</h2>
        <p>Jalan Gowongan Kidul Blok JT3 No.416, Gowongan, Kec. Jetis, Kota Yogyakarta, DIY 55232</p>
    </div>
    <h3><?php echo htmlspecialchars($report_title); ?></h3>
    <p>Bulan: <?php echo htmlspecialchars($bulan_name . ' ' . $tahun); ?></p>
    <?php $this->echoMonthlyAttendanceTable($rows, $daysInMonth); ?>
</body>
</html><?php
            exit;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Bulanan_' . $bulan_name . '_' . $tahun . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<style>table{border-collapse:collapse}th,td{border:1px solid #333;padding:4px;text-align:center}.name{text-align:left;min-width:180px}</style>';
        echo '</head><body>';
        echo '<h2>SMK NEGERI 7 Yogyakarta</h2>';
        echo '<h3>' . htmlspecialchars($report_title) . '</h3>';
        echo '<p>Bulan: ' . htmlspecialchars($bulan_name . ' ' . $tahun) . '</p>';
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

            if (empty($row->waktu)) {
                continue;
            }

            $day = (int) date('j', strtotime($row->waktu));
            if ($day < 1 || $day > $daysInMonth) {
                continue;
            }

            $code = $this->getAttendanceExportCode($row);
            if ($code === '') {
                continue;
            }

            $existing = $students[$studentId]['days'][$day];
            $students[$studentId]['days'][$day] = ($existing === '' || strpos($existing, $code) !== false) ? ($existing ?: $code) : $existing . '/' . $code;
        }

        uasort($students, function($a, $b) {
            return strcasecmp($a['nama'], $b['nama']);
        });

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
        if (!$jenis && empty($row->status)) {
            return 'A';
        }
        if ($jenis === 'hadir') return 'H';
        if ($jenis === 'izin') return 'I';
        if ($jenis === 'sakit') return 'S';
        if ($jenis === 'alpha') return 'A';
        return '';
    }

    private function echoMonthlyAttendanceTable($rows, $daysInMonth) {
        echo '<table>';
        echo '<tr><th rowspan="2">Urut</th><th rowspan="2">NIPD/NIS</th><th rowspan="2" class="name">Nama Lengkap</th><th rowspan="2">L/P</th><th colspan="' . $daysInMonth . '">Tanggal</th><th colspan="4">Jumlah</th></tr>';
        echo '<tr>';
        for ($day = 1; $day <= $daysInMonth; $day++) echo '<th>' . $day . '</th>';
        echo '<th>H</th><th>I</th><th>S</th><th>A</th></tr>';
        $no = 1;
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($row['nis']) . '</td>';
            echo '<td class="name">' . htmlspecialchars($row['nama']) . '</td>';
            echo '<td></td>';
            for ($day = 1; $day <= $daysInMonth; $day++) echo '<td>' . htmlspecialchars($row['days'][$day]) . '</td>';
            echo '<td>' . $row['hadir'] . '</td><td>' . $row['izin'] . '</td><td>' . $row['sakit'] . '</td><td>' . $row['alpha'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<p>Ket: H = Hadir, I = Izin, S = Sakit, A = Alpha</p>';
    }

    private function getLaporanKemajuanMapelRows($startDate, $endDate, $kelas_filter_id = '', $mapel_id = '', $tahun_ajaran_filter = '', $semester_filter = '') {
        $selectedMapel = $this->getSelectedMapelGroup($mapel_id);
        $db = new Database();
        $sql = 'SELECT s.waktu_buka as tanggal, s.waktu_buka as created_at, s.laporan_kemajuan as catatan,
                       u.nama as guru_nama, j.nama_mata_pelajaran, k.nama_kelas
                FROM presensi_mapel_sesi s
                INNER JOIN jadwal_mata_pelajaran j ON s.jadwal_mata_pelajaran_id = j.id
                INNER JOIN kelas k ON j.kelas_jadwal_id = k.id
                LEFT JOIN users u ON s.guru_id = u.id
                WHERE DATE(s.waktu_buka) BETWEEN :start_date AND :end_date
                  AND s.laporan_kemajuan IS NOT NULL
                  AND s.laporan_kemajuan <> ""';
        if ($kelas_filter_id) {
            $sql .= ' AND j.kelas_jadwal_id = :kelas_filter_id';
        }
        if ($tahun_ajaran_filter) {
            $sql .= ' AND k.tahun_ajaran = :tahun_ajaran_filter';
        }
        if ($semester_filter) {
            $sql .= ' AND k.semester = :semester_filter';
        }
        if ($selectedMapel) {
            $sql .= ' AND j.kelas_jadwal_id = :mapel_kelas_id
                      AND j.nama_mata_pelajaran = :nama_mata_pelajaran
                      AND (j.guru_pengampu <=> :guru_pengampu)';
        }
        $sql .= ' ORDER BY s.waktu_buka DESC';
        $db->query($sql);
        $db->bind(':start_date', $startDate);
        $db->bind(':end_date', $endDate);
        if ($kelas_filter_id) {
            $db->bind(':kelas_filter_id', (int) $kelas_filter_id);
        }
        if ($tahun_ajaran_filter) {
            $db->bind(':tahun_ajaran_filter', $tahun_ajaran_filter);
        }
        if ($semester_filter) {
            $db->bind(':semester_filter', $semester_filter);
        }
        if ($selectedMapel) {
            $db->bind(':mapel_kelas_id', (int) $selectedMapel->kelas_jadwal_id);
            $db->bind(':nama_mata_pelajaran', $selectedMapel->nama_mata_pelajaran);
            $db->bind(':guru_pengampu', $selectedMapel->guru_pengampu !== null ? (int) $selectedMapel->guru_pengampu : null);
        }
        return $db->resultSet();
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
            
            header('Location: ' . BASE_URL . '/index.php?action=admin_users');
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
            
            // Add password to data if provided
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }

            if($this->userModel->updateUser($data)) {
                $_SESSION['success'] = 'User berhasil diperbarui!';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui user!';
            }

            header('Location: ' . BASE_URL . '/index.php?action=admin_users');
            exit;
        }
    }

    public function deleteUser() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            
            if (!$id) {
                $_SESSION['error'] = 'ID user tidak valid';
                header('Location: ' . BASE_URL . '/index.php?action=admin_users');
                exit;
            }
            
            // Cek apakah user yang akan dihapus adalah diri sendiri
            if ($id == $_SESSION['user_id']) {
                $_SESSION['error'] = 'Tidak dapat menghapus akun Anda sendiri';
                header('Location: ' . BASE_URL . '/index.php?action=admin_users');
                exit;
            }
            
            if($this->userModel->deleteUser($id)) {
                $_SESSION['success'] = 'User berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus user';
            }
            
            header('Location: ' . BASE_URL . '/index.php?action=admin_users');
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
            
            header('Location: ' . BASE_URL . '/index.php?action=admin_lokasi');
        }
    }

    public function exportExcel() {
        $periode = $_GET['periode'] ?? 'bulanan';
        if (!in_array($periode, ['harian', 'bulanan'], true)) {
            $periode = 'bulanan';
        }
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $filter_status = $_GET['status'] ?? null;
        $tipe = $_GET['tipe'] ?? 'sekolah';
        if (!in_array($tipe, ['sekolah', 'kelas'], true)) {
            $tipe = 'sekolah';
        }
        $kelas_filter_id = $_GET['kelas_filter_id'] ?? '';
        $tahun_ajaran_filter = $_GET['tahun_ajaran_filter'] ?? '';
        $semester_filter = $_GET['semester_filter'] ?? '';
        $mapel_id = $_GET['mapel_id'] ?? ($_GET['kelas_id'] ?? '');
        $kelas_id = $mapel_id;
        
        // Calculate date range based on periode
        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
        } else {
            $startDate = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
        }
        
        // Get all data (no pagination for export)
        if ($tipe === 'kelas') {
            $presensi = $this->getPresensiMapelReportRows($startDate, $endDate, $kelas_filter_id, $mapel_id, $filter_status, $tahun_ajaran_filter, $semester_filter);
            $statistik = $this->buildPresensiMapelStats($presensi);
            $laporan_kemajuan = $this->getLaporanKemajuanMapelRows($startDate, $endDate, $kelas_filter_id, $mapel_id, $tahun_ajaran_filter, $semester_filter);
            $report_title = 'Laporan Presensi Mata Pelajaran';
        } else {
            $db = new Database();
            $db->query('SELECT ps.*, bi.nama, COALESCE(bi.email_ortu, "") AS email 
                        FROM presensi_sekolah ps 
                        JOIN buku_induk bi ON ps.user_id = bi.id 
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

        if ($periode === 'bulanan') {
            $this->renderMonthlyAttendanceExport($presensi, $report_title, $bulan, $tahun, false);
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
        
        // Kop Surat
        echo '<div style="text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px;">';
        echo '<h1 style="margin: 5px 0; font-size: 18px; text-transform: uppercase;">SMK NEGERI 7 Yogyakarta</h1>';
        ;
        echo '<p style="margin: 3px 0; font-size: 10px;">Jalan Gowongan Kidul Blok JT3 No.416, Gowongan, Kec. Jetis, Kota Yogyakarta, DIY 55232</p>';
        echo '<p style="margin: 3px 0; font-size: 10px;">Telp: (0274) 512403 | Email: smknegeri7jogja@smkn7jogja.sch.id | Website: https://www.smkn7jogja.sch.id/</p>';
        echo '</div>';
        
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
        if ($tipe === 'kelas' && !empty($laporan_kemajuan)) {
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
            echo '<th>Mata Pelajaran</th>';
            echo '<th>Guru</th>';
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
                echo '<td>' . htmlspecialchars($p->nama_mata_pelajaran ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($p->guru_nama ?? '-') . '</td>';
            }
            
            $waktuTs = (isset($p->waktu) && $p->waktu) ? strtotime($p->waktu) : null;
            echo '<td>' . ($waktuTs ? date('d M Y', $waktuTs) : '-') . '</td>';
            echo '<td>' . ($waktuTs ? date('H:i', $waktuTs) : '-') . '</td>';
            
            if ($tipe === 'kelas' && empty($p->status) && empty($p->jenis)) {
                echo '<td>Alpha</td>';
            } elseif (isset($p->status) && $p->status) {
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
        $periode = $_GET['periode'] ?? 'bulanan';
        if (!in_array($periode, ['harian', 'bulanan'], true)) {
            $periode = 'bulanan';
        }
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $filter_status = $_GET['status'] ?? null;
        $tipe = $_GET['tipe'] ?? 'sekolah';
        if (!in_array($tipe, ['sekolah', 'kelas'], true)) {
            $tipe = 'sekolah';
        }
        $kelas_filter_id = $_GET['kelas_filter_id'] ?? '';
        $tahun_ajaran_filter = $_GET['tahun_ajaran_filter'] ?? '';
        $semester_filter = $_GET['semester_filter'] ?? '';
        $mapel_id = $_GET['mapel_id'] ?? ($_GET['kelas_id'] ?? '');
        $kelas_id = $mapel_id;
        
        // Calculate date range based on periode
        if ($periode === 'harian') {
            $startDate = $tanggal;
            $endDate = $tanggal;
        } else {
            $startDate = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
        }
        
        // Get all data (no pagination for export)
        if ($tipe === 'kelas') {
            $presensi = $this->getPresensiMapelReportRows($startDate, $endDate, $kelas_filter_id, $mapel_id, $filter_status, $tahun_ajaran_filter, $semester_filter);
            $statistik = $this->buildPresensiMapelStats($presensi);
            $laporan_kemajuan = $this->getLaporanKemajuanMapelRows($startDate, $endDate, $kelas_filter_id, $mapel_id, $tahun_ajaran_filter, $semester_filter);
            $report_title = 'Laporan Presensi Mata Pelajaran';
        } else {
            $db = new Database();
            $db->query('SELECT ps.*, bi.nis, bi.nama, COALESCE(bi.email_ortu, "") AS email 
                        FROM presensi_sekolah ps 
                        JOIN buku_induk bi ON ps.user_id = bi.id 
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
        
        if ($periode === 'bulanan') {
            $this->renderMonthlyAttendanceExport($presensi, $report_title, $bulan, $tahun, true);
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
         .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 5px 0; font-size: 24px; color: #000; text-transform: uppercase; }
        
        .kop-surat p { margin: 3px 0; font-size: 12px; color: #555; }
        .kop-surat .separator { border-top: 2px solid #000; margin-top: 10px; }
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
     <!-- Kop Surat -->
    <div class="kop-surat">
        <h1>SMK NEGERI 7 Yogyakarta</h1>
        
        <p>Jalan Gowongan Kidul Blok JT3 No.416, Gowongan, Kec. Jetis, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55232</p>
        <p>Telp: (0274) 512403 | Email: smknegeri7jogja@smkn7jogja.sch.id | Website: https://www.smkn7jogja.sch.id/</p>
        <div class="separator"></div>
    </div>
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
    
    <?php if ($tipe === 'kelas' && !empty($laporan_kemajuan)): ?>
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
            <?php $no = 1; foreach($laporan_kemajuan as $l): ?>
            <tr>
                <td><?php echo $no++; ?></td>
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
                <th>Mata Pelajaran</th>
                <th>Guru</th>
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
                <td><?php echo htmlspecialchars($p->nama_mata_pelajaran ?? ''); ?></td>
                <td><?php echo htmlspecialchars($p->guru_nama ?? '-'); ?></td>
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
                    } elseif ($tipe === 'kelas' && empty($p->jenis)) {
                        echo 'Alpha';
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
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Presensi mata pelajaran telah dinonaktifkan.']);
        exit;
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
            $db->query('UPDATE presensi_mapel SET 
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

    
    public function bukuInduk() {
        $siswa = $this->userModel->getUsersByRole('siswa');
        $records = $this->bukuIndukModel->getAll();
        
        require_once __DIR__ . '/../views/admin/buku_induk.php';
    }

    public function saveBukuInduk() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $data = [
            'user_id' => !empty($_POST['user_id']) ? $_POST['user_id'] : null,
            'nama' => trim($_POST['nama'] ?? ''),
            'nis' => trim($_POST['nis'] ?? ''),
            'nisn' => isset($_POST['nisn']) && trim($_POST['nisn']) !== '' ? trim($_POST['nisn']) : null,
            'kelas' => isset($_POST['kelas']) ? trim($_POST['kelas']) : null,
            'jurusan' => isset($_POST['jurusan']) ? trim($_POST['jurusan']) : null,
            'tanggal_diterima' => !empty($_POST['tanggal_diterima']) ? $_POST['tanggal_diterima'] : null,
            'agama' => isset($_POST['agama']) ? trim($_POST['agama']) : null,
            'tempat_lahir' => isset($_POST['tempat_lahir']) && trim($_POST['tempat_lahir']) !== '' ? trim($_POST['tempat_lahir']) : null,
            'tanggal_lahir' => !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null,
            'alamat' => isset($_POST['alamat']) && trim($_POST['alamat']) !== '' ? trim($_POST['alamat']) : null,
            'nama_ayah' => $_POST['nama_ayah'] ?? null,
            'nama_ibu' => $_POST['nama_ibu'] ?? null,
            'nama_wali' => $_POST['nama_wali'] ?? null,
            'no_telp_ortu' => $_POST['no_telp_ortu'] ?? null,
            'email_ortu' => $_POST['email_ortu'] ?? null,
            'dokumen_ijasah' => $_POST['existing_ijasah'] ?? null,
            'dokumen_pas_foto' => $_POST['existing_pas_foto'] ?? null,
            'dokumen_akta_kelahiran' => $_POST['existing_akta_kelahiran'] ?? null,
            'dokumen_kk' => $_POST['existing_kk'] ?? null,
        ];

        if ($data['nama'] === '' || $data['nis'] === '') {
            $_SESSION['error'] = 'Nama dan NIS wajib diisi.';
            header('Location: ' . BASE_URL . '/index.php?action=admin_buku_induk');
            exit();
        }

        $password = $_POST['password'] ?? '';
        if ($password !== '') {
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password siswa minimal 6 karakter.';
                header('Location: ' . BASE_URL . '/index.php?action=admin_buku_induk');
                exit();
            }
            $data['password'] = $password;
        }

        $documentUploads = [
            'dokumen_ijasah' => ['label' => 'Dokumen ijasah', 'images' => false],
            'dokumen_pas_foto' => ['label' => 'Pas foto', 'images' => true],
            'dokumen_akta_kelahiran' => ['label' => 'Akta kelahiran', 'images' => false],
            'dokumen_kk' => ['label' => 'KK', 'images' => false],
        ];

        foreach ($documentUploads as $field => $config) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleBukuIndukUpload($_FILES[$field], $config['images']);
                if (!$uploadResult['success']) {
                    $_SESSION['error'] = $config['label'] . ': ' . $uploadResult['message'];
                    header('Location: ' . BASE_URL . '/index.php?action=admin_buku_induk');
                    exit();
                }
                $data[$field] = $uploadResult['path'];
            }
        }

        if($this->bukuIndukModel->upsert($data)) {
            $_SESSION['success'] = 'Buku induk berhasil disimpan.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan buku induk.';
        }

        header('Location: ' . BASE_URL . '/index.php?action=admin_buku_induk');
        exit();
    }

    public function deleteDokumen() {
        $_SESSION['error'] = 'Tabel dokumen tambahan sudah tidak digunakan.';
        header('Location: ' . BASE_URL . '/index.php?action=admin_buku_induk');
        exit();
    }

    private function handleBukuIndukUpload($file, $allowImage = false) {
        $allowed = ['application/pdf'];
        if ($allowImage) {
            $allowed = array_merge($allowed, ['image/jpeg', 'image/jpg', 'image/png']);
        }
        if(!in_array($file['type'], $allowed)) {
            return ['success' => false, 'message' => $allowImage ? 'Hanya file PDF, JPG, atau PNG yang diperbolehkan.' : 'Hanya file PDF yang diperbolehkan.'];
        }
        $uploadDir = __DIR__ . '/../../public/uploads/buku_induk';
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = uniqid('buku-induk-') . '.' . $ext;
        $target = $uploadDir . '/' . $safeName;
        if(move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => true, 'path' => BASE_URL . '/public/uploads/buku_induk/' . $safeName];
        }
        return ['success' => false, 'message' => 'Gagal mengunggah dokumen.'];
    }


}
?>
