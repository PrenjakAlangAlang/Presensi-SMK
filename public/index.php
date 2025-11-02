<?php
// public/index.php
require_once '../config/config.php';

// Routing sederhana
$action = $_GET['action'] ?? 'login';

// Check authentication for protected routes
$protectedRoutes = [
    'admin_dashboard', 'admin_users', 'admin_kelas', 'admin_lokasi', 'admin_laporan', 'admin_create_user',
    'admin_update_user',
    'guru_dashboard', 'guru_kelas', 'guru_laporan',
    'siswa_dashboard', 'siswa_presensi', 'siswa_riwayat', 'siswa_izin',
    'orangtua_dashboard',
    // orangtua management endpoints (admin)
    'admin_get_siswa_orangtua', 'admin_get_siswa_tersedia_orangtua', 'admin_add_siswa_orangtua', 'admin_remove_siswa_orangtua'
];

if (in_array($action, $protectedRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/public/index.php?action=login');
    exit();
}

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
        
    case 'admin_laporan':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->laporan();
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

    case 'admin_get_siswa_orangtua':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getSiswaOrangtua();
        break;

    case 'admin_get_siswa_tersedia_orangtua':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->getSiswaTersediaOrangtua();
        break;

    case 'admin_add_siswa_orangtua':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->addSiswaToOrangtua();
        break;

    case 'admin_remove_siswa_orangtua':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->removeSiswaFromOrangtua();
        break;

    case 'admin_update_user':
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateUser();
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
        
    case 'siswa_izin':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->izin();
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
        
    case 'ajukan_izin':
        require_once '../app/controllers/SiswaController.php';
        $siswa = new SiswaController();
        $siswa->ajukanIzin();
        break;
        // Tambahkan case ini dalam switch statement di public/index.php

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

    // Orang Tua Routes
    case 'orangtua_dashboard':
        require_once '../app/controllers/OrangTuaController.php';
        $ortu = new OrangTuaController();
        $ortu->dashboard();
        break;
        
    case 'get_detail_anak':
        require_once '../app/controllers/OrangTuaController.php';
        $ortu = new OrangTuaController();
        $ortu->getDetailAnak($_GET['siswa_id']);
        break;
        
    case 'get_laporan_mingguan':
        require_once '../app/controllers/OrangTuaController.php';
        $ortu = new OrangTuaController();
        $ortu->getLaporanMingguan($_GET['siswa_id']);
        break;
    // Default fallback
    default:
        require_once '../app/views/auth/login.php';
        break;
}
?>