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
        $user_id = $_SESSION['user_id'];
        $lokasiSekolah = $this->locationModel->getLokasiSekolah();
        $kelas = $this->kelasModel->getKelasBySiswa($user_id);
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
        
        // Get kelas yang diikuti siswa
        $kelasSiswa = $this->kelasModel->getKelasBySiswa($user_id);
        
        // Check sesi presensi sekolah aktif
        $this->presensiSekolahSesiModel->closeExpiredSessions();
        $sesiSekolahAktif = $this->presensiSekolahSesiModel->getActiveSession();
        
        // Check sesi presensi kelas aktif untuk setiap kelas
        foreach ($kelasSiswa as $kelas) {
            $kelas->sesi_aktif = $this->presensiSesiModel->getActiveSessionByKelas($kelas->id);
        }
        
        require_once __DIR__ . '/../views/siswa/izin.php';
    }
    
    public function submitPresensiSekolah() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $latitude = $_POST['latitude'] ?? 0;
            $longitude = $_POST['longitude'] ?? 0;
            $jenis = $_POST['jenis'] ?? 'hadir'; // hadir, izin, sakit
            $alasan = $_POST['alasan'] ?? null;
            
            // Pastikan sesi sekolah yang sudah kadaluarsa ditutup
            $this->presensiSekolahSesiModel->closeExpiredSessions();
            $activeSession = $this->presensiSekolahSesiModel->getActiveSession();

            if (!$activeSession) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tidak ada sesi presensi sekolah aktif saat ini.']);
                return;
            }

            // Cegah duplikat presensi untuk session yang sama
            if ($this->presensiModel->hasPresensiInSchoolSession($user_id, $activeSession->id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan presensi untuk sesi sekolah ini.']);
                return;
            }

            // Jika izin atau sakit, nonaktifkan validasi GPS (set koordinat ke 0)
            if ($jenis === 'izin' || $jenis === 'sakit') {
                $latitude = 0;
                $longitude = 0;
                $distance = 0;
                $isValid = true; // Otomatis valid untuk izin/sakit
            } else {
                // Untuk hadir, validasi lokasi GPS dengan algoritma Haversine
                $distance = $this->locationModel->getDistance($latitude, $longitude);
                $isValid = $this->locationModel->validateLocation($latitude, $longitude);
            }
            
            // Handle upload bukti jika ada (untuk izin/sakit)
            $foto_bukti = null;
            if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->handleBuktiUpload($_FILES['bukti']);
                if($upload['success']) {
                    $foto_bukti = $upload['path'];
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $upload['message']]);
                    return;
                }
            }

            $data = [
                'presensi_sekolah_sesi_id' => $activeSession->id,
                'user_id' => $user_id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'jarak' => $distance,
                'status' => $isValid ? 'valid' : 'invalid',
                'jenis' => $jenis,
                'alasan' => $alasan,
                'foto_bukti' => $foto_bukti
            ];

            // Simpan presensi dan kembalikan hasil serta apakah lokasi valid
            header('Content-Type: application/json');
            if($this->presensiModel->recordPresensiSekolah($data)) {
                echo json_encode(['success' => true, 'valid' => $isValid, 'jenis' => $jenis]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mencatat presensi']);
            }
        }
    }
    
    public function submitPresensiKelas() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $kelas_id = $_POST['kelas_id'];
            $latitude = $_POST['latitude'] ?? 0;
            $longitude = $_POST['longitude'] ?? 0;
            $jenis = $_POST['jenis'] ?? 'hadir'; // hadir, izin, sakit
            $alasan = $_POST['alasan'] ?? null;
            
            // Attach active presensi session id for the kelas
            $activeSession = $this->presensiSesiModel->getActiveSessionByKelas($kelas_id);
            if (!$activeSession) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tidak ada sesi presensi aktif untuk kelas ini.']);
                return;
            }

            // Prevent duplicate presensi for the same session
            if ($this->presensiModel->hasPresensiInSession($user_id, $activeSession->id)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Anda sudah melakukan presensi untuk sesi ini.']);
                return;
            }

            // Jika izin atau sakit, nonaktifkan validasi GPS (set koordinat ke 0)
            if ($jenis === 'izin' || $jenis === 'sakit') {
                $latitude = 0;
                $longitude = 0;
                $distance = 0;
                $isValid = true; // Otomatis valid untuk izin/sakit
            } else {
                // Untuk hadir, validasi lokasi GPS dengan algoritma Haversine
                $distance = $this->locationModel->getDistance($latitude, $longitude);
                $isValid = $this->locationModel->validateLocation($latitude, $longitude);
            }
            
            // Handle upload bukti jika ada (untuk izin/sakit)
            $foto_bukti = null;
            if(isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->handleBuktiUpload($_FILES['bukti']);
                if($upload['success']) {
                    $foto_bukti = $upload['path'];
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $upload['message']]);
                    return;
                }
            }

            $data = [
                'presensi_sesi_id' => $activeSession->id,
                'user_id' => $user_id,
                'kelas_id' => $kelas_id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'jarak' => $distance,
                'status' => $isValid ? 'valid' : 'invalid',
                'jenis' => $jenis,
                'alasan' => $alasan,
                'foto_bukti' => $foto_bukti
            ];

            if($this->presensiModel->recordPresensiKelas($data)) {
                echo json_encode(['success' => true, 'valid' => $isValid, 'jenis' => $jenis]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal mencatat presensi kelas']);
            }
        }
    }

    public function bukuInduk() {
        $user_id = $_SESSION['user_id'];
        $record = $this->bukuIndukModel->getByUserId($user_id);
        $dokumen = [];
        if ($record) {
            $dokumen = $this->bukuIndukModel->getDokumen($record->id);
        }
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
            'nama_ayah' => isset($_POST['nama_ayah']) ? trim($_POST['nama_ayah']) : null,
            'nama_ibu' => isset($_POST['nama_ibu']) ? trim($_POST['nama_ibu']) : null,
            'no_telp_ortu' => isset($_POST['no_telp_ortu']) ? trim($_POST['no_telp_ortu']) : null,
            'email_ortu' => isset($_POST['email_ortu']) ? trim($_POST['email_ortu']) : null,
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
            // Get the buku induk record to get its ID
            $record = $this->bukuIndukModel->getByUserId($user_id);
            
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
            
            $_SESSION['success'] = 'Data buku induk diperbarui.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan data.';
        }

        header('Location: ' . BASE_URL . '/public/index.php?action=siswa_buku_induk');
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

    private function handleBuktiUpload($file) {
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if(!in_array($file['type'], $allowed)) {
            return ['success' => false, 'message' => 'File harus JPG, PNG, atau PDF.'];
        }
        
        // Cek ukuran file (max 2MB)
        if($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 2MB.'];
        }
        
        $uploadDir = __DIR__ . '/../../public/uploads/izin';
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = uniqid('bukti-') . '.' . $ext;
        $target = $uploadDir . '/' . $safeName;
        
        if(move_uploaded_file($file['tmp_name'], $target)) {
            $relative = BASE_URL . '/public/uploads/izin/' . $safeName;
            return ['success' => true, 'path' => $relative];
        }
        return ['success' => false, 'message' => 'Gagal upload bukti.'];
    }
}
?>