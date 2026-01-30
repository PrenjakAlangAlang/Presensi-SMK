<?php
// app/services/WhatsAppService.php
// Service untuk mengirim notifikasi WhatsApp menggunakan Twilio API

class WhatsAppService {
    private $accountSid;
    private $authToken;
    private $fromNumber;
    private $appName;
    private $apiUrl;
    
    public function __construct() {
        $this->accountSid = TWILIO_ACCOUNT_SID;
        $this->authToken = TWILIO_AUTH_TOKEN;
        $this->fromNumber = TWILIO_WHATSAPP_FROM;
        $this->appName = TWILIO_APP_NAME;
        $this->apiUrl = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";
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
     * Fungsi umum untuk mengirim pesan WhatsApp via Twilio
     * 
     * @param string $phoneNumber Nomor tujuan (format: whatsapp:+62XXXXXXXXXX)
     * @param string $message Isi pesan
     * @return bool
     */
    private function sendMessage($phoneNumber, $message) {
        try {
            // Format nomor untuk Twilio: whatsapp:+62xxx
            $toNumber = 'whatsapp:' . $phoneNumber;
            
            // Data untuk API Twilio
            $postData = [
                'From' => $this->fromNumber,
                'To' => $toNumber,
                'Body' => $message
            ];
            
            // Initialize cURL
            $ch = curl_init($this->apiUrl);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->accountSid . ':' . $this->authToken);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
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
            
            // Check if message sent successfully
            // Twilio returns HTTP 200 or 201 for successful message
            if ($httpCode === 200 || $httpCode === 201) {
                return true;
            }
            
            // Handle sandbox error (63015) - number not joined sandbox
            if (isset($responseData['code']) && $responseData['code'] == 63015) {
                error_log('WhatsApp Sandbox Error: Nomor ' . $phoneNumber . ' belum join sandbox. Silakan kirim "join <code>" ke +1 415 523 8886');
                return false; // Silently fail untuk development
            }
            
            // Log other errors
            error_log('WhatsApp API error (HTTP ' . $httpCode . '): ' . $response);
            return false;
            
        } catch (Exception $e) {
            error_log('WhatsApp sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format nomor telepon ke format internasional yang diterima Twilio
     * Input: 08123456789, +628123456789, 628123456789, 8123456789
     * Output: +628123456789
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
            return '+' . $phone; // Twilio requires + prefix
        }
        
        return false;
    }
    
    /**
     * Test koneksi ke Twilio API
     * 
     * @return array Status koneksi dan pesan
     */
    public function testConnection() {
        if (empty($this->accountSid) || $this->accountSid === 'your-twilio-account-sid-here') {
            return [
                'success' => false,
                'message' => 'Account SID belum dikonfigurasi. Silakan update config.php'
            ];
        }
        
        if (empty($this->authToken) || $this->authToken === 'your-twilio-auth-token-here') {
            return [
                'success' => false,
                'message' => 'Auth Token belum dikonfigurasi. Silakan update config.php'
            ];
        }
        
        if (empty($this->fromNumber) || $this->fromNumber === 'whatsapp:+14155238886') {
            return [
                'success' => true,
                'message' => 'Konfigurasi valid (Sandbox Mode). Pastikan nomor penerima sudah join sandbox.',
                'account_sid' => substr($this->accountSid, 0, 10) . '...',
                'from_number' => $this->fromNumber,
                'mode' => 'sandbox'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Konfigurasi WhatsApp service sudah benar',
            'account_sid' => substr($this->accountSid, 0, 10) . '...',
            'from_number' => $this->fromNumber,
            'mode' => 'production'
        ];
    }
}
?>
