<?php
// test_alternative_email.php
// Test email dengan berbagai metode alternatif

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

echo "=================================================\n";
echo "TEST ALTERNATIF PENGIRIMAN EMAIL\n";
echo "=================================================\n\n";

// Test 1: Cek koneksi internet & DNS
echo "1. CEK KONEKSI INTERNET & DNS\n";
$host = 'smtp.gmail.com';
echo "   Resolving $host...\n";
$ip = gethostbyname($host);
if ($ip == $host) {
    echo "   ❌ DNS resolution failed!\n";
    echo "   Tidak bisa resolve smtp.gmail.com\n\n";
} else {
    echo "   ✅ DNS OK: $host = $ip\n\n";
}

// Test 2: Check port connectivity
echo "2. CEK KONEKTIVITAS PORT SMTP\n";
$ports = [
    25 => 'SMTP Standard',
    465 => 'SMTP SSL/TLS',
    587 => 'SMTP STARTTLS',
    2525 => 'Alternative SMTP'
];

foreach ($ports as $port => $desc) {
    echo "   Testing port $port ($desc)...\n";
    $connection = @fsockopen('smtp.gmail.com', $port, $errno, $errstr, 5);
    if ($connection) {
        echo "   ✅ Port $port is OPEN\n";
        fclose($connection);
    } else {
        echo "   ❌ Port $port is BLOCKED (Error: $errstr)\n";
    }
}

echo "\n3. DIAGNOSIS\n";
$blockedPorts = 0;
foreach ([465, 587, 25] as $p) {
    if (!@fsockopen('smtp.gmail.com', $p, $e, $es, 3)) {
        $blockedPorts++;
    }
}

if ($blockedPorts >= 3) {
    echo "   ❌ SEMUA PORT SMTP DIBLOKIR!\n";
    echo "   Kemungkinan: ISP/Router memblokir SMTP keluar\n\n";
    
    echo "=================================================\n";
    echo "SOLUSI: GUNAKAN ALTERNATIF SMTP\n";
    echo "=================================================\n\n";
    
    echo "OPSI 1: MAILTRAP.IO (Recommended untuk Development)\n";
    echo "-----------------------------------------------\n";
    echo "1. Daftar gratis di: https://mailtrap.io/register/signup\n";
    echo "2. Setelah login, buat 'Inbox' baru\n";
    echo "3. Klik 'Show Credentials' untuk SMTP settings\n";
    echo "4. Update config/config.php:\n\n";
    echo "   define('EMAIL_HOST', 'sandbox.smtp.mailtrap.io');\n";
    echo "   define('EMAIL_PORT', 2525); atau 25, 465, 587\n";
    echo "   define('EMAIL_ENCRYPTION', 'tls');\n";
    echo "   define('EMAIL_USERNAME', 'your_username_from_mailtrap');\n";
    echo "   define('EMAIL_PASSWORD', 'your_password_from_mailtrap');\n\n";
    echo "   Keuntungan:\n";
    echo "   - Tidak kena block ISP (pakai port alternatif)\n";
    echo "   - Email masuk ke inbox Mailtrap (bukan email asli)\n";
    echo "   - Bisa preview HTML email\n";
    echo "   - Gratis 500 email/bulan\n\n";
    
    echo "OPSI 2: BREVO (SendInBlue) - Free 300 email/day\n";
    echo "-----------------------------------------------\n";
    echo "1. Daftar di: https://www.brevo.com/\n";
    echo "2. Verifikasi email & domain (opsional)\n";
    echo "3. Buat SMTP key di Settings > SMTP & API\n";
    echo "4. Update config/config.php:\n\n";
    echo "   define('EMAIL_HOST', 'smtp-relay.brevo.com');\n";
    echo "   define('EMAIL_PORT', 587);\n";
    echo "   define('EMAIL_ENCRYPTION', 'tls');\n";
    echo "   define('EMAIL_USERNAME', 'your_brevo_email');\n";
    echo "   define('EMAIL_PASSWORD', 'your_smtp_key');\n\n";
    
    echo "OPSI 3: MAILGUN - Free 5,000 email/month\n";
    echo "-----------------------------------------------\n";
    echo "1. Daftar di: https://www.mailgun.com/\n";
    echo "2. Verifikasi domain (atau pakai sandbox domain)\n";
    echo "3. Get SMTP credentials dari dashboard\n";
    echo "4. Update config/config.php\n\n";
    
    echo "OPSI 4: Gunakan VPN\n";
    echo "-----------------------------------------------\n";
    echo "1. Install VPN (ProtonVPN, Cloudflare WARP - gratis)\n";
    echo "2. Connect ke VPN\n";
    echo "3. Test lagi dengan: php test_smtp_connection.php\n\n";
    
    echo "OPSI 5: Hubungi ISP\n";
    echo "-----------------------------------------------\n";
    echo "Minta ISP membuka port 587 untuk SMTP keluar\n";
    echo "(Biasanya sulit jika pakai ISP rumahan)\n\n";
    
} else {
    echo "   ℹ️  Beberapa port terbuka, tapi koneksi gagal\n";
    echo "   Kemungkinan: App Password Gmail salah\n\n";
    
    echo "SOLUSI: Generate App Password Baru\n";
    echo "1. Buka: https://myaccount.google.com/apppasswords\n";
    echo "2. Login dengan akun kristinluthfi@gmail.com\n";
    echo "3. Buat App Password untuk 'Mail'\n";
    echo "4. Copy 16 karakter password\n";
    echo "5. Update di config/config.php (hilangkan spasi)\n\n";
}

echo "=================================================\n";
echo "REKOMENDASI: GUNAKAN MAILTRAP UNTUK DEVELOPMENT\n";
echo "=================================================\n";
echo "Paling mudah dan pasti jalan!\n";
?>
