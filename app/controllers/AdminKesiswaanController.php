<?php
// app/controllers/AdminKesiswaanController.php
// Peran admin kesiswaan: kelola buku induk seluruh siswa dan kelola sesi presensi sekolah
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/BukuIndukModel.php';
require_once __DIR__ . '/../models/PresensiSekolahSesiModel.php';
require_once __DIR__ . '/../models/PresensiModel.php';

class AdminKesiswaanController {
    private $userModel;
    private $bukuIndukModel;
    private $presensiSekolahSesiModel;
    private $presensiModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->bukuIndukModel = new BukuIndukModel();
        $this->presensiSekolahSesiModel = new PresensiSekolahSesiModel();
        $this->presensiModel = new PresensiModel();
    }

    public function dashboard() {
        $totalSiswa = count($this->userModel->getUsersByRole('siswa'));
        $totalGuru = count($this->userModel->getUsersByRole('guru'));
        $sessions = $this->presensiSekolahSesiModel->getSessions();
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
}
?>
