<?php
// app/services/EmailService.php
// Service untuk mengirim email notifikasi menggunakan SwiftMailer

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService {
    private $mailer;
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        // Konfigurasi SMTP
        // Remove spaces from password (Gmail App Password sometimes has spaces)
        $password = str_replace(' ', '', EMAIL_PASSWORD);
        
        $transport = (new Swift_SmtpTransport(EMAIL_HOST, EMAIL_PORT, EMAIL_ENCRYPTION))
            ->setUsername(EMAIL_USERNAME)
            ->setPassword($password)
            ->setStreamOptions([
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
        
        $this->mailer = new Swift_Mailer($transport);
        $this->fromEmail = EMAIL_FROM;
        $this->fromName = EMAIL_FROM_NAME;
    }
    
    /**
     * Kirim notifikasi alpha presensi sekolah ke orang tua
     * 
     * @param string $parentEmail Email orang tua
     * @param string $studentName Nama siswa
     * @param string $tanggal Tanggal alpha
     * @param string $waktu Waktu sesi ditutup
     * @return bool
     */
    public function sendAlphaNotificationSekolah($parentEmail, $studentName, $tanggal, $waktu) {
        if (empty($parentEmail)) {
            return false;
        }
        
        $subject = 'Notifikasi Ketidakhadiran - ' . $studentName;
        
        $body = $this->getAlphaSekolahTemplate($studentName, $tanggal, $waktu);
        
        return $this->sendEmail($parentEmail, $subject, $body);
    }
    
    /**
     * Kirim notifikasi alpha presensi kelas ke orang tua
     * 
     * @param string $parentEmail Email orang tua
     * @param string $studentName Nama siswa
     * @param string $namaKelas Nama kelas
     * @param string $tanggal Tanggal alpha
     * @param string $waktu Waktu sesi ditutup
     * @return bool
     */
    public function sendAlphaNotificationKelas($parentEmail, $studentName, $namaKelas, $tanggal, $waktu) {
        if (empty($parentEmail)) {
            return false;
        }
        
        $subject = 'Notifikasi Ketidakhadiran Kelas - ' . $studentName;
        
        $body = $this->getAlphaKelasTemplate($studentName, $namaKelas, $tanggal, $waktu);
        
        return $this->sendEmail($parentEmail, $subject, $body);
    }
    
    /**
     * Fungsi umum untuk mengirim email
     * 
     * @param string $to Email tujuan
     * @param string $subject Subject email
     * @param string $body Body email (HTML)
     * @return bool
     */
    private function sendEmail($to, $subject, $body) {
        try {
            $message = (new Swift_Message($subject))
                ->setFrom([$this->fromEmail => $this->fromName])
                ->setTo([$to])
                ->setBody($body, 'text/html');
            
            $result = $this->mailer->send($message);
            
            return $result > 0;
        } catch (Exception $e) {
            // Log error jika ada
            error_log('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Template email untuk alpha presensi sekolah
     */
    private function getAlphaSekolahTemplate($studentName, $tanggal, $waktu) {
        $formattedDate = date('d F Y', strtotime($tanggal));
        $formattedTime = date('H:i', strtotime($waktu));
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
                .footer { background-color: #6c757d; color: white; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; font-size: 12px; }
                .alert { background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .info-box { background-color: white; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>⚠️ Notifikasi Ketidakhadiran</h2>
                </div>
                <div class="content">
                    <p>Kepada Yth. Orang Tua/Wali,</p>
                    
                    <div class="alert">
                        <strong>Pemberitahuan:</strong> Kami informasikan bahwa siswa atas nama:
                    </div>
                    
                    <div class="info-box">
                        <p><strong>Nama Siswa:</strong> ' . htmlspecialchars($studentName) . '</p>
                        <p><strong>Status:</strong> <span style="color: #dc3545; font-weight: bold;">ALPHA (Tidak Hadir)</span></p>
                        <p><strong>Jenis Presensi:</strong> Presensi Sekolah</p>
                        <p><strong>Tanggal:</strong> ' . $formattedDate . '</p>
                        <p><strong>Waktu Penutupan Sesi:</strong> ' . $formattedTime . ' WIB</p>
                    </div>
                    
                    <p>Siswa tercatat <strong>TIDAK MELAKUKAN PRESENSI</strong> pada sesi presensi sekolah yang telah dibuka.</p>
                    
                    <p>Mohon untuk:</p>
                    <ul>
                        <li>Mengkonfirmasi kehadiran siswa</li>
                        <li>Menghubungi pihak sekolah jika terdapat ketidaksesuaian data</li>
                        <li>Memastikan siswa melakukan presensi tepat waktu</li>
                    </ul>
                    
                    <p>Terima kasih atas perhatian dan kerjasamanya.</p>
                    
                    <p>Hormat kami,<br><strong>Tim Administrasi Sekolah</strong></p>
                </div>
                <div class="footer">
                    <p>Email ini dikirim secara otomatis oleh Sistem Presensi SMK</p>
                    <p>Mohon tidak membalas email ini</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    
    /**
     * Template email untuk alpha presensi kelas
     */
    private function getAlphaKelasTemplate($studentName, $namaKelas, $tanggal, $waktu) {
        $formattedDate = date('d F Y', strtotime($tanggal));
        $formattedTime = date('H:i', strtotime($waktu));
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
                .footer { background-color: #6c757d; color: white; padding: 15px; text-align: center; border-radius: 0 0 5px 5px; font-size: 12px; }
                .alert { background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .info-box { background-color: white; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>⚠️ Notifikasi Ketidakhadiran Kelas</h2>
                </div>
                <div class="content">
                    <p>Kepada Yth. Orang Tua/Wali,</p>
                    
                    <div class="alert">
                        <strong>Pemberitahuan:</strong> Kami informasikan bahwa siswa atas nama:
                    </div>
                    
                    <div class="info-box">
                        <p><strong>Nama Siswa:</strong> ' . htmlspecialchars($studentName) . '</p>
                        <p><strong>Status:</strong> <span style="color: #dc3545; font-weight: bold;">ALPHA (Tidak Hadir)</span></p>
                        <p><strong>Jenis Presensi:</strong> Presensi Kelas</p>
                        <p><strong>Kelas:</strong> ' . htmlspecialchars($namaKelas) . '</p>
                        <p><strong>Tanggal:</strong> ' . $formattedDate . '</p>
                        <p><strong>Waktu Penutupan Sesi:</strong> ' . $formattedTime . ' WIB</p>
                    </div>
                    
                    <p>Siswa tercatat <strong>TIDAK MELAKUKAN PRESENSI</strong> pada sesi kelas yang telah dibuka oleh guru pengampu.</p>
                    
                    <p>Mohon untuk:</p>
                    <ul>
                        <li>Mengkonfirmasi kehadiran siswa di kelas tersebut</li>
                        <li>Menghubungi guru pengampu atau pihak sekolah jika terdapat ketidaksesuaian data</li>
                        <li>Memastikan siswa melakukan presensi kelas tepat waktu</li>
                    </ul>
                    
                    <p>Terima kasih atas perhatian dan kerjasamanya.</p>
                    
                    <p>Hormat kami,<br><strong>Tim Administrasi Sekolah</strong></p>
                </div>
                <div class="footer">
                    <p>Email ini dikirim secara otomatis oleh Sistem Presensi SMK</p>
                    <p>Mohon tidak membalas email ini</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
}
?>
