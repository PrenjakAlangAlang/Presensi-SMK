<?php

// public/index.php
// Front controller sederhana: load config, cek autentikasi, dan route ke controller sesuai action
require_once '../config/config.php';

// Routing sederhana
$action = $_GET['action'] ?? 'login';

// Check authentication for protected routes
$protectedRoutes = [
    'admin_dashboard', 'admin_users', 'admin_kelas', 'admin_lokasi', 'admin_laporan', 'admin_create_user',
    'admin_update_user', 'admin_export_excel', 'admin_export_pdf',
    'admin_kesiswaan_dashboard', 'admin_kesiswaan_buku_induk', 'admin_kesiswaan_presensi_sekolah',
    'admin_kesiswaan_create_presensi_sekolah', 'admin_kesiswaan_extend_presensi_sekolah', 'admin_kesiswaan_close_presensi_sekolah',
    'admin_kesiswaan_get_presensi_sekolah_status', 'admin_kesiswaan_save_buku_induk', 'admin_kesiswaan_delete_dokumen',
    'admin_kesiswaan_laporan', 'admin_kesiswaan_export_excel', 'admin_kesiswaan_export_pdf',
    'guru_dashboard', 'guru_kelas', 'guru_laporan',
    'siswa_dashboard', 'siswa_presensi', 'siswa_riwayat',
    'siswa_buku_induk', 'siswa_save_buku_induk', 'siswa_delete_dokumen'
];

if (in_array($action, $protectedRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/public/index.php?action=login');
    exit();
}

// Routing utama: panggil controller dan method sesuai action
switch($action) {
    case 'login':
        require_once '../app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        break;
        
    case 'logout':
        require_once '../app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
        
    // Admin Routes
    case 'admin_dashboard':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->dashboard();
        break;
        
    case 'admin_users':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->users();
        break;
        
    case 'admin_kelas':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->kelas();
        break;
        
    case 'admin_lokasi':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->lokasi();
        break;

    case 'admin_presensi_sekolah':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->presensiSekolah();
        break;

    case 'admin_create_presensi_sekolah':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createPresensiSekolah();
        break;

    case 'admin_extend_presensi_sekolah':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->extendPresensiSekolah();
        break;

    case 'admin_close_presensi_sekolah':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->closePresensiSekolah();
        break;

    case 'get_presensi_sekolah_status':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getPresensiSekolahStatus();
        break;
        
    case 'admin_laporan':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->laporan();
        break;

    case 'admin_export_excel':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->exportExcel();
        break;

    case 'admin_export_pdf':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->exportPDF();
        break;

  
        
    case 'admin_create_user':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createUser();
        break;

    case 'admin_create_kelas':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->createKelas();
        break;

    case 'admin_update_kelas':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateKelas();
        break;

    case 'admin_delete_kelas':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->deleteKelas();
        break;

    case 'admin_get_siswa_kelas':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getSiswaDalamKelas();
        break;

    case 'admin_get_siswa_tersedia':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getSiswaTersedia();
        break;

    case 'admin_add_siswa_kelas':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->addSiswaToKelas();
        break;

    case 'admin_remove_siswa_kelas':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->removeSiswaFromKelas();
        break;

    case 'admin_update_user':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateUser();
        break;

    // Admin Kesiswaan Routes
    case 'admin_kesiswaan_dashboard':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->dashboard();
        break;

    case 'admin_kesiswaan_buku_induk':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->bukuInduk();
        break;

    case 'admin_kesiswaan_save_buku_induk':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->saveBukuInduk();
        break;

    case 'admin_kesiswaan_delete_dokumen':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->deleteDokumen();
        break;

    case 'admin_kesiswaan_presensi_sekolah':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->presensiSekolah();
        break;

    case 'admin_kesiswaan_create_presensi_sekolah':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->createPresensiSekolah();
        break;

    case 'admin_kesiswaan_extend_presensi_sekolah':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->extendPresensiSekolah();
        break;

    case 'admin_kesiswaan_close_presensi_sekolah':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->closePresensiSekolah();
        break;

    case 'admin_kesiswaan_get_presensi_sekolah_status':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->getPresensiSekolahStatus();
        break;

    case 'admin_kesiswaan_laporan':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->laporan();
        break;

    case 'admin_kesiswaan_export_excel':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->exportExcel();
        break;

    case 'admin_kesiswaan_export_pdf':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->exportPDF();
        break;

        
    case 'admin_update_lokasi':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateLokasi();
        break;
        
    // Siswa Routes
    case 'siswa_dashboard':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->dashboard();
        break;
        
    case 'siswa_presensi':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->presensi();
        break;
        
    case 'siswa_riwayat':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->riwayat();
        break;

    case 'siswa_buku_induk':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->bukuInduk();
        break;

    case 'siswa_save_buku_induk':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->saveBukuInduk();
        break;
    
    case 'siswa_delete_dokumen':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->deleteDokumen();
        break;
        
    case 'submit_presensi_sekolah':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->submitPresensiSekolah();
        break;
        
    case 'submit_presensi_kelas':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->submitPresensiKelas();
        break;

    // Guru Routes
    case 'guru_dashboard':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->dashboard();
        break;
        
    case 'guru_kelas':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->kelas();
        break;
        
    case 'guru_laporan':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->laporan();
        break;
        
    case 'buka_presensi_kelas':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->bukaPresensiKelas();
        break;
        
    case 'tutup_presensi_kelas':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->tutupPresensiKelas();
        break;
        
    case 'get_presensi_kelas':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->getPresensiKelas($_GET['kelas_id']);
        break;
        
    case 'export_laporan':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->exportLaporan();
        break;
        
    case 'guru_ubah_status_presensi':
        require_once '../app/controllers/GuruController.php';
        $guru = new GuruController();
        $guru->ubahStatusPresensi();
        break;
        
    case 'admin_ubah_status_presensi_sekolah':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->ubahStatusPresensiSekolah();
        break;
        
    case 'admin_kesiswaan_ubah_status_presensi_sekolah':
        require_once '../app/controllers/AdminKesiswaanController.php';
        $ak = new AdminKesiswaanController();
        $ak->ubahStatusPresensiSekolah();
        break;

    // Default fallback
    default:
        require_once '../app/views/auth/login.php';
        break;
}
?>