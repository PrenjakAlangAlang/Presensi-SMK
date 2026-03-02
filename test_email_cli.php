<?php
// test_email_cli.php
// CLI version untuk test email

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/services/EmailService.php';
require_once __DIR__ . '/app/models/Database.php';
require_once __DIR__ . '/app/models/BukuIndukModel.php';

echo "=================================================\n";
echo "TEST EMAIL SERVICE - SISTEM PRESENSI SMK\n";
echo "=================================================\n\n";

// 1. Cek Konfigurasi Email
echo "1. KONFIGURASI EMAIL\n";
echo "   Host: " . EMAIL_HOST . "\n";
echo "   Port: " . EMAIL_PORT . "\n";
echo "   Encryption: " . EMAIL_ENCRYPTION . "\n";
echo "   Username: " . EMAIL_USERNAME . "\n";
echo "   From: " . EMAIL_FROM . " (" . EMAIL_FROM_NAME . ")\n";
echo "   Password: " . (empty(EMAIL_PASSWORD) ? "TIDAK ADA" : str_repeat("*", 19) . " (tersedia)") . "\n\n";

// 2. Cek Database
echo "2. CEK DATA EMAIL ORANG TUA DI DATABASE\n";
try {
    $db = new Database();
    $db->query('SELECT u.nama, bi.email_ortu, bi.no_telp_ortu 
                FROM users u 
                LEFT JOIN buku_induk bi ON u.id = bi.user_id 
                WHERE u.role = "siswa" 
                LIMIT 10');
    $students = $db->resultSet();
    
    if (empty($students)) {
        echo "   ⚠️  Tidak ada data siswa di database\n\n";
    } else {
        $siswaWithEmail = 0;
        foreach ($students as $student) {
            $emailStatus = empty($student->email_ortu) ? "❌ Kosong" : "✅ Ada";
            $phoneStatus = empty($student->no_telp_ortu) ? "❌" : "✅";
            
            if (!empty($student->email_ortu)) {
                $siswaWithEmail++;
            }
            
            echo "   - " . $student->nama . "\n";
            echo "     Email: " . ($student->email_ortu ?: '-') . " [$emailStatus]\n";
            echo "     Telp:  " . ($student->no_telp_ortu ?: '-') . " [$phoneStatus]\n";
        }
        
        echo "\n   Total: $siswaWithEmail dari " . count($students) . " siswa punya email orang tua\n\n";
        
        if ($siswaWithEmail == 0) {
            echo "   ❌ MASALAH: Tidak ada email_ortu di buku_induk!\n";
            echo "   Silakan tambahkan email orang tua via menu Buku Induk.\n\n";
            exit;
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error database: " . $e->getMessage() . "\n\n";
    exit;
}

// 3. Test Kirim Email
echo "3. TEST PENGIRIMAN EMAIL\n";
try {
    $db = new Database();
    $db->query('SELECT u.id, u.nama, bi.email_ortu 
                FROM users u 
                INNER JOIN buku_induk bi ON u.id = bi.user_id 
                WHERE u.role = "siswa" AND bi.email_ortu IS NOT NULL AND bi.email_ortu != ""
                LIMIT 1');
    $testStudent = $db->single();
    
    if ($testStudent) {
        echo "   Siswa: " . $testStudent->nama . "\n";
        echo "   Email tujuan: " . $testStudent->email_ortu . "\n";
        echo "   Mengirim email test...\n\n";
        
        try {
            $emailService = new EmailService();
            
            echo "   Connecting to SMTP server...\n";
            $result = $emailService->sendAlphaNotificationSekolah(
                $testStudent->email_ortu,
                $testStudent->nama,
                date('Y-m-d'),
                date('Y-m-d H:i:s')
            );
            
            if ($result) {
                echo "\n   ✅ EMAIL BERHASIL DIKIRIM!\n";
                echo "   Cek inbox/spam di: " . $testStudent->email_ortu . "\n\n";
            } else {
                echo "\n   ❌ Email gagal dikirim (return false)\n";
                echo "   Kemungkinan masalah:\n";
                echo "   - App Password Gmail salah/expired\n";
                echo "   - Koneksi SMTP terblokir\n";
                echo "   - Email tujuan tidak valid\n\n";
            }
        } catch (Exception $e) {
            echo "\n   ❌ ERROR SAAT MENGIRIM EMAIL:\n";
            echo "   " . $e->getMessage() . "\n\n";
            
            echo "   SOLUSI:\n";
            echo "   1. Generate App Password baru di:\n";
            echo "      https://myaccount.google.com/apppasswords\n";
            echo "   2. Update di config/config.php bagian EMAIL_PASSWORD\n";
            echo "   3. Pastikan 2-Step Verification aktif di Gmail\n\n";
        }
    } else {
        echo "   ⚠️  Tidak ada siswa dengan email orang tua\n";
        echo "   Tambahkan email orang tua di menu Buku Induk\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

echo "=================================================\n";
echo "TEST SELESAI\n";
echo "=================================================\n";
?>
