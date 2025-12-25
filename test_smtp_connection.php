<?php
// test_smtp_connection.php - Test koneksi SMTP
require_once __DIR__ . '/config/config.php';

echo "=== TEST KONEKSI SMTP ===\n\n";

echo "1. Test DNS Lookup...\n";
$ip = gethostbyname(EMAIL_HOST);
echo "   " . EMAIL_HOST . " -> " . $ip . "\n\n";

echo "2. Test Port Connection...\n";
$timeout = 5;
$fp = @fsockopen(EMAIL_HOST, EMAIL_PORT, $errno, $errstr, $timeout);

if ($fp) {
    echo "   ✓ Port " . EMAIL_PORT . " TERBUKA dan bisa diakses\n";
    fclose($fp);
} else {
    echo "   ✗ Port " . EMAIL_PORT . " TIDAK bisa diakses!\n";
    echo "   Error: $errstr ($errno)\n";
    echo "\n   Kemungkinan:\n";
    echo "   - Firewall memblokir port 587\n";
    echo "   - Antivirus memblokir koneksi SMTP\n";
    echo "   - ISP memblokir port email\n\n";
    exit;
}

echo "\n3. Test SwiftMailer Basic...\n";
require_once __DIR__ . '/vendor/autoload.php';

try {
    echo "   Membuat transport...\n";
    $password = str_replace(' ', '', EMAIL_PASSWORD);
    
    $transport = (new Swift_SmtpTransport(EMAIL_HOST, EMAIL_PORT, EMAIL_ENCRYPTION))
        ->setUsername(EMAIL_USERNAME)
        ->setPassword($password)
        ->setTimeout(10);
    
    echo "   Membuat mailer...\n";
    $mailer = new Swift_Mailer($transport);
    
    echo "   Membuat message...\n";
    $message = (new Swift_Message('Test Email dari Sistem Presensi'))
        ->setFrom([EMAIL_FROM => 'Sistem Presensi SMK'])
        ->setTo(['luthfinurafiq76@gmail.com'])
        ->setBody('Ini adalah test email. Jika Anda menerima email ini, berarti konfigurasi SMTP sudah benar!');
    
    echo "   Mengirim email...\n";
    $result = $mailer->send($message);
    
    if ($result) {
        echo "   ✓ EMAIL BERHASIL DIKIRIM!\n";
        echo "   Cek inbox: luthfinurafiq76@gmail.com\n\n";
    } else {
        echo "   ✗ Email GAGAL dikirim\n\n";
    }
    
} catch (Swift_TransportException $e) {
    echo "   ✗ TRANSPORT ERROR: " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), 'Username and Password not accepted') !== false) {
        echo "   MASALAH: Username atau Password SALAH\n";
        echo "   Solusi:\n";
        echo "   1. Pastikan EMAIL_USERNAME = kristinluthfi@gmail.com (sudah benar)\n";
        echo "   2. Cek App Password di config.php\n";
        echo "   3. Buat App Password baru jika perlu:\n";
        echo "      https://myaccount.google.com/apppasswords\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
    echo "   Type: " . get_class($e) . "\n\n";
}

echo "=== TEST SELESAI ===\n";
?>
