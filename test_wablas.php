<?php
/**
 * Test file untuk Wablas WhatsApp Service
 * Akses via: http://localhost/Presensi-SMK/test_wablas.php
 */

// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load config
require_once __DIR__ . '/config/config.php';

// Load WhatsAppService
require_once __DIR__ . '/app/services/WhatsAppService.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Wablas WhatsApp Service</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #25D366;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        h1::before {
            content: "📱";
            margin-right: 10px;
            font-size: 1.2em;
        }
        .section {
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #25D366;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .config-label {
            font-weight: bold;
            color: #555;
        }
        .config-value {
            color: #007bff;
            font-family: 'Courier New', monospace;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"], input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="tel"]:focus {
            outline: none;
            border-color: #25D366;
        }
        button {
            background: #25D366;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }
        button:hover {
            background: #128C7E;
        }
        pre {
            background: #272822;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 13px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Wablas WhatsApp Service</h1>
        
        <?php
        // Initialize WhatsAppService
        $waService = new WhatsAppService();
        
        // Test 1: Connection Test
        echo '<div class="section">';
        echo '<h2>🔌 Test Koneksi Wablas</h2>';
        $connectionTest = $waService->testConnection();
        
        if ($connectionTest['success']) {
            echo '<div class="success">';
            echo '<strong>✅ ' . $connectionTest['message'] . '</strong><br><br>';
            echo '<div class="config-item">';
            echo '<span class="config-label">Domain:</span>';
            echo '<span class="config-value">' . ($connectionTest['domain'] ?? 'N/A') . '</span>';
            echo '</div>';
            echo '<div class="config-item">';
            echo '<span class="config-label">Token:</span>';
            echo '<span class="config-value">' . ($connectionTest['token'] ?? 'N/A') . '</span>';
            echo '</div>';
            echo '<div class="config-item">';
            echo '<span class="config-label">API URL:</span>';
            echo '<span class="config-value">' . ($connectionTest['api_url'] ?? 'N/A') . '</span>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<strong>❌ ' . $connectionTest['message'] . '</strong><br><br>';
            echo 'Silakan update konfigurasi di file <code>.env</code>';
            echo '</div>';
        }
        echo '</div>';
        
        // Test 2: Configuration Check
        echo '<div class="section">';
        echo '<h2>⚙️ Konfigurasi Sistem</h2>';
        echo '<div class="config-item">';
        echo '<span class="config-label">WABLAS_DOMAIN:</span>';
        echo '<span class="config-value">' . (defined('WABLAS_DOMAIN') ? WABLAS_DOMAIN : '❌ Not set') . '</span>';
        echo '</div>';
        echo '<div class="config-item">';
        echo '<span class="config-label">WABLAS_TOKEN:</span>';
        $token = defined('WABLAS_TOKEN') ? WABLAS_TOKEN : '';
        if (empty($token) || $token === 'your-wablas-token-here') {
            echo '<span class="config-value" style="color: red;">❌ Belum dikonfigurasi</span>';
        } else {
            echo '<span class="config-value">✅ ' . substr($token, 0, 20) . '...</span>';
        }
        echo '</div>';
        echo '<div class="config-item">';
        echo '<span class="config-label">WABLAS_APP_NAME:</span>';
        echo '<span class="config-value">' . (defined('WABLAS_APP_NAME') ? WABLAS_APP_NAME : 'PresensiSMK') . '</span>';
        echo '</div>';
        echo '</div>';
        
        // Test 3: Send Message Form
        if ($connectionTest['success']) {
            echo '<div class="section">';
            echo '<h2>📤 Test Kirim Pesan</h2>';
            
            // Process form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_send'])) {
                $testPhone = $_POST['phone_number'] ?? '';
                $testName = $_POST['student_name'] ?? 'Budi Santoso';
                
                if (!empty($testPhone)) {
                    echo '<div class="info">';
                    echo '<strong>📨 Mengirim pesan test...</strong><br>';
                    echo 'Nomor: ' . htmlspecialchars($testPhone) . '<br>';
                    echo 'Nama: ' . htmlspecialchars($testName);
                    echo '</div>';
                    
                    $sent = $waService->sendAlphaNotificationSekolah(
                        $testPhone,
                        $testName,
                        date('Y-m-d'),
                        date('H:i:s')
                    );
                    
                    if ($sent) {
                        echo '<div class="success">';
                        echo '<strong>✅ Pesan berhasil dikirim!</strong><br><br>';
                        echo 'Silakan cek WhatsApp di nomor: <strong>' . htmlspecialchars($testPhone) . '</strong><br>';
                        echo '<small>Jika tidak menerima pesan, cek:</small><br>';
                        echo '<small>1. Pastikan nomor WhatsApp aktif</small><br>';
                        echo '<small>2. Pastikan format nomor benar (62xxx)</small><br>';
                        echo '<small>3. Cek status di dashboard Wablas</small>';
                        echo '</div>';
                    } else {
                        echo '<div class="error">';
                        echo '<strong>❌ Gagal mengirim pesan</strong><br><br>';
                        echo 'Kemungkinan penyebab:<br>';
                        echo '• Token tidak valid<br>';
                        echo '• Device WhatsApp tidak connected<br>';
                        echo '• Saldo/kuota habis<br>';
                        echo '• Format nomor salah<br><br>';
                        echo 'Silakan cek error log untuk detail lebih lanjut.';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="error">❌ Nomor WhatsApp tidak boleh kosong!</div>';
                }
                
                echo '<hr style="margin: 20px 0;">';
            }
            
            // Show form
            echo '<form method="POST" action="">';
            echo '<div class="form-group">';
            echo '<label for="phone_number">📱 Nomor WhatsApp Tujuan:</label>';
            echo '<input type="tel" id="phone_number" name="phone_number" placeholder="628123456789 atau 08123456789" required>';
            echo '<small style="color: #666; display: block; margin-top: 5px;">Format: 62xxx atau 08xxx</small>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="student_name">👤 Nama Siswa (untuk test):</label>';
            echo '<input type="text" id="student_name" name="student_name" value="Budi Santoso" required>';
            echo '</div>';
            
            echo '<button type="submit" name="test_send">🚀 Kirim Pesan Test</button>';
            echo '</form>';
            
            echo '</div>';
        }
        
        // Test 4: Phone Number Formatting Test
        echo '<div class="section">';
        echo '<h2>🔢 Test Format Nomor</h2>';
        echo '<p>Testing phone number formatting:</p>';
        
        $testNumbers = [
            '08123456789',
            '+628123456789',
            '628123456789',
            '8123456789',
            '0812-3456-789'
        ];
        
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f8f9fa;">';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Input</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Output (Format Wablas)</th>';
        echo '<th style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">Status</th>';
        echo '</tr>';
        
        foreach ($testNumbers as $number) {
            // Simulate the formatPhoneNumber logic
            $formatted = preg_replace('/[^0-9+]/', '', $number);
            $formatted = ltrim($formatted, '+');
            
            if (substr($formatted, 0, 1) === '0') {
                $formatted = '62' . substr($formatted, 1);
            }
            
            if (substr($formatted, 0, 1) === '8') {
                $formatted = '62' . $formatted;
            }
            
            $isValid = (substr($formatted, 0, 2) === '62' && strlen($formatted) >= 11 && strlen($formatted) <= 15);
            
            echo '<tr>';
            echo '<td style="padding: 10px; border: 1px solid #dee2e6;"><code>' . htmlspecialchars($number) . '</code></td>';
            echo '<td style="padding: 10px; border: 1px solid #dee2e6;"><code>+' . $formatted . '</code></td>';
            echo '<td style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">';
            if ($isValid) {
                echo '<span class="badge badge-success">✅ Valid</span>';
            } else {
                echo '<span class="badge badge-danger">❌ Invalid</span>';
            }
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</div>';
        
        // Instructions
        echo '<div class="section">';
        echo '<h2>📖 Petunjuk Setup</h2>';
        echo '<ol>';
        echo '<li>Daftar akun di <a href="https://wablas.com" target="_blank">wablas.com</a> (klik Register/Daftar)</li>';
        echo '<li>Verifikasi nomor WhatsApp dengan scan QR Code</li>';
        echo '<li>Beli paket/top up saldo</li>';
        echo '<li>Copy token API dari dashboard</li>';
        echo '<li>Update file <code>.env</code> dengan token Anda</li>';
        echo '<li>Refresh halaman ini untuk test ulang</li>';
        echo '</ol>';
        echo '<p><strong>📚 Dokumentasi lengkap:</strong> Lihat file <code>SETUP_WHATSAPP_WABLAS.md</code></p>';
        echo '</div>';
        ?>
        
        <div class="section" style="background: #e7f3ff; border-color: #007bff;">
            <h2>ℹ️ Informasi</h2>
            <p><strong>File ini untuk testing saja.</strong> Untuk production:</p>
            <ul>
                <li>Pastikan file <code>.env</code> sudah dikonfigurasi dengan benar</li>
                <li>Token API harus valid dan aktif</li>
                <li>Device WhatsApp harus dalam status connected</li>
                <li>Pastikan ada saldo/kuota untuk mengirim pesan</li>
            </ul>
            <p><strong>Support:</strong> Jika ada masalah, hubungi support Wablas di +62 823-3838-5000</p>
        </div>
    </div>
</body>
</html>
