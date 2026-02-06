<?php
/**
 * Debug Fonnte API
 * File untuk debugging koneksi dan response dari Fonnte API
 */

require_once 'config/config.php';

// Test langsung ke API Fonnte
$token = FONNTE_TOKEN;
$apiUrl = 'https://api.fonnte.com/send';
$testPhone = '0881024291419'; // Nomor test Anda

// Format nomor ke 62xxx
$phone = preg_replace('/[^0-9]/', '', $testPhone);
if (substr($phone, 0, 1) === '0') {
    $phone = '62' . substr($phone, 1);
}

echo "<h1>Debug Fonnte API</h1>";
echo "<pre>";

echo "=== KONFIGURASI ===\n";
echo "Token: " . substr($token, 0, 15) . "...\n";
echo "API URL: " . $apiUrl . "\n";
echo "Test Phone (input): " . $testPhone . "\n";
echo "Test Phone (formatted): " . $phone . "\n\n";

// Test 1: Kirim pesan sederhana
echo "=== TEST KIRIM PESAN ===\n";

$postData = [
    'target' => $phone,
    'message' => 'Test pesan dari sistem presensi SMK - ' . date('Y-m-d H:i:s'),
    'countryCode' => '62'
];

echo "POST Data:\n";
print_r($postData);
echo "\n";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $token
]);

// Execute
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

echo "HTTP Code: " . $httpCode . "\n";

if ($curlError) {
    echo "cURL Error: " . $curlError . "\n";
}

echo "Raw Response:\n";
echo $response . "\n\n";

$responseData = json_decode($response, true);
echo "Parsed Response:\n";
print_r($responseData);
echo "\n\n";

curl_close($ch);

// Analisis response
echo "=== ANALISIS ===\n";
if (isset($responseData['status'])) {
    if ($responseData['status'] === true) {
        echo "✅ STATUS: SUCCESS\n";
        echo "Pesan berhasil dikirim!\n";
    } else {
        echo "❌ STATUS: FAILED\n";
        echo "Pesan gagal dikirim.\n";
    }
}

if (isset($responseData['reason'])) {
    echo "Reason: " . $responseData['reason'] . "\n";
}

if (isset($responseData['message'])) {
    echo "Message: " . $responseData['message'] . "\n";
}

if (isset($responseData['detail'])) {
    echo "Detail: " . $responseData['detail'] . "\n";
}

echo "\n=== SOLUSI JIKA GAGAL ===\n";
echo "1. Cek token Fonnte sudah benar di file .env\n";
echo "2. Pastikan device WhatsApp connected (status hijau) di dashboard\n";
echo "3. Cek nomor HP format Indonesia (62xxx)\n";
echo "4. Pastikan saldo/quota Fonnte masih ada\n";
echo "5. Cek dashboard Fonnte untuk log error\n";

echo "</pre>";
?>
