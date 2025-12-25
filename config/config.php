<?php
// config/config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'presensi_smk');

// Base URL of the app. Ensure this matches your folder or vhost.
// If accessing via http://localhost/Presensi-SMK/, use the dashed folder name:
define('BASE_URL', 'http://localhost/Presensi-SMK');

// Radius maksimal presensi dalam meter
define('MAX_RADIUS', 100);

// Koordinat default SMK Negeri 7 Yogyakarta
define('DEFAULT_LATITUDE', -7.7956);
define('DEFAULT_LONGITUDE', 110.3695);

// Email Configuration untuk SwiftMailer
define('EMAIL_HOST', 'smtp.gmail.com'); // Ganti dengan SMTP server Anda
define('EMAIL_PORT', 465); // 465 untuk SSL (lebih stabil jika 587 diblokir)
define('EMAIL_ENCRYPTION', 'ssl'); // 'ssl' untuk port 465
define('EMAIL_USERNAME', 'kristinluthfi@gmail.com'); // Email pengirim
define('EMAIL_PASSWORD', 'fbuk xfdr aogc qlmg'); // App password atau password email
define('EMAIL_FROM', 'kristinluthfi@gmail.com'); // Email pengirim
define('EMAIL_FROM_NAME', 'Sistem Presensi SMK'); // Nama pengirim

// Fungsi helper
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header('Location: ' . BASE_URL . '/public/' . $url);
    exit();
}

function checkRole($allowedRoles) {
    if (!isLoggedIn() || !in_array($_SESSION['user_role'], $allowedRoles)) {
        redirect('index.php?action=login');
    }
}
?>