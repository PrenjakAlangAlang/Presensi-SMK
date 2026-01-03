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
}

?>
