<?php
// app/models/PresensiSekolahSesiModel.php
// Model untuk mengelola sesi presensi sekolah (auto dan manual override oleh admin)
require_once 'Database.php';
require_once 'PresensiModel.php';

class PresensiSekolahSesiModel {
    private $db;
    private $presensiModel;

    public function __construct() {
        $this->db = new Database();
        $this->presensiModel = new PresensiModel();
    }

    // Ambil sesi sekolah yang masih open (jika ada)
    public function getActiveSession() {
        $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE status = "open" ORDER BY waktu_buka DESC LIMIT 1');
        return $this->db->single();
    }

    // Buat sesi baru (manual atau auto)
    public function createSession($waktu_buka, $waktu_tutup, $created_by = null, $note = null) {
        $this->db->query('INSERT INTO presensi_sekolah_sesi (waktu_buka, waktu_tutup, status, created_by, note) VALUES (:wb, :wt, "open", :created_by, :note)');
        $this->db->bind(':wb', $waktu_buka);
        $this->db->bind(':wt', $waktu_tutup);
        $this->db->bind(':created_by', $created_by);
        $this->db->bind(':note', $note);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Tutup sesi (manual oleh admin atau otomatis ketika waktu lewat)
    public function closeSession($id) {
        $this->db->query('UPDATE presensi_sekolah_sesi SET status = "closed" WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Perpanjang sesi (update waktu_tutup)
    public function extendSession($id, $new_waktu_tutup) {
        $this->db->query('UPDATE presensi_sekolah_sesi SET waktu_tutup = :wt WHERE id = :id');
        $this->db->bind(':wt', $new_waktu_tutup);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Tutup semua sesi yang sudah lewat waktunya (dipanggil sebelum cek aktif)
    public function closeExpiredSessions() {
        // Get all sessions that need to be closed
        $this->db->query('SELECT id FROM presensi_sekolah_sesi WHERE status = "open" AND waktu_tutup <= NOW()');
        $expiredSessions = $this->db->resultSet();
        
        // Mark absent students as alpha for each expired session
        foreach ($expiredSessions as $session) {
            $this->presensiModel->markAbsentStudentsAsAlphaSekolah($session->id);
        }
        
        // Close the expired sessions
        $this->db->query('UPDATE presensi_sekolah_sesi SET status = "closed" WHERE status = "open" AND waktu_tutup <= NOW()');
        return $this->db->execute();
    }

    // Ambil sesi berdasarkan tanggal (opsional)
    public function getSessions($date = null) {
        if ($date) {
            $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE DATE(waktu_buka) = :d ORDER BY waktu_buka DESC');
            $this->db->bind(':d', $date);
        } else {
            $this->db->query('SELECT * FROM presensi_sekolah_sesi ORDER BY waktu_buka DESC');
        }
        return $this->db->resultSet();
    }

    // Ambil satu sesi berdasarkan tanggal (untuk laporan)
    public function getSesiByTanggal($tanggal) {
        $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE DATE(waktu_buka) = :tanggal ORDER BY waktu_buka DESC LIMIT 1');
        $this->db->bind(':tanggal', $tanggal);
        return $this->db->single();
    }

    /**
     * Auto-create sesi presensi sekolah setiap jam 7 pagi (Senin-Jumat) WIB
     * Akan membuat sesi otomatis jika:
     * - Hari ini adalah hari kerja (Senin-Jumat)
     * - Sudah melewati jam 7 pagi WIB
     * - Belum ada sesi yang dibuat untuk hari ini
     */
    public function autoCreateDailySesi() {
        // Set timezone ke WIB (Asia/Jakarta)
        date_default_timezone_set('Asia/Jakarta');
        
        // Cek apakah hari ini adalah hari kerja (1=Senin, 5=Jumat, 0=Minggu, 6=Sabtu)
        $hariIni = date('w'); // 0=Minggu, 1=Senin, ..., 6=Sabtu
        
        // Jika bukan hari kerja (Sabtu-Minggu), skip
        if ($hariIni == 0 || $hariIni == 6) {
            return false;
        }
        
        // Cek apakah sudah melewati jam 07:00
        $waktuSekarang = date('H:i');
        if ($waktuSekarang < '07:00') {
            return false; // Belum jam 07:00
        }
        
        // Cek apakah sudah ada sesi untuk hari ini
        $tanggalHariIni = date('Y-m-d');
        $this->db->query('SELECT * FROM presensi_sekolah_sesi WHERE DATE(waktu_buka) = :tanggal');
        $this->db->bind(':tanggal', $tanggalHariIni);
        $sesiHariIni = $this->db->resultSet();
        
        // Jika sudah ada sesi hari ini, skip
        if (!empty($sesiHariIni)) {
            return false;
        }
        
        // Buat sesi otomatis
        // Waktu buka: Hari ini jam 07:00:00 (untuk testing)
        // Waktu tutup: Hari ini jam 23:59:59 (bisa disesuaikan)
        $waktuBuka = $tanggalHariIni . ' 07:00:00';
        $waktuTutup = $tanggalHariIni . ' 23:59:59';
        $note = 'Sesi otomatis - ' . $this->getNamaHari($hariIni);
        
        // Created by: NULL (sistem otomatis)
        $sesiId = $this->createSession($waktuBuka, $waktuTutup, null, $note);
        
        return $sesiId ? true : false;
    }
    
    /**
     * Helper: Dapatkan nama hari dalam Bahasa Indonesia
     */
    private function getNamaHari($dayNumber) {
        $namaHari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];
        return $namaHari[$dayNumber] ?? '';
    }
}

?>
