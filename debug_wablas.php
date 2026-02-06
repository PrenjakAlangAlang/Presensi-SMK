<?php
/**
 * Debug Wablas - Cek koneksi dan test kirim pesan
 */

// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load config
require_once __DIR__ . '/config/config.php';

// Load WhatsAppService
require_once __DIR__ . '/app/services/WhatsAppService.php';

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Debug Wablas</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:5px;border-left:4px solid #007bff;}";
echo ".success{border-color:#28a745;background:#d4edda;}";
echo ".error{border-color:#dc3545;background:#f8d7da;}";
echo ".info{border-color:#17a2b8;background:#d1ecf1;}";
echo "pre{background:#272822;color:#f8f8f2;padding:10px;overflow-x:auto;}";
echo "</style></head><body>";

echo "<h1>🔍 Debug Wablas WhatsApp Service</h1>";

// 1. CEK KONFIGURASI
echo "<div class='box info'>";
echo "<h2>⚙️ 1. Konfigurasi Saat Ini</h2>";
echo "WABLAS_DOMAIN: <strong>" . (defined('WABLAS_DOMAIN') ? WABLAS_DOMAIN : '❌ NOT SET') . "</strong><br>";
echo "WABLAS_TOKEN: <strong>" . (defined('WABLAS_TOKEN') ? substr(WABLAS_TOKEN, 0, 20) . '...' : '❌ NOT SET') . "</strong><br>";
echo "WABLAS_APP_NAME: <strong>" . (defined('WABLAS_APP_NAME') ? WABLAS_APP_NAME : '❌ NOT SET') . "</strong><br>";
echo "</div>";

// 2. TEST KONEKSI
$waService = new WhatsAppService();
$connectionTest = $waService->testConnection();

if ($connectionTest['success']) {
    echo "<div class='box success'>";
    echo "<h2>✅ 2. Test Koneksi: SUKSES</h2>";
    echo "Status: <strong>" . $connectionTest['message'] . "</strong><br>";
    echo "Domain: <strong>" . ($connectionTest['domain'] ?? 'N/A') . "</strong><br>";
    echo "API URL: <strong>" . ($connectionTest['api_url'] ?? 'N/A') . "</strong><br>";
    echo "</div>";
} else {
    echo "<div class='box error'>";
    echo "<h2>❌ 2. Test Koneksi: GAGAL</h2>";
    echo "Error: <strong>" . $connectionTest['message'] . "</strong>";
    echo "</div>";
    exit;
}

// 3. TEST CURL KE API
echo "<div class='box info'>";
echo "<h2>🔌 3. Test Direct API Call</h2>";

$testApiUrl = WABLAS_DOMAIN . '/api/send-message';
$testToken = WABLAS_TOKEN;
$testPhone = '6282143673449'; // Nomor test

$postData = json_encode([
    'phone' => $testPhone,
    'message' => '🧪 Test dari Debug Script - ' . date('H:i:s')
]);

$ch = curl_init($testApiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $testToken,
    'Content-Type: application/json'
]);

echo "📤 <strong>Request:</strong><br>";
echo "URL: <code>$testApiUrl</code><br>";
echo "Phone: <code>$testPhone</code><br>";
echo "Token: <code>" . substr($testToken, 0, 20) . "...</code><br><br>";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "📥 <strong>Response:</strong><br>";
echo "HTTP Code: <strong style='color:" . ($httpCode == 200 ? 'green' : 'red') . ";'>$httpCode</strong><br>";

if ($curlError) {
    echo "<strong style='color:red;'>cURL Error: $curlError</strong><br>";
}

echo "<br><strong>Response Body:</strong><br>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

$responseData = json_decode($response, true);

