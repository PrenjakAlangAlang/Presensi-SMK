<?php

// index.php
// Front controller sederhana: load config, cek autentikasi, dan route ke controller sesuai action

// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/config/config.php';

// Routing sederhana
$action = $_GET['action'] ?? 'login';

// Check authentication for protected routes
$protectedRoutes = [
    'admin_dashboard', 'admin_users', 'admin_kelas', 'admin_jadwal_mata_pelajaran', 'admin_lokasi', 'admin_laporan', 'admin_create_user',
    'admin_update_user', 'admin_delete_user', 'admin_export_excel', 'admin_export_pdf',
    'admin_presensi_sekolah', 'admin_create_presensi_sekolah', 'admin_extend_presensi_sekolah', 'admin_close_presensi_sekolah',
    'admin_delete_presensi_sekolah', 'admin_delete_multiple_presensi_sekolah',
    'admin_ubah_status_presensi_sekolah', 'admin_ubah_status_presensi_mapel',
    'admin_create_kelas', 'admin_update_kelas', 'admin_delete_kelas', 'admin_toggle_kelas_status',
    'admin_create_kelas_master', 'admin_update_kelas_master', 'admin_delete_kelas_master',
    'admin_mata_pelajaran', 'admin_create_mata_pelajaran', 'admin_update_mata_pelajaran', 'admin_delete_mata_pelajaran',
    'admin_get_siswa_mapel', 'admin_get_siswa_tersedia_mapel', 'admin_add_siswa_mapel', 'admin_add_multiple_siswa_mapel', 'admin_remove_siswa_mapel',
    'admin_get_mapel_kelas', 'admin_get_mapel_tersedia_kelas', 'admin_add_mapel_kelas', 'admin_remove_mapel_kelas',
    'admin_buku_induk', 'admin_save_buku_induk',
    'admin_kesiswaan_dashboard', 'admin_kesiswaan_buku_induk', 'admin_kesiswaan_presensi_sekolah',
    'admin_kesiswaan_create_presensi_sekolah', 'admin_kesiswaan_extend_presensi_sekolah', 'admin_kesiswaan_close_presensi_sekolah',
    'admin_kesiswaan_delete_presensi_sekolah', 'admin_kesiswaan_delete_multiple_presensi_sekolah',
    'admin_kesiswaan_get_presensi_sekolah_status', 'admin_kesiswaan_save_buku_induk',
    'admin_kesiswaan_ubah_status_presensi_sekolah', 'admin_kesiswaan_ubah_status_presensi_mapel',
    'admin_kesiswaan_laporan', 'admin_kesiswaan_export_excel', 'admin_kesiswaan_export_pdf',
    'guru_dashboard', 'guru_kelas', 'guru_presensi_mapel', 'guru_laporan', 'guru_export_pdf', 'guru_export_excel',
    'buka_presensi_mapel', 'tutup_presensi_mapel', 'hapus_presensi_mapel_sesi', 'simpan_laporan_kemajuan_mapel', 'get_presensi_mapel', 'guru_ubah_status_presensi',
    'siswa_dashboard', 'siswa_presensi', 'siswa_presensi_mapel', 'siswa_riwayat',
    'siswa_buku_induk', 'siswa_save_buku_induk', 'siswa_change_password', 'submit_presensi_sekolah', 'submit_presensi_mapel'
];

