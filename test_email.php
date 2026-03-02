<?php
// test_email.php
// File untuk test pengiriman email notifikasi

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/services/EmailService.php';
require_once __DIR__ . '/app/models/Database.php';
require_once __DIR__ . '/app/models/BukuIndukModel.php';

echo "<h2>Test Email Service - Sistem Presensi SMK</h2>";
echo "<hr>";

// 1. Cek Konfigurasi Email
echo "<h3>1. Konfigurasi Email</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> " . EMAIL_HOST . "</li>";
echo "<li><strong>Port:</strong> " . EMAIL_PORT . "</li>";
echo "<li><strong>Encryption:</strong> " . EMAIL_ENCRYPTION . "</li>";
echo "<li><strong>Username:</strong> " . EMAIL_USERNAME . "</li>";
echo "<li><strong>From:</strong> " . EMAIL_FROM . " (" . EMAIL_FROM_NAME . ")</li>";
echo "<li><strong>Password:</strong> " . (empty(EMAIL_PASSWORD) ? "TIDAK ADA" : str_repeat("*", strlen(EMAIL_PASSWORD)) . " (App Password tersedia)") . "</li>";
echo "</ul>";

// 2. Cek koneksi database dan data email di buku_induk
echo "<h3>2. Data Email Orang Tua di Database</h3>";
try {
    $db = new Database();
    $db->query('SELECT u.nama, bi.email_ortu, bi.no_telp_ortu 
                FROM users u 
                LEFT JOIN buku_induk bi ON u.id = bi.user_id 
                WHERE u.role = "siswa" 
                LIMIT 10');
    $students = $db->resultSet();
    
    if (empty($students)) {
        echo "<p style='color: orange;'>⚠️ Tidak ada data siswa di database</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Nama Siswa</th><th>Email Orang Tua</th><th>No Telp Orang Tua</th><th>Status</th></tr>";
        
        $siswaWithEmail = 0;
        foreach ($students as $student) {
            $emailStatus = empty($student->email_ortu) ? "<span style='color:red;'>❌ Kosong</span>" : "<span style='color:green;'>✅ Ada</span>";
            $phoneStatus = empty($student->no_telp_ortu) ? "❌" : "✅";
            
            if (!empty($student->email_ortu)) {
                $siswaWithEmail++;
            }
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($student->nama) . "</td>";
            echo "<td>" . htmlspecialchars($student->email_ortu ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($student->no_telp_ortu ?? '-') . "</td>";
            echo "<td>Email: $emailStatus | Telp: $phoneStatus</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p><strong>Siswa dengan email orang tua:</strong> $siswaWithEmail dari " . count($students) . " siswa</p>";
        
        if ($siswaWithEmail == 0) {
            echo "<p style='color: red; font-weight: bold;'>❌ MASALAH: Tidak ada data email_ortu di tabel buku_induk!</p>";
            echo "<p>Silakan tambahkan email orang tua melalui menu Buku Induk.</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error database: " . $e->getMessage() . "</p>";
}

// 3. Test Kirim Email
echo "<hr>";
echo "<h3>3. Test Pengiriman Email</h3>";

// Cari siswa pertama yang punya email orang tua
try {
    $db = new Database();
    $db->query('SELECT u.id, u.nama, bi.email_ortu 
                FROM users u 
                INNER JOIN buku_induk bi ON u.id = bi.user_id 
                WHERE u.role = "siswa" AND bi.email_ortu IS NOT NULL AND bi.email_ortu != ""
                LIMIT 1');
    $testStudent = $db->single();
    
    if ($testStudent) {
        echo "<p>Mengirim email test ke orang tua siswa: <strong>" . htmlspecialchars($testStudent->nama) . "</strong></p>";
        echo "<p>Email tujuan: <strong>" . htmlspecialchars($testStudent->email_ortu) . "</strong></p>";
        
        try {
            $emailService = new EmailService();
            $result = $emailService->sendAlphaNotificationSekolah(
                $testStudent->email_ortu,
                $testStudent->nama,
                date('Y-m-d'),
                date('Y-m-d H:i:s')
            );
            
            if ($result) {
                echo "<p style='color: green; font-weight: bold;'>✅ EMAIL BERHASIL DIKIRIM!</p>";
                echo "<p>Cek inbox/spam di email: " . htmlspecialchars($testStudent->email_ortu) . "</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>❌ Email gagal dikirim (return false)</p>";
                echo "<p>Kemungkinan masalah:</p>";
                echo "<ul>";
                echo "<li>App Password Gmail salah atau expired</li>";
                echo "<li>Koneksi ke SMTP Gmail terblokir</li>";
                echo "<li>Email tujuan tidak valid</li>";
                echo "</ul>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red; font-weight: bold;'>❌ Error saat mengirim email:</p>";
            echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
            
            echo "<h4>Solusi:</h4>";
            echo "<ol>";
            echo "<li><strong>Gmail App Password</strong>: Pastikan menggunakan App Password (bukan password Gmail biasa)</li>";
            echo "<li><strong>2-Step Verification</strong>: Aktifkan di akun Gmail Anda</li>";
            echo "<li><strong>Generate App Password</strong>: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>";
            echo "<li><strong>Update config.php</strong>: Masukkan App Password baru (16 karakter tanpa spasi)</li>";
            echo "<li><strong>Less Secure Apps</strong>: Jika masih error, aktifkan akses aplikasi kurang aman (tidak disarankan)</li>";
            echo "</ol>";
        }
    } else {
        echo "<p style='color: orange; font-weight: bold;'>⚠️ Tidak ada siswa dengan email orang tua untuk test</p>";
        echo "<p>Silakan tambahkan email orang tua di menu Buku Induk terlebih dahulu.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>Langkah-langkah Troubleshooting:</h3>";
echo "<ol>";
echo "<li><strong>Pastikan data email_ortu ada di database:</strong> Cek tabel 'buku_induk' kolom 'email_ortu'</li>";
echo "<li><strong>Generate Gmail App Password:</strong> 
    <ul>
        <li>Buka <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>
        <li>Login dengan akun Gmail Anda</li>
        <li>Buat App Password baru untuk 'Mail'</li>
        <li>Copy password 16 karakter</li>
        <li>Update di config/config.php pada EMAIL_PASSWORD</li>
    </ul>
</li>";
echo "<li><strong>Cek koneksi SMTP:</strong> Pastikan server bisa akses smtp.gmail.com port 465</li>";
echo "<li><strong>Test ulang:</strong> Refresh halaman ini untuk test lagi</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='index.php?action=login'>← Kembali ke Aplikasi</a></p>";
?>