if ($httpCode == 200 && isset($responseData['status']) && $responseData['status'] === true) {
    echo "<div class='box success'>";
    echo "<h2>✅ Pesan BERHASIL Dikirim!</h2>";
    echo "Message ID: <strong>" . ($responseData['data']['id'] ?? 'N/A') . "</strong><br>";
    echo "Cek WhatsApp di nomor: <strong>$testPhone</strong>";
    echo "</div>";
} else {
    echo "<div class='box error'>";
    echo "<h2>❌ Pesan GAGAL Dikirim</h2>";
    
    // Analisis error
    if ($httpCode == 401 || $httpCode == 403) {
        echo "<strong>❌ Error: Token tidak valid atau tidak memiliki akses</strong><br><br>";
        echo "✅ <strong>Solusi:</strong><br>";
        echo "1. Login ke dashboard Wablas (https://sby.wablas.com)<br>";
        echo "2. Pilih menu Device/Perangkat<br>";
        echo "3. Klik icon Settings/⚙️ pada device Anda<br>";
        echo "4. Copy token yang benar<br>";
        echo "5. Update file .env dengan token baru<br>";
    } elseif ($httpCode == 400) {
        echo "<strong>❌ Error: Bad Request (Format request salah)</strong><br><br>";
        $errorMsg = $responseData['message'] ?? 'Unknown';
        echo "Detail: <code>$errorMsg</code><br><br>";
        
        if (strpos($errorMsg, 'device') !== false || strpos($errorMsg, 'not connected') !== false) {
            echo "✅ <strong>Solusi:</strong><br>";
            echo "1. Login ke dashboard Wablas<br>";
            echo "2. Cek status device - harus 'Connected'<br>";
            echo "3. Jika 'Disconnected', scan ulang QR Code<br>";
        }
    } elseif ($httpCode == 402) {
        echo "<strong>❌ Error: Saldo/Kuota Habis</strong><br><br>";
        echo "✅ <strong>Solusi:</strong><br>";
        echo "1. Login ke dashboard Wablas<br>";
        echo "2. Cek saldo/kuota pesan<br>";
        echo "3. Top up atau beli paket baru<br>";
    } elseif ($httpCode == 0) {
        echo "<strong>❌ Error: Tidak bisa connect ke server Wablas</strong><br><br>";
        echo "✅ <strong>Solusi:</strong><br>";
        echo "1. Cek koneksi internet<br>";
        echo "2. Cek apakah domain benar: " . WABLAS_DOMAIN . "<br>";
        echo "3. Coba domain lain (solo.wablas.com, pati.wablas.com)<br>";
    } else {
        echo "<strong>❌ Error: HTTP $httpCode</strong><br><br>";
        echo "Response: <code>" . ($responseData['message'] ?? 'Unknown error') . "</code>";
    }
    echo "</div>";
}

// 4. CEK FORMAT NOMOR
echo "<div class='box info'>";
echo "<h2>📱 4. Test Format Nomor</h2>";
$testNumbers = ['08123456789', '+6282143673449', '6282143673449', '82143673449'];
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;'>";
echo "<tr><th>Input</th><th>Output</th><th>Valid?</th></tr>";

foreach ($testNumbers as $num) {
    $formatted = preg_replace('/[^0-9+]/', '', $num);
    $formatted = ltrim($formatted, '+');
    
    if (substr($formatted, 0, 1) === '0') {
        $formatted = '62' . substr($formatted, 1);
    }
    if (substr($formatted, 0, 1) === '8') {
        $formatted = '62' . $formatted;
    }
    
    $isValid = (substr($formatted, 0, 2) === '62' && strlen($formatted) >= 11 && strlen($formatted) <= 15);
    
    echo "<tr>";
    echo "<td><code>$num</code></td>";
    echo "<td><code>+$formatted</code></td>";
    echo "<td style='color:" . ($isValid ? 'green' : 'red') . ";'>" . ($isValid ? '✅' : '❌') . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// 5. KESIMPULAN
echo "<div class='box'>";
echo "<h2>📋 5. Kesimpulan & Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Jika HTTP 401/403:</strong> Token salah → Update di file .env</li>";
echo "<li><strong>Jika HTTP 400 (device):</strong> Device disconnect → Scan ulang QR Code di dashboard</li>";
echo "<li><strong>Jika HTTP 402:</strong> Saldo habis → Top up di dashboard</li>";
echo "<li><strong>Jika HTTP 200:</strong> Sukses → Cek WhatsApp Anda</li>";
echo "<li><strong>Jika HTTP 0:</strong> Koneksi error → Cek internet/domain</li>";
echo "</ol>";
echo "<br>";
echo "<strong>🔗 Link Penting:</strong><br>";
echo "Dashboard: <a href='https://sby.wablas.com' target='_blank'>https://sby.wablas.com</a><br>";
echo "Dokumentasi: <a href='https://wablas.com/docs' target='_blank'>https://wablas.com/docs</a><br>";
echo "Support: +62 823-3838-5000 (WhatsApp)<br>";
echo "</div>";

echo "</body></html>";
?>