if (in_array($action, $protectedRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/index.php?action=login');
    exit();
}

$roleProtectedRoutes = [
    'admin_presensi_sekolah' => ['admin'],
    'admin_create_presensi_sekolah' => ['admin'],
    'admin_extend_presensi_sekolah' => ['admin'],
    'admin_close_presensi_sekolah' => ['admin'],
    'admin_delete_presensi_sekolah' => ['admin'],
    'admin_delete_multiple_presensi_sekolah' => ['admin'],
    'admin_ubah_status_presensi_sekolah' => ['admin'],
    'admin_ubah_status_presensi_mapel' => ['admin'],
    'admin_create_kelas' => ['admin'],
    'admin_update_kelas' => ['admin'],
    'admin_delete_kelas' => ['admin'],
    'admin_toggle_kelas_status' => ['admin'],
    'admin_create_kelas_master' => ['admin'],
    'admin_update_kelas_master' => ['admin'],
    'admin_delete_kelas_master' => ['admin'],
    'admin_kesiswaan_presensi_sekolah' => ['admin_kesiswaan'],
    'admin_kesiswaan_create_presensi_sekolah' => ['admin_kesiswaan'],
    'admin_kesiswaan_extend_presensi_sekolah' => ['admin_kesiswaan'],
    'admin_kesiswaan_close_presensi_sekolah' => ['admin_kesiswaan'],
    'admin_kesiswaan_delete_presensi_sekolah' => ['admin_kesiswaan'],
    'admin_kesiswaan_delete_multiple_presensi_sekolah' => ['admin_kesiswaan'],
    'admin_kesiswaan_get_presensi_sekolah_status' => ['admin_kesiswaan'],
    'admin_kesiswaan_ubah_status_presensi_sekolah' => ['admin_kesiswaan'],
    'admin_kesiswaan_ubah_status_presensi_mapel' => ['admin_kesiswaan'],
];

if (isset($roleProtectedRoutes[$action])) {
    $currentRole = $_SESSION['user_role'] ?? null;
    if (!in_array($currentRole, $roleProtectedRoutes[$action], true)) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
            exit();
        }

        header('Location: ' . BASE_URL . '/index.php?action=login');
        exit();
    }
}

