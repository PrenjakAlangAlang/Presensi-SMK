<?php
// config/config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'presensi_smk');

// Use the actual folder name. The workspace folder contains a space ('Presensi SMK'), so
// encode the space for URLs. Adjust this if you use a virtual host (recommended).
define('BASE_URL', 'http://localhost/Presensi%20SMK');

// Radius maksimal presensi dalam meter
define('MAX_RADIUS', 100);

// Koordinat default SMK Negeri 7 Yogyakarta
define('DEFAULT_LATITUDE', -7.7956);
define('DEFAULT_LONGITUDE', 110.3695);

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