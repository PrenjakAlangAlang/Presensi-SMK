<?php
// app/services/WhatsAppService.php
// Service untuk mengirim notifikasi WhatsApp menggunakan Fonnte API

class WhatsAppService {
    private $token;
    private $appName;
    private $apiUrl;
    
    public function __construct() {
        $this->token = FONNTE_TOKEN;
        $this->appName = FONNTE_APP_NAME;
        $this->apiUrl = 'https://api.fonnte.com/send';
    }
    
    /**
     * Kirim notifikasi alpha presensi sekolah ke orang tua via WhatsApp
     * 
     * @param string $parentPhone Nomor WhatsApp orang tua (format: 62XXXXXXXXXX)
     * @param string $studentName Nama siswa
     * @param string $tanggal Tanggal alpha
     * @param string $waktu Waktu sesi ditutup
     * @return bool
     */
    public function sendAlphaNotificationSekolah($parentPhone, $studentName, $tanggal, $waktu) {
        if (empty($parentPhone)) {
            return false;
        }
        
        // Format nomor: pastikan menggunakan format internasional (62xxx)
        $phoneNumber = $this->formatPhoneNumber($parentPhone);
        
        if (!$phoneNumber) {
            return false;
        }
        
        $formattedDate = date('d F Y', strtotime($tanggal));
        $formattedTime = date('H:i', strtotime($waktu));
        
        $message = "⚠️ *NOTIFIKASI KETIDAKHADIRAN*\n\n";
        $message .= "Kepada Yth. Orang Tua/Wali,\n\n";
        $message .= "Kami informasikan bahwa:\n\n";
        $message .= "👤 *Nama Siswa:* {$studentName}\n";
        $message .= "📅 *Tanggal:* {$formattedDate}\n";
        $message .= "🕒 *Waktu Penutupan:* {$formattedTime} WIB\n";
        $message .= "📋 *Status:* *ALPHA (Tidak Hadir)*\n";
        $message .= "📍 *Jenis:* Presensi Sekolah\n\n";
        $message .= "Siswa tercatat *TIDAK MELAKUKAN PRESENSI* pada sesi presensi sekolah yang telah dibuka.\n\n";
        $message .= "Mohon untuk:\n";
        $message .= "• Mengkonfirmasi kehadiran siswa\n";
        $message .= "• Menghubungi pihak sekolah jika ada ketidaksesuaian\n";
        $message .= "• Memastikan siswa presensi tepat waktu\n\n";
        $message .= "Terima kasih atas perhatian dan kerjasamanya.\n\n";
        $message .= "---\n";
        $message .= "🏫 *{$this->appName}*\n";
        $message .= "_Pesan otomatis, mohon tidak membalas_";
        
        return $this->sendMessage($phoneNumber, $message);
    }
    
