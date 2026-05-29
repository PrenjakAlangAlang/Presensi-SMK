<?php
// app/controllers/SiswaController.php
require_once __DIR__ . '/../models/PresensiModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../models/MataPelajaranModel.php';
require_once __DIR__ . '/../models/PresensiSesiModel.php';
require_once __DIR__ . '/../models/PresensiSekolahSesiModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class SiswaController {
    private $presensiModel;
    private $locationModel;
    private $mataPelajaranModel;
    private $presensiSesiModel;
    private $presensiSekolahSesiModel;
    private $bukuIndukModel;
    private $userModel;
    
    public function __construct() {
        $this->presensiModel = new PresensiModel();
        $this->locationModel = new LocationModel();
        $this->mataPelajaranModel = new MataPelajaranModel();
        $this->presensiSesiModel = new PresensiSesiModel();
        $this->presensiSekolahSesiModel = new PresensiSekolahSesiModel();
        $this->bukuIndukModel = new BukuIndukModel();
        $this->userModel = new UserModel();
    }

    
    
   
    
    private function validateLocationAuthenticity($accuracy, $samplesJson) {
        // Layer 1: Akurasi terlalu sempurna 
        if ($accuracy !== null && (float)$accuracy < 5) {
            return [
                'valid'   => false,
                'message' => 'Presensi ditolak! Terdeteksi penggunaan lokasi palsu (akurasi GPS terlalu sempurna: ' . round($accuracy, 1) . 'm).'
            ];
        }

        // Layer 2: Cek apakah koordinat benar-benar diam (statis sempurna = fake GPS)
        
        $samples = json_decode($samplesJson, true);

        if (is_array($samples) && count($samples) >= 2) {
            $allIdentical = true;

            for ($i = 1; $i < count($samples); $i++) {
                $diffLat = abs((float)$samples[$i]['lat'] - (float)$samples[0]['lat']);
                $diffLng = abs((float)$samples[$i]['lng'] - (float)$samples[0]['lng']);

                // Jika ada pergeseran > 0.000001 derajat, koordinat bergerak secara alami
                if ($diffLat > 0.000001 || $diffLng > 0.000001) {
                    $allIdentical = false;
                    break;
                }
            }

            if ($allIdentical) {
                return [
                    'valid'   => false,
                    'message' => 'Presensi ditolak! Terdeteksi penggunaan lokasi palsu (koordinat GPS tidak bergerak sama sekali).'
                ];
            }
        }

        return ['valid' => true];
    }
    
    public function dashboard() {
        $this->presensiModel->closeExpiredSekolahSessions();
        $user_id = $_SESSION['user_id'];
        $statistik = $this->presensiModel->getStatistikKehadiran($user_id);
        $presensiTerakhir = $this->presensiModel->getPresensiSekolahByUser($user_id, 5);
        $presensiHariIni = $this->presensiModel->getPresensiHariIni($user_id);
        $kelas = $this->mataPelajaranModel->getMataPelajaranBySiswa($user_id);
        
        require_once __DIR__ . '/../views/siswa/dashboard.php';
    }
    
    public function presensi() {
        $this->presensiModel->closeExpiredSekolahSessions();
        $user_id = $_SESSION['user_id'];
        $lokasiSekolah = $this->locationModel->getLokasiSekolah();
        $kelas = [];

        require_once __DIR__ . '/../views/siswa/presensi.php';
    }

    public function presensiMapel() {
        $this->presensiSesiModel->closeExpiredSessions();
        $user_id = $_SESSION['user_id'];
        $lokasiSekolah = $this->locationModel->getLokasiSekolah();
        $jadwalHariIni = $this->presensiSesiModel->getTodayForSiswa($user_id);
        $riwayatMapel = $this->presensiModel->getPresensiKelasByUser($user_id, 10);

        require_once __DIR__ . '/../views/siswa/presensi_mapel.php';
    }
    
    public function riwayat() {
        $this->presensiModel->closeExpiredSekolahSessions();
        $this->presensiSesiModel->closeExpiredSessions();
        $user_id = $_SESSION['user_id'];
        
        $periode = $_GET['periode'] ?? 'bulanan';
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $bulan   = $_GET['bulan']   ?? date('m');
        $tahun   = $_GET['tahun']   ?? date('Y');
        if (!in_array($periode, ['harian', 'bulanan'], true)) {
            $periode = 'bulanan';
        }
        $mapelFilter = trim($_GET['mapel'] ?? '');
        $semesterFilter = trim($_GET['semester'] ?? '');
        $tahunAjaranFilter = trim($_GET['tahun_ajaran'] ?? '');
        $mapelFilterOptions = $this->getMapelRiwayatFilterOptions($user_id);
        $mapelFilters = [
            'mapel' => $mapelFilter !== '' ? $mapelFilter : null,
            'semester' => $semesterFilter !== '' ? $semesterFilter : null,
            'tahun_ajaran' => $tahunAjaranFilter !== '' ? $tahunAjaranFilter : null,
        ];
        
        if ($periode === 'harian') {
            $statistik      = $this->presensiModel->getStatistikPresensiSekolah($tanggal, null, null, $user_id);
            $presensiSekolah = $this->presensiModel->getPresensiSekolahByUserPeriode($user_id, $tanggal, null);
            $statistikKelas = $this->presensiModel->getStatistikPresensiKelas($tanggal, null, null, $user_id, $mapelFilters);
            $presensiKelas = $this->presensiModel->getPresensiKelasByUserPeriode($user_id, $tanggal, null, $mapelFilters);
        } else {
            $statistik      = $this->presensiModel->getStatistikPresensiSekolah(null, $bulan, $tahun, $user_id);
            $presensiSekolah = $this->presensiModel->getPresensiSekolahByUser($user_id, 100);
            $statistikKelas = $this->presensiModel->getStatistikPresensiKelas(null, $bulan, $tahun, $user_id, $mapelFilters);
            $presensiKelas = $this->presensiModel->getPresensiKelasByUser($user_id, 100, $mapelFilters);
            
            $presensiSekolah = array_filter($presensiSekolah, function($p) use ($bulan, $tahun) {
                return date('m', strtotime($p->waktu)) == $bulan && date('Y', strtotime($p->waktu)) == $tahun;
            });
            $presensiKelas = array_filter($presensiKelas, function($p) use ($bulan, $tahun) {
                return date('m', strtotime($p->waktu)) == $bulan && date('Y', strtotime($p->waktu)) == $tahun;
            });
        }
        
        $chartData      = $this->getChartData($user_id, $periode, $tanggal, null, $bulan, $tahun);
        $chartDataKelas = $this->getChartDataKelas($user_id, $periode, $tanggal, null, $bulan, $tahun, $mapelFilters);
        
        require_once __DIR__ . '/../views/siswa/riwayat.php';
    }
    
    public function izin() {
        $this->presensiModel->closeExpiredSekolahSessions();
        $user_id = $_SESSION['user_id'];
        $riwayatIzin = $this->presensiModel->getIzinBySiswa($user_id);
        $kelasSiswa  = $this->mataPelajaranModel->getMataPelajaranBySiswa($user_id);
        
        $sesiSekolahAktif = $this->presensiSekolahSesiModel->getActiveSession();
        
        foreach ($kelasSiswa as $kelas) {
            $kelas->sesi_aktif = $this->presensiSesiModel->getActiveSessionByKelas($kelas->id);
        }
        
        require_once __DIR__ . '/../views/siswa/izin.php';
    }
    
    public function submitPresensiSekolah() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->presensiModel->closeExpiredSekolahSessions();
            $user_id   = $_SESSION['user_id'];
            $latitude  = $_POST['latitude']  ?? 0;
            $longitude = $_POST['longitude'] ?? 0;
            $jenis     = $_POST['jenis']     ?? 'hadir';
            $alasan    = $_POST['alasan']    ?? null;
            //  Data tambahan untuk deteksi fake GPS
            $accuracy  = isset($_POST['accuracy']) ? (float)$_POST['accuracy'] : null;
            $samples   = $_POST['samples'] ?? '[]';

            $activeSession = $this->presensiSekolahSesiModel->getActiveSession();

            if (!$activeSession) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tidak ada sesi presensi sekolah aktif saat ini.']);
                exit;
            }

            $existingPresensi = $this->presensiModel->getPresensiInSchoolSession($user_id, $activeSession->id);
            
            if ($existingPresensi && $existingPresensi->jenis !== 'alpha') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan presensi untuk sesi sekolah ini.']);
                exit;
            }

            if ($jenis === 'izin' || $jenis === 'sakit') {
                // Izin/sakit tidak butuh validasi GPS maupun deteksi fake
                $latitude  = 0;
                $longitude = 0;
                $distance  = 0;
                $isValid   = true;
            } else {
                //  DETEKSI FAKE GPS 
                $mockCheck = $this->validateLocationAuthenticity($accuracy, $samples);
                if (!$mockCheck['valid']) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $mockCheck['message']]);
                    exit;
                }

                // Validasi radius Haversine
                $distance = $this->locationModel->getDistance($latitude, $longitude);
                $isValid  = $this->locationModel->validateLocation($latitude, $longitude);
                
                if (!$isValid) {
                    $lokasiSekolah = $this->locationModel->getLokasiSekolah();
                    $radiusMax     = $lokasiSekolah ? $lokasiSekolah->radius_presensi : 100;
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Presensi ditolak! Anda berada di luar radius sekolah. Jarak Anda: ' . round($distance, 2) . ' meter. Radius maksimal: ' . $radiusMax . ' meter.'
                    ]);
                    exit;
                }
            }
            
            $foto_bukti = null;
            if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->handleBuktiUpload($_FILES['bukti']);
                if ($upload['success']) {
                    $foto_bukti = $upload['path'];
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $upload['message']]);
                    exit;
                }
            }

            $data = [
                'presensi_sekolah_sesi_id' => $activeSession->id,
                'user_id'    => $user_id,
                'latitude'   => $latitude,
                'longitude'  => $longitude,
                'jarak'      => $distance,
                'status'     => $isValid ? 'valid' : 'invalid',
                'jenis'      => $jenis,
                'alasan'     => $alasan,
                'foto_bukti' => $foto_bukti
            ];

            header('Content-Type: application/json');
            
            if ($existingPresensi && $existingPresensi->jenis === 'alpha') {
                if ($this->presensiModel->updatePresensiSekolahById($existingPresensi->id, $data)) {
                    echo json_encode(['success' => true, 'valid' => $isValid, 'jenis' => $jenis, 'updated' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui presensi']);
                }
            } else {
                if ($this->presensiModel->recordPresensiSekolah($data)) {
                    echo json_encode(['success' => true, 'valid' => $isValid, 'jenis' => $jenis]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan presensi']);
                }
            }
            exit;
        }
    }
    
    public function submitPresensiKelas() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Metode tidak valid.']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $jadwal_id = (int) ($_POST['kelas_id'] ?? 0);
        $jenis = $_POST['jenis'] ?? 'hadir';
        $alasan = $_POST['alasan'] ?? null;
        $latitude = (float) ($_POST['latitude'] ?? 0);
        $longitude = (float) ($_POST['longitude'] ?? 0);
        $accuracy = isset($_POST['accuracy']) ? (float) $_POST['accuracy'] : null;
        $samples = $_POST['samples'] ?? '[]';

        header('Content-Type: application/json');
        $this->presensiSesiModel->closeExpiredSessions();

        if (!$jadwal_id || !in_array($jenis, ['hadir', 'izin', 'sakit'], true)) {
            echo json_encode(['success' => false, 'message' => 'Data presensi tidak lengkap.']);
            exit;
        }

        $kelasSiswa = $this->mataPelajaranModel->getMataPelajaranBySiswa($user_id);
        $terdaftar = false;
        foreach ($kelasSiswa as $kelas) {
            if ((int) $kelas->id === $jadwal_id) {
                $terdaftar = true;
                break;
            }
        }
        if (!$terdaftar) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak terdaftar pada mata pelajaran ini.']);
            exit;
        }

        $activeSession = $this->presensiSesiModel->getActiveSessionByKelas($jadwal_id);
        if (!$activeSession) {
            echo json_encode(['success' => false, 'message' => 'Sesi presensi mata pelajaran belum aktif atau sudah ditutup.']);
            exit;
        }

        if ($this->presensiModel->hasPresensiInSession($user_id, $activeSession->id)) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan presensi untuk sesi ini.']);
            exit;
        }

        if (($jenis === 'izin' || $jenis === 'sakit') && trim((string) $alasan) === '') {
            echo json_encode(['success' => false, 'message' => 'Alasan wajib diisi untuk izin atau sakit.']);
            exit;
        }

        if ($jenis === 'hadir') {
            $mockCheck = $this->validateLocationAuthenticity($accuracy, $samples);
            if (!$mockCheck['valid']) {
                echo json_encode(['success' => false, 'message' => $mockCheck['message']]);
                exit;
            }

            $distance = $this->locationModel->getDistance($latitude, $longitude);
            if (!$this->locationModel->validateLocation($latitude, $longitude)) {
                echo json_encode(['success' => false, 'message' => 'Presensi ditolak! Anda berada di luar radius sekolah.']);
                exit;
            }
        } else {
            $latitude = 0;
            $longitude = 0;
            $distance = 0;
        }

        $foto_bukti = null;
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->handleBuktiUpload($_FILES['bukti']);
            if (!$upload['success']) {
                echo json_encode(['success' => false, 'message' => $upload['message']]);
                exit;
            }
            $foto_bukti = $upload['path'];
        }

        $ok = $this->presensiModel->recordPresensiKelas([
            'presensi_sesi_id' => $activeSession->id,
            'user_id' => $user_id,
            'jadwal_mata_pelajaran_id' => $jadwal_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'jarak' => $distance,
            'status' => 'valid',
            'jenis' => $jenis,
            'alasan' => $alasan,
            'foto_bukti' => $foto_bukti
        ]);

        echo json_encode(['success' => (bool) $ok, 'message' => $ok ? 'Presensi mata pelajaran berhasil dicatat.' : 'Gagal menyimpan presensi.']);
        exit;
    }

    public function bukuInduk() {
        $user_id = $_SESSION['user_id'];
        $record  = $this->bukuIndukModel->getByUserId($user_id);
        $kelasMasterList = $this->mataPelajaranModel->getAllKelasMaster();
        require_once __DIR__ . '/../views/siswa/buku_induk.php';
    }

    public function saveBukuInduk() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $user_id = $_SESSION['user_id'];
        $existingRecord = $this->bukuIndukModel->getByUserId($user_id);
        $data = [
            'user_id'       => $user_id,
            'nama'          => trim($_POST['nama'] ?? ''),
            'nipd'           => trim($_POST['nipd'] ?? ''),
            'email'         => $existingRecord->email ?? $this->generateStudentEmail($_POST['nipd'] ?? ''),
            'nisn'          => isset($_POST['nisn']) && trim($_POST['nisn']) !== '' ? trim($_POST['nisn']) : null,
            'kelas_id'      => !empty($_POST['kelas_id']) ? $_POST['kelas_id'] : ($existingRecord->kelas_id ?? null),
            'kelas'         => isset($_POST['kelas_label']) ? trim($_POST['kelas_label']) : ($existingRecord->kelas ?? null),
            'jurusan'       => isset($_POST['jurusan_label']) ? trim($_POST['jurusan_label']) : ($existingRecord->jurusan ?? null),
            'tanggal_diterima' => $existingRecord->tanggal_diterima ?? null,
            'agama'         => isset($_POST['agama']) ? trim($_POST['agama']) : ($existingRecord->agama ?? null),
            'tempat_lahir'  => isset($_POST['tempat_lahir']) && trim($_POST['tempat_lahir']) !== '' ? trim($_POST['tempat_lahir']) : null,
            'tanggal_lahir' => !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null,
            'alamat'        => isset($_POST['alamat']) && trim($_POST['alamat']) !== '' ? trim($_POST['alamat']) : null,
            'nama_ayah'     => isset($_POST['nama_ayah'])    ? trim($_POST['nama_ayah'])    : null,
            'nama_ibu'      => isset($_POST['nama_ibu'])     ? trim($_POST['nama_ibu'])     : null,
            'nama_wali'     => isset($_POST['nama_wali'])    ? trim($_POST['nama_wali'])    : null,
            'no_telp_ortu'  => isset($_POST['no_telp_ortu']) ? trim($_POST['no_telp_ortu']) : null,
            'email_ortu'    => isset($_POST['email_ortu'])   ? trim($_POST['email_ortu'])   : null,
            'dokumen_ijasah' => $_POST['existing_ijasah'] ?? null,
            'dokumen_pas_foto' => $_POST['existing_pas_foto'] ?? null,
            'dokumen_akta_kelahiran' => $_POST['existing_akta_kelahiran'] ?? null,
            'dokumen_kk' => $_POST['existing_kk'] ?? null
        ];

        if ($data['nama'] === '' || $data['nipd'] === '') {
            $_SESSION['error'] = 'Nama dan NIPD wajib diisi.';
            header('Location: ' . BASE_URL . '/index.php?action=siswa_buku_induk');
            exit();
        }

        if ($this->hasValueLongerThan($data, ['nama', 'email', 'nama_ayah', 'nama_ibu', 'nama_wali', 'email_ortu'], 50)) {
            $_SESSION['error'] = 'Kolom nama dan email maksimal 50 karakter.';
            header('Location: ' . BASE_URL . '/index.php?action=siswa_buku_induk');
            exit();
        }

        if (!empty($data['kelas_id'])) {
            $kelasMaster = $this->mataPelajaranModel->getKelasMasterById($data['kelas_id']);
            if (!$kelasMaster) {
                $_SESSION['error'] = 'Kelas yang dipilih tidak valid.';
                header('Location: ' . BASE_URL . '/index.php?action=siswa_buku_induk');
                exit();
            }
            $data['kelas'] = $kelasMaster->nama_kelas ?? null;
            $data['jurusan'] = $kelasMaster->jurusan ?? null;
        }

        $documentUploads = [
            'dokumen_ijasah' => ['label' => 'Dokumen ijasah', 'images' => false],
            'dokumen_pas_foto' => ['label' => 'Pas foto', 'images' => true],
            'dokumen_akta_kelahiran' => ['label' => 'Akta kelahiran', 'images' => false],
            'dokumen_kk' => ['label' => 'KK', 'images' => false],
        ];

        foreach ($documentUploads as $field => $config) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $upload = $this->handleBukuIndukUpload($_FILES[$field], $config['images']);
                if (!$upload['success']) {
                    $_SESSION['error'] = $config['label'] . ': ' . $upload['message'];
                    header('Location: ' . BASE_URL . '/index.php?action=siswa_buku_induk');
                    exit();
                }
                $data[$field] = $upload['path'];
            }
        }

        if ($this->bukuIndukModel->upsert($data)) {
            $_SESSION['success'] = 'Data buku induk diperbarui.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan data.';
        }

        header('Location: ' . BASE_URL . '/index.php?action=siswa_buku_induk');
        exit();
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $user_id = $_SESSION['user_id'];
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password minimal 6 karakter.';
        } elseif ($password !== $password_confirm) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
        } elseif ($this->bukuIndukModel->updatePasswordByUserId($user_id, $password)) {
            $_SESSION['success'] = 'Password berhasil diperbarui.';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui password.';
        }

        header('Location: ' . BASE_URL . '/index.php?action=siswa_buku_induk');
        exit();
    }

    private function generateStudentEmail($nipd) {
        $safeNipd = preg_replace('/[^a-zA-Z0-9._-]/', '', trim((string) $nipd));
        if ($safeNipd === '') {
            $safeNipd = uniqid('siswa');
        }
        return strtolower($safeNipd) . '@smk7.sch.id';
    }

    public function deleteDokumen() {
        $_SESSION['error'] = 'Tabel dokumen tambahan sudah tidak digunakan.';
        header('Location: ' . BASE_URL . '/index.php?action=siswa_buku_induk');
        exit();
    }

    private function handleBukuIndukUpload($file, $allowImage = false) {
        $allowed = ['application/pdf'];
        if ($allowImage) {
            $allowed = array_merge($allowed, ['image/jpeg', 'image/jpg', 'image/png']);
        }
        if (!in_array($file['type'], $allowed)) {
            return ['success' => false, 'message' => $allowImage ? 'File harus PDF, JPG, atau PNG.' : 'File harus PDF.'];
        }
        $uploadDir = __DIR__ . '/../../public/uploads/buku_induk';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeName = uniqid('buku-induk-') . '.' . $ext;
        $target   = $uploadDir . '/' . $safeName;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => true, 'path' => BASE_URL . '/public/uploads/buku_induk/' . $safeName];
        }
        return ['success' => false, 'message' => 'Gagal upload dokumen.'];
    }

    private function handleBuktiUpload($file) {
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!in_array($file['type'], $allowed)) {
            return ['success' => false, 'message' => 'File harus JPG, PNG, atau PDF.'];
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 2MB.'];
        }
        $uploadDir = __DIR__ . '/../../public/uploads/izin';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = uniqid('bukti-') . '.' . $ext;
        $target   = $uploadDir . '/' . $safeName;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => true, 'path' => BASE_URL . '/public/uploads/izin/' . $safeName];
        }
        return ['success' => false, 'message' => 'Gagal upload bukti.'];
    }

    private function getMapelRiwayatFilterOptions($user_id) {
        $jadwalSiswa = $this->mataPelajaranModel->getMataPelajaranBySiswa($user_id);
        $mapel = [];
        $semester = [];
        $tahunAjaran = [];

        foreach ($jadwalSiswa as $jadwal) {
            if (!empty($jadwal->nama_mata_pelajaran)) {
                $mapel[$jadwal->nama_mata_pelajaran] = $jadwal->nama_mata_pelajaran;
            }
            if (!empty($jadwal->semester)) {
                $semester[$jadwal->semester] = $jadwal->semester;
            }
            if (!empty($jadwal->tahun_ajaran)) {
                $tahunAjaran[$jadwal->tahun_ajaran] = $jadwal->tahun_ajaran;
            }
        }

        natcasesort($mapel);
        natcasesort($semester);
        rsort($tahunAjaran, SORT_NATURAL);

        return [
            'mapel' => array_values($mapel),
            'semester' => array_values($semester),
            'tahun_ajaran' => array_values($tahunAjaran),
        ];
    }
    
    private function getStatistikMingguan($user_id, $startDate, $endDate) {
        $db = new Database();
        $db->query('SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "valid" AND jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN jenis = "izin" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                    FROM presensi_sekolah 
                    WHERE user_id = :user_id AND DATE(waktu) BETWEEN :start_date AND :end_date');
        $db->bind(':user_id',     $user_id);
        $db->bind(':start_date',  $startDate);
        $db->bind(':end_date',    $endDate);
        return $db->single();
    }
    
    private function getStatistikMingguanKelas($user_id, $startDate, $endDate, $kelas_jadwal_id = null) {
        $db = new Database();
        $db->query('SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN pm.status = "valid" AND pm.jenis = "hadir" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN pm.jenis = "izin" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN pm.jenis = "sakit" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN pm.jenis = "alpha" THEN 1 ELSE 0 END) as alpha
                    FROM presensi_mapel pm
                    INNER JOIN jadwal_mata_pelajaran j ON pm.jadwal_mata_pelajaran_id = j.id
                    WHERE pm.user_id = :user_id AND DATE(pm.waktu) BETWEEN :start_date AND :end_date' .
                    ($kelas_jadwal_id ? ' AND j.kelas_jadwal_id = :kelas_jadwal_id' : ''));
        $db->bind(':user_id',    $user_id);
        $db->bind(':start_date', $startDate);
        $db->bind(':end_date',   $endDate);
        if ($kelas_jadwal_id) {
            $db->bind(':kelas_jadwal_id', (int) $kelas_jadwal_id);
        }
        return $db->single();
    }
    
    private function getChartData($user_id, $periode, $tanggal, $minggu, $bulan, $tahun) {
        $db   = new Database();
        $data = [];
        
        if ($periode === 'harian') {
            $labels = [];
            $values = [];
            for ($i = 0; $i < 24; $i++) {
                $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $values[] = 0;
            }
            $db->query('SELECT HOUR(waktu) as jam, COUNT(*) as jumlah 
                       FROM presensi_sekolah 
                       WHERE user_id = :user_id AND DATE(waktu) = :tanggal 
                       GROUP BY HOUR(waktu)');
            $db->bind(':user_id',  $user_id);
            $db->bind(':tanggal',  $tanggal);
            $results = $db->resultSet();
            foreach ($results as $row) {
                $values[$row->jam] = $row->jumlah;
            }
            $data['labels'] = $labels;
            $data['values'] = $values;
            
        } elseif ($periode === 'mingguan') {
            $startDate = date('Y-m-d', strtotime($tahun . 'W' . str_pad($minggu, 2, '0', STR_PAD_LEFT)));
            $labels    = [];
            $values    = [];
            for ($i = 0; $i < 7; $i++) {
                $date      = date('Y-m-d', strtotime($startDate . ' +' . $i . ' days'));
                $dayName   = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $labels[]  = $dayName[date('w', strtotime($date))];
                $db->query('SELECT COUNT(*) as jumlah 
                           FROM presensi_sekolah 
                           WHERE user_id = :user_id AND DATE(waktu) = :tanggal AND (status = "valid" OR jenis IN ("izin", "sakit"))');
                $db->bind(':user_id',  $user_id);
                $db->bind(':tanggal',  $date);
                $result   = $db->single();
                $values[] = $result->jumlah ?? 0;
            }
            $data['labels'] = $labels;
            $data['values'] = $values;
            
        } else {
            $labels = [];
            $values = [];
            for ($week = 1; $week <= 5; $week++) {
                $labels[] = 'Minggu ' . $week;
                $values[] = 0;
            }
            $db->query('SELECT DAY(waktu) as hari, COUNT(*) as jumlah 
                       FROM presensi_sekolah 
                       WHERE user_id = :user_id AND MONTH(waktu) = :bulan AND YEAR(waktu) = :tahun AND (status = "valid" OR jenis IN ("izin", "sakit"))
                       GROUP BY DAY(waktu)');
            $db->bind(':user_id', $user_id);
            $db->bind(':bulan',   $bulan);
            $db->bind(':tahun',   $tahun);
            $results = $db->resultSet();
            foreach ($results as $row) {
                $weekNum = (int) ceil($row->hari / 7) - 1;
                if ($weekNum >= 0 && $weekNum < 5) {
                    $values[$weekNum] += $row->jumlah;
                }
            }
            $data['labels'] = $labels;
            $data['values'] = $values;
        }
        
        return $data;
    }
    
    private function getChartDataKelas($user_id, $periode, $tanggal, $minggu, $bulan, $tahun, $filters = []) {
        $filters = is_array($filters) ? $filters : ['kelas_jadwal_id' => $filters];
        $db   = new Database();
        $data = [];
        $filterSql = $this->buildMapelFilterSql($filters);
        
        if ($periode === 'harian') {
            $labels = [];
            $values = [];
            for ($i = 0; $i < 24; $i++) {
                $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $values[] = 0;
            }
            $db->query('SELECT HOUR(pm.waktu) as jam, COUNT(*) as jumlah 
                       FROM presensi_mapel pm
                       INNER JOIN jadwal_mata_pelajaran j ON pm.jadwal_mata_pelajaran_id = j.id
                       LEFT JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                       LEFT JOIN kelas k ON pkel.kelas_id = k.id
                       WHERE pm.user_id = :user_id AND DATE(pm.waktu) = :tanggal' . $filterSql . '
                       GROUP BY HOUR(pm.waktu)');
            $db->bind(':user_id', $user_id);
            $db->bind(':tanggal', $tanggal);
            $this->bindMapelFiltersToDb($db, $filters);
            $results = $db->resultSet();
            foreach ($results as $row) {
                $values[$row->jam] = $row->jumlah;
            }
            $data['labels'] = $labels;
            $data['values'] = $values;
            
        } else {
            $labels = [];
            $values = [];
            for ($week = 1; $week <= 5; $week++) {
                $labels[] = 'Minggu ' . $week;
                $values[] = 0;
            }
            $db->query('SELECT DAY(pm.waktu) as hari, COUNT(*) as jumlah 
                       FROM presensi_mapel pm
                       INNER JOIN jadwal_mata_pelajaran j ON pm.jadwal_mata_pelajaran_id = j.id
                       LEFT JOIN periode_kelas pkel ON j.kelas_jadwal_id = pkel.id
                       LEFT JOIN kelas k ON pkel.kelas_id = k.id
                       WHERE pm.user_id = :user_id AND MONTH(pm.waktu) = :bulan AND YEAR(pm.waktu) = :tahun AND (pm.status = "valid" OR pm.jenis IN ("izin", "sakit"))' . $filterSql . '
                       GROUP BY DAY(pm.waktu)');
            $db->bind(':user_id', $user_id);
            $db->bind(':bulan',   $bulan);
            $db->bind(':tahun',   $tahun);
            $this->bindMapelFiltersToDb($db, $filters);
            $results = $db->resultSet();
            foreach ($results as $row) {
                $weekNum = (int) ceil($row->hari / 7) - 1;
                if ($weekNum >= 0 && $weekNum < 5) {
                    $values[$weekNum] += $row->jumlah;
                }
            }
            $data['labels'] = $labels;
            $data['values'] = $values;
        }
        
        return $data;
    }

    private function buildMapelFilterSql($filters) {
        $sql = '';
        if (!empty($filters['mapel'])) {
            $sql .= ' AND j.nama_mata_pelajaran = :filter_mapel';
        }
        if (!empty($filters['semester'])) {
            $sql .= ' AND pkel.semester = :filter_semester';
        }
        if (!empty($filters['tahun_ajaran'])) {
            $sql .= ' AND pkel.tahun_ajaran = :filter_tahun_ajaran';
        }
        return $sql;
    }

    private function bindMapelFiltersToDb($db, $filters) {
        if (!empty($filters['mapel'])) {
            $db->bind(':filter_mapel', $filters['mapel']);
        }
        if (!empty($filters['semester'])) {
            $db->bind(':filter_semester', $filters['semester']);
        }
        if (!empty($filters['tahun_ajaran'])) {
            $db->bind(':filter_tahun_ajaran', $filters['tahun_ajaran']);
        }
    }

    private function hasValueLongerThan($data, $fields, $limit) {
        foreach ($fields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && strlen((string) $data[$field]) > $limit) {
                return true;
            }
        }
        return false;
    }
}
?>