// Routing utama: panggil controller dan method sesuai action
switch($action) {
    case 'login':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        break;
        
    case 'logout':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'register_siswa':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->registerSiswa();
        break;
        
    // Admin Routes
    case 'admin_dashboard':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->dashboard();
        break;
        
    case 'admin_users':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->users();
        break;
        
    case 'admin_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->kelas();
        break;

    case 'admin_jadwal_mata_pelajaran':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->jadwalMataPelajaran();
        break;
        
    case 'admin_lokasi':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->lokasi();
        break;

    case 'admin_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->presensiSekolah();
        break;

    case 'admin_create_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createPresensiSekolah();
        break;

    case 'admin_extend_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->extendPresensiSekolah();
        break;

    case 'admin_close_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->closePresensiSekolah();
        break;

    case 'get_presensi_sekolah_status':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getPresensiSekolahStatus();
        break;

    case 'admin_delete_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->deletePresensiSekolah();
        break;

    case 'admin_delete_multiple_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->deleteMultiplePresensiSekolah();
        break;
        
    case 'admin_laporan':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->laporan();
        break;

    case 'admin_export_excel':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->exportExcel();
        break;

    case 'admin_export_pdf':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->exportPDF();
        break;

  
        
    case 'admin_create_user':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createUser();
        break;

    case 'admin_update_user':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateUser();
        break;

    case 'admin_delete_user':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->deleteUser();
        break;

    case 'admin_create_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createKelas();
        break;

    case 'admin_update_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateKelas();
        break;

    case 'admin_delete_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->deleteKelas();
        break;

    case 'admin_toggle_kelas_status':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->toggleKelasStatus();
        break;

    case 'admin_create_kelas_master':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createKelasMaster();
        break;

    case 'admin_update_kelas_master':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateKelasMaster();
        break;

    case 'admin_delete_kelas_master':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->deleteKelasMaster();
        break;

    // NOTE: Siswa dikelola PER MATA PELAJARAN, bukan per kelas
    // API endpoints untuk kelola siswa per mata pelajaran

    case 'admin_get_siswa_mapel':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getSiswaDalamMapel();
        break;

    case 'admin_get_siswa_tersedia_mapel':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getSiswaTersediaMapel();
        break;

    case 'admin_add_siswa_mapel':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->addSiswaToMapel();
        break;

    case 'admin_add_multiple_siswa_mapel':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->addMultipleSiswaToMapel();
        break;

    case 'admin_remove_siswa_mapel':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->removeSiswaFromMapel();
        break;

    case 'admin_mata_pelajaran':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->mataPelajaran();
        break;

    case 'admin_create_mata_pelajaran':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createMataPelajaran();
        break;

    case 'admin_update_mata_pelajaran':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateMataPelajaran();
        break;

    case 'admin_delete_mata_pelajaran':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->deleteMataPelajaran();
        break;

    case 'admin_get_mapel_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getMataPelajaranDalamKelas();
        break;

    case 'admin_get_mapel_tersedia_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getMataPelajaranTersediaKelas();
        break;

    case 'admin_add_mapel_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->addMataPelajaranToKelas();
        break;

    case 'admin_remove_mapel_kelas':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->removeMataPelajaranFromKelas();
        break;

    case 'admin_buku_induk':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->bukuInduk();
        break;

    case 'admin_save_buku_induk':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->saveBukuInduk();
        break;

    // Admin Kesiswaan Routes
    case 'admin_kesiswaan_dashboard':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->dashboard();
        break;

    case 'admin_kesiswaan_buku_induk':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->bukuInduk();
        break;

    case 'admin_kesiswaan_save_buku_induk':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->saveBukuInduk();
        break;

    case 'admin_kesiswaan_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->presensiSekolah();
        break;

    case 'admin_kesiswaan_create_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->createPresensiSekolah();
        break;

    case 'admin_kesiswaan_extend_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->extendPresensiSekolah();
        break;

    case 'admin_kesiswaan_close_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->closePresensiSekolah();
        break;

    case 'admin_kesiswaan_delete_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->deletePresensiSekolah();
        break;

    case 'admin_kesiswaan_delete_multiple_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->deleteMultiplePresensiSekolah();
        break;

    case 'admin_kesiswaan_get_presensi_sekolah_status':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->getPresensiSekolahStatus();
        break;

    case 'admin_kesiswaan_laporan':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->laporan();
        break;

    case 'admin_kesiswaan_export_excel':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->exportExcel();
        break;

    case 'admin_kesiswaan_export_pdf':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->exportPDF();
        break;

        
    case 'admin_update_lokasi':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateLokasi();
        break;
        
    // Siswa Routes
    case 'siswa_dashboard':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->dashboard();
        break;
        
    case 'siswa_presensi':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->presensi();
        break;

    case 'siswa_presensi_mapel':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->presensiMapel();
        break;
        
    case 'siswa_riwayat':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->riwayat();
        break;

    case 'siswa_buku_induk':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->bukuInduk();
        break;

    case 'siswa_save_buku_induk':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->saveBukuInduk();
        break;

    case 'siswa_change_password':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->changePassword();
        break;
    
    case 'submit_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->submitPresensiSekolah();
        break;
        
    case 'submit_presensi_mapel':
        require_once __DIR__ . '/app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->submitPresensiKelas();
        break;

    // Guru Routes
    case 'guru_dashboard':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->dashboard();
        break;
        
    case 'guru_kelas':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->kelas();
        break;

    case 'guru_presensi_mapel':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->presensiMapel();
        break;
        
    case 'guru_laporan':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->laporan();
        break;
        
    case 'buka_presensi_mapel':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->bukaPresensiKelas();
        break;
        
    case 'tutup_presensi_mapel':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->tutupPresensiKelas();
        break;

    case 'hapus_presensi_mapel_sesi':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->hapusPresensiMapelSesi();
        break;

    case 'simpan_laporan_kemajuan_mapel':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->simpanLaporanKemajuanMapel();
        break;
        
    case 'get_presensi_mapel':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->getPresensiKelas($_GET['kelas_id']);
        break;
        
    case 'export_laporan':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->exportLaporan();
        break;
        
    case 'guru_ubah_status_presensi':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->ubahStatusPresensi();
        break;
        
    case 'guru_export_pdf':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->exportPDF();
        break;
        
    case 'guru_export_excel':
        require_once __DIR__ . '/app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->exportExcel();
        break;
        
    case 'admin_ubah_status_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->ubahStatusPresensiSekolah();
        break;
        
    case 'admin_ubah_status_presensi_mapel':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->ubahStatusPresensiKelas();
        break;
        
    case 'admin_kesiswaan_ubah_status_presensi_sekolah':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->ubahStatusPresensiSekolah();
        break;
        
    case 'admin_kesiswaan_ubah_status_presensi_mapel':
        require_once __DIR__ . '/app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->ubahStatusPresensiKelas();
        break;

    // Default fallback
    default:
        require_once __DIR__ . '/app/views/auth/login.php';
        break;
}
?>