    /**
     * Kirim notifikasi alpha presensi kelas ke orang tua via WhatsApp
     * 
     * @param string $parentPhone Nomor WhatsApp orang tua
     * @param string $studentName Nama siswa
     * @param string $namaKelas Nama kelas
     * @param string $tanggal Tanggal alpha
     * @param string $waktu Waktu sesi ditutup
     * @return bool
     */
    public function sendAlphaNotificationKelas($parentPhone, $studentName, $namaKelas, $tanggal, $waktu) {
        if (empty($parentPhone)) {
            return false;
        }
        
        // Format nomor: pastikan menggunakan format internasional (62xxx)
        $phoneNumber = $this->formatPhoneNumber($parentPhone);
        
        if (!$phoneNumber) {
            return false;
        }
        
        $formattedDate = date('d F Y', strtotime($tanggal));
        $formattedTime = date('H:i', strtotime($waktu));
        
        $message = "⚠️ *NOTIFIKASI KETIDAKHADIRAN KELAS*\n\n";
        $message .= "Kepada Yth. Orang Tua/Wali,\n\n";
        $message .= "Kami informasikan bahwa:\n\n";
        $message .= "👤 *Nama Siswa:* {$studentName}\n";
        $message .= "📚 *Kelas:* {$namaKelas}\n";
        $message .= "📅 *Tanggal:* {$formattedDate}\n";
        $message .= "🕒 *Waktu Penutupan:* {$formattedTime} WIB\n";
        $message .= "📋 *Status:* *ALPHA (Tidak Hadir)*\n";
        $message .= "📍 *Jenis:* Presensi Kelas\n\n";
        $message .= "Siswa tercatat *TIDAK MELAKUKAN PRESENSI* pada sesi kelas yang telah dibuka oleh guru pengampu.\n\n";
        $message .= "Mohon untuk:\n";
        $message .= "• Mengkonfirmasi kehadiran siswa\n";
        $message .= "• Menghubungi pihak sekolah jika ada ketidaksesuaian\n";
        $message .= "• Memastikan siswa presensi tepat waktu\n\n";
        $message .= "Terima kasih atas perhatian dan kerjasamanya.\n\n";
        $message .= "---\n";
        $message .= "🏫 *{$this->appName}*\n";
        $message .= "_Pesan otomatis, mohon tidak membalas_";
        
        return $this->sendMessage($phoneNumber, $message);
    }
    
    /**
     * Fungsi umum untuk mengirim pesan WhatsApp via Fonnte
     * 
     * @param string $phoneNumber Nomor tujuan (format: 628XXXXXXXXXX)
     * @param string $message Isi pesan
     * @return bool
     */
    private function sendMessage($phoneNumber, $message) {
        try {
            // Data untuk API Fonnte
            $postData = [
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => '62' // Indonesia
            ];
            
            // Initialize cURL
            $ch = curl_init($this->apiUrl);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $this->token
            ]);
            
            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Check for cURL errors
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                error_log('WhatsApp cURL error: ' . $error);
                return false;
            }
            
            curl_close($ch);
            
            // Parse response
            $responseData = json_decode($response, true);
            
            // Debug: Log full response
            error_log('Fonnte API Response: ' . json_encode($responseData));
            
            // Check if message sent successfully
            // Fonnte returns status true for success
            if (isset($responseData['status']) && $responseData['status'] === true) {
                return true;
            }
            
            // Log errors with full details
            $errorMsg = $responseData['reason'] ?? $responseData['message'] ?? 'Unknown error';
            error_log('Fonnte API error (HTTP ' . $httpCode . '): ' . $errorMsg . ' | Full response: ' . json_encode($responseData));
            return false;
            
        } catch (Exception $e) {
            error_log('WhatsApp sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format nomor telepon ke format yang diterima Fonnte
     * Input: 08123456789, +628123456789, 628123456789, 8123456789
     * Output: 628123456789
     * 
     * @param string $phone Nomor telepon
     * @return string|false Nomor terformat atau false jika invalid
     */
    private function formatPhoneNumber($phone) {
        // Remove whitespace and special characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove leading + temporarily for processing
        $phone = ltrim($phone, '+');
        
        // Convert 08xx to 628xx
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Add 62 if not present and starts with 8
        if (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        }
        
        // Validate: must start with 62 and have reasonable length (11-15 digits)
        if (substr($phone, 0, 2) === '62' && strlen($phone) >= 11 && strlen($phone) <= 15) {
            return $phone; // Fonnte uses simple format: 628xxx
        }
        
        return false;
    }
    
    /**
     * Test koneksi ke Fonnte API
     * 
     * @return array Status koneksi dan pesan
     */
    public function testConnection() {
        if (empty($this->token) || $this->token === 'your-fonnte-token-here') {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum dikonfigurasi. Silakan update file .env'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Konfigurasi Fonnte sudah benar',
            'token' => substr($this->token, 0, 10) . '...',
            'api_url' => $this->apiUrl
        ];
    }
}
?>
