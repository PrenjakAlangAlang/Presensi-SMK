<?php
/**
 * Test WhatsApp Service dengan Twilio
 * File untuk testing notifikasi WhatsApp sebelum digunakan di production
 * 
 * Cara menggunakan:
 * 1. Pastikan sudah setup config.php dengan Account SID dan Auth Token
 * 2. Join Twilio Sandbox: kirim "join <code>" ke +1 415 523 8886 di WhatsApp
 * 3. Ganti nomor test dengan nomor yang sudah join sandbox
 * 4. Akses file ini via browser: http://localhost/Presensi-SMK/test_whatsapp.php
 */

require_once 'config/config.php';
require_once 'app/services/WhatsAppService.php';

// Style untuk output
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test WhatsApp Service - Twilio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #25D366;
            border-bottom: 3px solid #25D366;
            padding-bottom: 10px;
        }
        h2 {
            color: #333;
            margin-top: 30px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 15px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin: 15px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
            margin: 15px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #ddd;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #25D366;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background: #128C7E;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 Test WhatsApp Service - Twilio</h1>
        
        <?php
        // Initialize WhatsApp service
        $whatsapp = new WhatsAppService();
        
        // Test 1: Check Configuration
        echo '<div class="test-section">';
        echo '<h2>1️⃣ Test Konfigurasi</h2>';
        $configTest = $whatsapp->testConnection();
        
        if ($configTest['success']) {
            echo '<div class="success">';
            echo '<strong>✅ Konfigurasi Valid</strong><br>';
            echo $configTest['message'];
            echo '</div>';
            echo '<pre>';
            print_r($configTest);
            echo '</pre>';
        } else {
            echo '<div class="error">';
            echo '<strong>❌ Konfigurasi Error</strong><br>';
            echo $configTest['message'];
            echo '</div>';
            echo '<div class="info">';
            echo '<strong>Cara memperbaiki:</strong><br>';
            echo '1. Buka file <code>config/config.php</code><br>';
            echo '2. Update nilai <code>TWILIO_ACCOUNT_SID</code> dan <code>TWILIO_AUTH_TOKEN</code><br>';
            echo '3. Reload halaman ini';
            echo '</div>';
        }
        echo '</div>';
        
        // Test 2: Send Test Message (via form)
        if ($configTest['success']) {
            echo '<div class="test-section">';
            echo '<h2>2️⃣ Test Kirim Pesan</h2>';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_phone'])) {
                $testPhone = $_POST['test_phone'];
                $studentName = $_POST['student_name'] ?? 'Budi Santoso';
                
                echo '<div class="info">';
                echo '<strong>⏳ Mengirim pesan ke nomor:</strong> ' . htmlspecialchars($testPhone);
                echo '</div>';
                
                // Test Presensi Sekolah
                $result1 = $whatsapp->sendAlphaNotificationSekolah(
                    $testPhone,
                    $studentName,
                    date('Y-m-d'),
                    date('Y-m-d H:i:s')
                );
                
                if ($result1) {
                    echo '<div class="success">';
                    echo '<strong>✅ Notifikasi Presensi Sekolah berhasil dikirim!</strong><br>';
                    echo 'Cek WhatsApp Anda untuk melihat pesan.';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '<strong>❌ Gagal mengirim notifikasi Presensi Sekolah</strong><br>';
                    echo 'Cek log error di server Anda.';
                    echo '</div>';
                }
                
                echo '<hr style="margin: 20px 0;">';
                
                // Test Presensi Kelas
                $result2 = $whatsapp->sendAlphaNotificationKelas(
                    $testPhone,
                    $studentName,
                    'XII RPL 1',
                    date('Y-m-d'),
                    date('Y-m-d H:i:s')
                );
                
                if ($result2) {
                    echo '<div class="success">';
                    echo '<strong>✅ Notifikasi Presensi Kelas berhasil dikirim!</strong><br>';
                    echo 'Cek WhatsApp Anda untuk melihat pesan.';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '<strong>❌ Gagal mengirim notifikasi Presensi Kelas</strong><br>';
                    echo 'Cek log error di server Anda.';
                    echo '</div>';
                }
            }
            
            echo '<form method="POST">';
            echo '<div class="warning">';
            echo '<strong>⚠️ Penting:</strong><br>';
            echo 'Jika menggunakan <strong>Sandbox Mode</strong> Twilio, pastikan nomor HP sudah join sandbox:<br>';
            echo '1. Buka WhatsApp di HP Anda<br>';
            echo '2. Chat ke nomor: <code>+1 415 523 8886</code><br>';
            echo '3. Kirim pesan: <code>join &lt;code&gt;</code> (lihat code di Twilio Console)<br>';
            echo '4. Tunggu konfirmasi "You are all set!"<br>';
            echo '5. Baru bisa menerima pesan test';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label>Nomor WhatsApp Test (yang sudah verify):</label>';
            echo '<input type="text" name="test_phone" placeholder="08123456789 atau 628123456789" required>';
            echo '<small style="color: #666;">Format: 08xxx, 628xxx, atau +628xxx</small>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label>Nama Siswa (opsional):</label>';
            echo '<input type="text" name="student_name" placeholder="Budi Santoso" value="Budi Santoso">';
            echo '</div>';
            
            echo '<button type="submit">📤 Kirim Test WhatsApp</button>';
            echo '</form>';
            echo '</div>';
        }
        
        // Instructions
        echo '<div class="test-section">';
        echo '<h2>📖 Petunjuk Lengkap</h2>';
        echo '<p>Untuk panduan lengkap setup Twilio, baca file: <code>SETUP_WHATSAPP_TWILIO.md</code></p>';
        echo '<div class="info">';
        echo '<strong>Langkah-langkah:</strong><br>';
        echo '1. Registrasi akun di <a href="https://www.twilio.com/try-twilio" target="_blank">Twilio.com</a><br>';
        echo '2. Copy Account SID dan Auth Token dari console<br>';
        echo '3. Join Twilio Sandbox: kirim join code ke +1 415 523 8886<br>';
        echo '4. Update config.php dengan kredensial Twilio<br>';
        echo '5. Test kirim pesan menggunakan form di atas<br>';
        echo '6. Jika berhasil, sistem sudah siap digunakan!';
        echo '</div>';
        echo '</div>';
        
        // Technical Info
        echo '<div class="test-section">';
        echo '<h2>🔧 Informasi Teknis</h2>';
        echo '<ul>';
        echo '<li><strong>API Provider:</strong> Twilio</li>';
        echo '<li><strong>API Endpoint:</strong> https://api.twilio.com/2010-04-01/Accounts/{AccountSid}/Messages.json</li>';
        echo '<li><strong>Method:</strong> POST</li>';
        echo '<li><strong>Auth:</strong> Basic Authentication (AccountSid:AuthToken)</li>';
        echo '<li><strong>Sandbox Number:</strong> +1 415 523 8886</li>';
        echo '<li><strong>PHP Extension:</strong> cURL (required)</li>';
        echo '</ul>';
        echo '</div>';
        ?>
        
    </div>
</body>
</html>
