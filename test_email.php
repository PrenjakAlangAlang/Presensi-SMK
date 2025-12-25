<?php
// test_email.php - Script untuk test email notifikasi
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/services/EmailService.php';
require_once __DIR__ . '/app/models/BukuIndukModel.php';

echo "=== TEST EMAIL NOTIFIKASI ===\n\n";

// Test 1: Cek autoload SwiftMailer
echo "1. Cek SwiftMailer...\n";
if (class_exists('Swift_Mailer')) {
    echo "   ✓ SwiftMailer berhasil dimuat\n\n";
} else {
    echo "   ✗ SwiftMailer TIDAK ditemukan!\n";
    echo "   Jalankan: composer install\n\n";
    exit;
}

// Test 2: Cek konfigurasi email
echo "2. Konfigurasi Email:\n";
echo "   HOST: " . EMAIL_HOST . "\n";
echo "   PORT: " . EMAIL_PORT . "\n";
echo "   USER: " . EMAIL_USERNAME . "\n";
echo "   FROM: " . EMAIL_FROM . "\n\n";

// Test 3: Cek data siswa dan email orang tua
echo "3. Data Siswa dan Email Orang Tua:\n";
$bukuIndukModel = new BukuIndukModel();
$allRecords = $bukuIndukModel->getAll();

if (empty($allRecords)) {
    echo "   ✗ Tidak ada data di buku induk\n\n";
    exit;
}

foreach ($allRecords as $record) {
    echo "   - User ID: " . $record->user_id . " | Nama: " . $record->nama . " | Email Ortu: " . ($record->email_ortu ?: 'TIDAK ADA') . "\n";
}
echo "\n";

// Test 4: Ambil siswa pertama yang punya email orang tua untuk testing
$testStudent = null;
foreach ($allRecords as $record) {
    if (!empty($record->email_ortu)) {
        $testStudent = $record;
        break;
    }
}

if (!$testStudent) {
    echo "4. ✗ TIDAK ADA siswa dengan email orang tua yang terisi!\n";
    echo "   Silakan isi email_ortu di buku induk siswa terlebih dahulu.\n\n";
    exit;
}

echo "4. Test Kirim Email ke: " . $testStudent->email_ortu . " (Siswa: " . $testStudent->nama . ")\n";

try {
    // Set timeout untuk koneksi SMTP
    ini_set('default_socket_timeout', 15);
    
    echo "   Membuat koneksi ke SMTP...\n";
    $emailService = new EmailService();
    
    // Test kirim notifikasi alpha presensi sekolah
    echo "   Mengirim email...\n";
    $startTime = microtime(true);
    
    $result = $emailService->sendAlphaNotificationSekolah(
        $testStudent->email_ortu,
        $testStudent->nama,
        date('Y-m-d'),
        date('Y-m-d H:i:s')
    );
    
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    if ($result) {
        echo "   ✓ EMAIL BERHASIL DIKIRIM! (dalam {$duration} detik)\n";
        echo "   Cek inbox: " . $testStudent->email_ortu . "\n\n";
    } else {
        echo "   ✗ Email GAGAL dikirim (return false setelah {$duration} detik)\n";
        echo "   Cek error_log untuk detail error\n\n";
    }
    
} catch (Swift_TransportException $e) {
    echo "   ✗ SMTP ERROR: " . $e->getMessage() . "\n\n";
    echo "Kemungkinan Masalah:\n";
    echo "1. Username/Password salah\n";
    echo "2. App Password belum dibuat (Gmail butuh App Password)\n";
    echo "3. Port 587 diblokir firewall\n";
    echo "4. Host SMTP tidak bisa diakses\n\n";
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
    echo "   Type: " . get_class($e) . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

echo "=== TEST SELESAI ===\n";
?>
